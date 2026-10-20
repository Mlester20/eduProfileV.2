<?php
session_start();

require_once __DIR__ . '/../core/BaseUrl.php';
require_once __DIR__ . '/../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../models/UpdateProfileModel.php';
require_once __DIR__ . '/../../database/config/config.php';

// Check if user is authenticated
if (!isset($_SESSION['id'])) {
    FlashMessage::setFlash('error', 'Please log in to access this page.');
    header('Location: ' . base_url('index.php'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Resolved from an explicit hidden field rather than the Referer
    // header -- browsers/extensions frequently omit or strip Referer,
    // which left header('Location: ' . $_SERVER['HTTP_REFERER']) pointed
    // at an empty string and the user stranded on this controller's own
    // blank response instead of being redirected back.
    $redirectMap = [
        'teacher' => '../../resources/views/teacher/settings.php',
        'admin' => '../../resources/views/admin/settings.php',
        'administrative' => '../../resources/views/administrative/settings.php',
    ];
    $redirectTo = $redirectMap[$_POST['redirect_role'] ?? ''] ?? '../../index.php';

    Csrf::requireValidOnPost($redirectTo);
    $updateProfileModel = new UpdateProfileModel($con);
    $userId = $_SESSION['id'];

    // ---------------------------------------------------------------
    // Personal Information save (name, email, profile photo) -- does
    // NOT require the current password, since it isn't a security-
    // sensitive change.
    // ---------------------------------------------------------------
    if (isset($_POST['update_personal_info'])) {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $errors = [];

        if (empty($fullName)) {
            $errors[] = 'Full name is required.';
        }

        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        } elseif ($updateProfileModel->emailExists($email, $userId)) {
            $errors[] = 'Email is already in use by another account.';
        }

        if (!empty($errors)) {
            FlashMessage::setFlash('error', implode(' ', $errors));
            header('Location: ' . $redirectTo);
            exit();
        }

        // Handle profile picture upload
        $profilePicturePath = null;
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            $fileType = $_FILES['profile_pic']['type'];
            $fileSize = $_FILES['profile_pic']['size'];

            if (!in_array($fileType, $allowedTypes)) {
                FlashMessage::setFlash('error', 'Invalid file type. Only JPG, PNG, and GIF are allowed.');
                header('Location: ' . $redirectTo);
                exit();
            }

            if ($fileSize > $maxSize) {
                FlashMessage::setFlash('error', 'File size exceeds 2MB limit.');
                header('Location: ' . $redirectTo);
                exit();
            }

            $uploadDir = __DIR__ . '/../../storage/profiles/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileExtension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $fileName = 'pfp_' . $userId . '_' . time() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $uploadPath)) {
                $profilePicturePath = 'storage/profiles/' . $fileName;
            } else {
                FlashMessage::setFlash('error', 'Failed to upload profile picture.');
                header('Location: ' . $redirectTo);
                exit();
            }
        }

        if ($updateProfileModel->updateProfile($userId, $fullName, $email, null)) {
            $_SESSION['full_name'] = $fullName;
            $_SESSION['email'] = $email;

            if ($profilePicturePath) {
                $updateProfileModel->updateProfilePicture($userId, $profilePicturePath);
                $_SESSION['profile_picture'] = $profilePicturePath;
            }

            FlashMessage::setFlash('success', 'Profile updated successfully!');
        } else {
            FlashMessage::setFlash('error', 'Failed to update profile. Please try again.');
        }

        header('Location: ' . $redirectTo);
        exit();
    }

    // ---------------------------------------------------------------
    // Security save (password change) -- current password IS required
    // here, since this is the security-sensitive action.
    // ---------------------------------------------------------------
    if (isset($_POST['update_security'])) {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $errors = [];

        if (empty($currentPassword)) {
            $errors[] = 'Current password is required.';
        } else {
            $userPassword = $updateProfileModel->getUserPassword($userId);
            if (!$updateProfileModel->verifyPassword($currentPassword, $userPassword)) {
                $errors[] = 'Current password is incorrect.';
            }
        }

        if (empty($newPassword)) {
            $errors[] = 'New password is required.';
        } elseif (strlen($newPassword) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            FlashMessage::setFlash('error', implode(' ', $errors));
            header('Location: ' . $redirectTo);
            exit();
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 10]);
        if ($updateProfileModel->updatePassword($userId, $hashedPassword)) {
            FlashMessage::setFlash('success', 'Password updated successfully!');
        } else {
            FlashMessage::setFlash('error', 'Failed to update password. Please try again.');
        }

        header('Location: ' . $redirectTo);
        exit();
    }
}
?>
