<?php
session_start();

require_once __DIR__ . '/../../app/models/AuthModel.php';
require_once __DIR__ . '/../../database/config/config.php';
require_once __DIR__ . '/../core/BaseUrl.php';
require_once __DIR__ . '/../../app/helpers/flashMessage.php';
require_once __DIR__ . '/../../app/helpers/csrf.php';
require_once __DIR__ . '/../core/Model.php';

class AuthController extends Model{
    private AuthModel $authModel;

    public function __construct()
    {
        global $con;

        parent::__construct($con);

        $this->authModel = new AuthModel($this->con);
    }

    public function handle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Csrf::requireValidOnPost('../../../index.php');
            $this->login();
        }
    }

    private function login(): void
    {
        $email    = $_POST['email']    ?? '';
        $password = $_POST['password'] ?? '';

        $row = $this->authModel->getUserByEmail($email);

        if ($row && $this->authModel->verifyPassword($password, $row['password'])) {
            $this->startUserSession($row);
            $this->redirectByRole($row['role']);
        } else {
            FlashMessage::setFlash('error', 'Invalid email or password');
            header('Location: ' . base_url('index.php'));
            exit();
        }
    }

    private function startUserSession(array $row): void
    {
        $_SESSION['id']              = $row['id'];
        $_SESSION['full_name']       = $row['full_name'];
        $_SESSION['email']           = $row['email'];
        $_SESSION['role']            = $row['role'];
        $_SESSION['profile_picture'] = $row['profile_picture'];
    }

    private function redirectByRole(string $role): void
    {
        $routes = [
            'admin'          => base_url('resources/views/admin/dashboard.php'),
            'administrative' => base_url('resources/views/administrative/home.php'),
            'teacher'        => base_url('resources/views/teacher/home.php'),
        ];

        $location = $routes[$role] ?? base_url('index.php');

        header('Location: ' . $location);
        exit();
    }
}

// ------------------------------------------------------------------ //
//  Bootstrap                                                          //
// ------------------------------------------------------------------ //
(new AuthController())->handle();