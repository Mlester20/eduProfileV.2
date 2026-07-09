-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 09, 2026 at 03:57 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `profilingdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_profiles`
--

CREATE TABLE `academic_profiles` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `grading_period` enum('1st Quarter','2nd Quarter','3rd Quarter','4th Quarter') NOT NULL,
  `grade` decimal(5,2) NOT NULL,
  `remarks` text DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `session` enum('Morning','Afternoon') NOT NULL,
  `status` enum('Present','Absent','Late','Excused') NOT NULL,
  `remarks` text DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `module` varchar(100) NOT NULL,
  `reference_id` int(10) UNSIGNED DEFAULT NULL,
  `reference_table` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `status` enum('success','failed') NOT NULL DEFAULT 'success',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `role`, `action`, `module`, `reference_id`, `reference_table`, `description`, `ip_address`, `status`, `created_at`) VALUES
(261, 116, 'teacher', 'Deleting a student', 'Students', NULL, NULL, 'Mark Lester Raguindin Deleted a student with ID: ', '::1', 'success', '2026-07-01 14:27:18'),
(262, 116, 'teacher', 'Deleting a student', 'Students', NULL, NULL, 'Mark Lester Raguindin Deleted a student with ID: 2', '::1', 'success', '2026-07-01 14:27:47'),
(263, 116, 'teacher', 'Updating a student', 'Students', NULL, NULL, 'Mark Lester Raguindin Updated a student with ID: 1', '::1', 'success', '2026-07-01 14:45:44'),
(264, 116, 'teacher', 'Deleting a student', 'Students', NULL, NULL, 'Mark Lester Raguindin Deleted a student with ID: 1', '::1', 'success', '2026-07-01 14:45:48'),
(265, 116, 'teacher', 'Adding a new student', 'Students', NULL, NULL, 'Mark Lester Raguindin Added Mark Lester  Raguindin', '::1', 'success', '2026-07-01 15:00:28'),
(266, 116, 'teacher', 'Adding a new student', 'Students', NULL, NULL, 'Mark Lester Raguindin Added Armando Raguindin', '::1', 'success', '2026-07-01 15:03:38'),
(267, 116, 'teacher', 'Added Parent/Guardian', 'Parent/Guardian', NULL, NULL, 'Mark Lester Raguindin Added Parent/Guardian Melba Raguindin', '::1', 'success', '2026-07-02 13:35:32'),
(268, 116, 'teacher', 'Deleted Parent/Guardian', 'Parent/Guardian', NULL, NULL, 'Mark Lester Raguindin Deleted Parent/Guardian', '::1', 'success', '2026-07-02 14:00:27'),
(269, 116, 'teacher', 'Deleted Parent/Guardian', 'Parent/Guardian', NULL, NULL, 'Mark Lester Raguindin Deleted Parent/Guardian', '::1', 'success', '2026-07-02 14:07:16'),
(270, 116, 'teacher', 'Added Parent/Guardian', 'Parent/Guardian', NULL, NULL, 'Mark Lester Raguindin Added Parent/Guardian Melba Raguindin', '::1', 'success', '2026-07-02 14:23:03'),
(271, 116, 'teacher', 'Updated Parent/Guardian', 'Parent/Guardian', NULL, NULL, 'Mark Lester Raguindin Updated Parent/Guardian dasda', '::1', 'success', '2026-07-02 14:26:20'),
(272, 116, 'teacher', 'Deleted Parent/Guardian', 'Parent/Guardian', NULL, NULL, 'Mark Lester Raguindin Deleted Parent/Guardian', '::1', 'success', '2026-07-02 14:26:24'),
(273, 116, 'teacher', 'Updating Student Behavioral', 'Student Behavioral', NULL, NULL, 'Mark Lester Raguindin Added Student Behavioral to 3', '::1', 'success', '2026-07-04 01:37:18'),
(274, 116, 'teacher', 'Deleting Student Behavior', 'Student Behavioral', NULL, '2', 'Mark Lester Raguindin Deleted Student Behavioral ', '::1', 'success', '2026-07-04 01:39:02'),
(275, 116, 'teacher', 'Updating Student Behavioral', 'Student Behavioral', NULL, NULL, 'Mark Lester Raguindin Added Student Behavioral to 3', '::1', 'success', '2026-07-04 01:42:40'),
(276, 116, 'teacher', 'Updating Student Behavioral', 'Student Behavioral', NULL, NULL, 'Mark Lester Raguindin Added Student Behavioral to 4', '::1', 'success', '2026-07-04 01:56:44'),
(277, 116, 'teacher', 'Deleting Student Behavior', 'Student Behavioral', NULL, '4', 'Mark Lester Raguindin Deleted Student Behavioral ', '::1', 'success', '2026-07-04 01:57:07'),
(278, 116, 'teacher', 'Updating Student Behavior', 'Student Behavioral', 3, 'Mark Lester Raguindin Updated a student behavioral 3', '', '::1', 'success', '2026-07-04 01:57:20'),
(279, 116, 'teacher', 'Updating Student Behavior', 'Student Behavioral', 3, 'Mark Lester Raguindin Updated a student behavioral 4', '', '::1', 'success', '2026-07-04 01:57:30'),
(280, 116, 'teacher', 'Deleting Student Behavior', 'Student Behavioral', NULL, '3', 'Mark Lester Raguindin Deleted Student Behavioral ', '::1', 'success', '2026-07-04 06:25:38'),
(281, 116, 'teacher', 'Updating Student Behavioral', 'Student Behavioral', NULL, NULL, 'Mark Lester Raguindin Added Student Behavioral to 3', '::1', 'success', '2026-07-04 06:26:41'),
(282, 116, 'teacher', 'Deleting Student Behavior', 'Student Behavioral', NULL, '5', 'Mark Lester Raguindin Deleted Student Behavioral ', '::1', 'success', '2026-07-04 06:26:44'),
(283, 116, 'teacher', 'Updating Student Behavioral', 'Student Behavioral', NULL, NULL, 'Mark Lester Raguindin Added Student Behavioral to 3', '::1', 'success', '2026-07-04 06:48:35'),
(284, 116, 'teacher', 'Deleting Student Behavior', 'Student Behavioral', NULL, '6', 'Mark Lester Raguindin Deleted Student Behavioral ', '::1', 'success', '2026-07-04 06:48:38'),
(285, 116, 'teacher', 'Updating Student Behavioral', 'Student Behavioral', NULL, NULL, 'Mark Lester Raguindin Added Student Behavioral to 3', '::1', 'success', '2026-07-04 06:52:31'),
(286, 116, 'teacher', 'Creating new Developmental Profile', 'Developmental', NULL, NULL, 'Mark Lester Raguindin Created student developmental for ', '::1', 'success', '2026-07-06 15:00:35'),
(287, 116, 'teacher', 'Deleting Developmental Profile', 'Developmental', 3, NULL, 'Mark Lester Raguindin Deleted student developmental for ', '::1', 'success', '2026-07-06 15:04:22'),
(288, 116, 'teacher', 'Deleting Developmental Profile', 'Developmental', 2, NULL, 'Mark Lester Raguindin Deleted student developmental for ', '::1', 'success', '2026-07-06 15:04:25'),
(289, 116, 'teacher', 'Recording Attendance', 'Attendance', NULL, NULL, 'Mark Lester Raguindin recorded attendance for 2026-07-08 (2 students)', '::1', 'success', '2026-07-08 13:03:11'),
(290, 116, 'teacher', 'Recording Attendance', 'Attendance', NULL, NULL, 'Mark Lester Raguindin recorded attendance for 2026-07-08 (4 students)', '::1', 'success', '2026-07-08 13:16:29'),
(291, 116, 'teacher', 'Recording Attendance', 'Attendance', NULL, NULL, 'Mark Lester Raguindin recorded attendance for 2026-07-08 (2 records)', '::1', 'success', '2026-07-08 13:26:47'),
(292, 116, 'teacher', 'Recording Attendance', 'Attendance', NULL, NULL, 'Mark Lester Raguindin recorded attendance for 2026-07-08 (2 records)', '::1', 'success', '2026-07-08 13:27:03'),
(293, 116, 'teacher', 'Recording Attendance', 'Attendance', NULL, NULL, 'Mark Lester Raguindin recorded attendance for 2026-07-09 (4 records)', '::1', 'success', '2026-07-09 12:44:08'),
(294, 116, 'teacher', 'Recording Student Health', 'Student Health', NULL, NULL, 'Test Teacher Added Health Profile for 3', 'UNKNOWN', 'success', '2026-07-09 13:21:26'),
(295, 116, 'teacher', 'Updating Student Health', 'Student Health', 1, 'Test Teacher Updated a Health Profile record 3', '', 'UNKNOWN', 'success', '2026-07-09 13:21:26'),
(296, 116, 'teacher', 'Deleting Student Health', 'Student Health', NULL, '1', 'Test Teacher Deleted Health Profile record ', 'UNKNOWN', 'success', '2026-07-09 13:21:26'),
(297, 116, 'teacher', 'Recording Student Health', 'Student Health', NULL, NULL, 'Mark Lester Raguindin Added Health Profile for 3', '::1', 'success', '2026-07-09 13:23:12'),
(298, 116, 'teacher', 'Recording Student Health', 'Student Health', NULL, NULL, 'Mark Lester Raguindin Added Health Profile for 3', '::1', 'success', '2026-07-09 13:23:15'),
(299, 116, 'teacher', 'Deleting Student Health', 'Student Health', NULL, '3', 'Mark Lester Raguindin Deleted Health Profile record ', '::1', 'success', '2026-07-09 13:23:26'),
(300, 116, 'teacher', 'Recording Student Health', 'Student Health', NULL, NULL, 'Mark Lester Raguindin Added Health Profile for 4', '::1', 'success', '2026-07-09 13:24:20'),
(301, 116, 'teacher', 'Updating Student Health', 'Student Health', 2, 'Test Teacher Updated a Health Profile record 3', '', 'UNKNOWN', 'success', '2026-07-09 13:33:36'),
(302, 116, 'teacher', 'Deleting Student Health', 'Student Health', NULL, '2', 'Test Teacher Deleted Health Profile record ', 'UNKNOWN', 'success', '2026-07-09 13:33:36'),
(303, 116, 'teacher', 'Deleting Student Health', 'Student Health', NULL, '4', 'Mark Lester Raguindin Deleted Health Profile record ', '::1', 'success', '2026-07-09 13:35:45'),
(306, 116, 'teacher', 'Recording Student Health', 'Student Health', NULL, NULL, 'Mark Lester Raguindin Added Health Profile for 3', '::1', 'success', '2026-07-09 13:40:05'),
(307, 116, 'teacher', 'Deleting Student Health', 'Student Health', NULL, '7', 'Mark Lester Raguindin Deleted Health Profile record ', '::1', 'success', '2026-07-09 13:53:23'),
(308, 116, 'teacher', 'Recording Student Health', 'Student Health', NULL, NULL, 'Mark Lester Raguindin Added Health Profile for 3', '::1', 'success', '2026-07-09 13:56:31'),
(309, 116, 'teacher', 'Updating Student Health', 'Student Health', 8, 'Mark Lester Raguindin Updated a Health Profile record 3', '', '::1', 'success', '2026-07-09 13:56:49'),
(310, 116, 'teacher', 'Deleting Student Health', 'Student Health', NULL, '8', 'Mark Lester Raguindin Deleted Health Profile record ', '::1', 'success', '2026-07-09 13:56:55');

-- --------------------------------------------------------

--
-- Table structure for table `behavioral_profiles`
--

CREATE TABLE `behavioral_profiles` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `observation_date` date NOT NULL,
  `category` varchar(100) NOT NULL,
  `observation` text NOT NULL,
  `intervention` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `behavioral_profiles`
--

INSERT INTO `behavioral_profiles` (`id`, `student_id`, `school_year_id`, `observation_date`, `category`, `observation`, `intervention`, `remarks`, `recorded_by`, `created_at`, `updated_at`) VALUES
(7, 3, 8, '2026-07-04', 'Test Category', 'Test Observation', 'Test Intervention', 'Test', 116, '2026-07-04 06:52:31', '2026-07-04 06:52:31');

-- --------------------------------------------------------

--
-- Table structure for table `developmental_profiles`
--

CREATE TABLE `developmental_profiles` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `domain` enum('Cognitive','Social','Emotional','Physical','Language') NOT NULL,
  `observation` text NOT NULL,
  `recommendation` text DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grade_levels`
--

CREATE TABLE `grade_levels` (
  `id` int(11) NOT NULL,
  `grade_name` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade_levels`
--

INSERT INTO `grade_levels` (`id`, `grade_name`, `created_at`, `updated_at`) VALUES
(5, 'Grade 1', '2026-06-28 14:50:48', '2026-06-28 14:50:48');

-- --------------------------------------------------------

--
-- Table structure for table `health_profiles`
--

CREATE TABLE `health_profiles` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `bmi` decimal(5,2) DEFAULT NULL,
  `bmi_classification` enum('Severely Wasted','Wasted','Normal','Overweight','Obese') DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `vision_screening_result` varchar(100) DEFAULT NULL,
  `hearing_screening_result` varchar(100) DEFAULT NULL,
  `immunization_status` text DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parents_guardians`
--

CREATE TABLE `parents_guardians` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `recorded_by` int(11) DEFAULT NULL,
  `father_name` varchar(50) DEFAULT NULL,
  `father_occupation` varchar(50) DEFAULT NULL,
  `father_contact` varchar(50) DEFAULT NULL,
  `mother_name` varchar(50) DEFAULT NULL,
  `mother_occupation` varchar(50) DEFAULT NULL,
  `mother_contact` varchar(50) DEFAULT NULL,
  `guardian_name` varchar(50) DEFAULT NULL,
  `guardian_relationship` varchar(50) DEFAULT NULL,
  `guardian_contact` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school_year`
--

CREATE TABLE `school_year` (
  `id` int(11) NOT NULL,
  `school_year` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','inactive','archived') DEFAULT 'inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_year`
--

INSERT INTO `school_year` (`id`, `school_year`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES
(8, '2026-2027', '2026-06-08', '2027-04-05', 'active', '2026-05-15 05:40:37', '2026-05-15 05:57:44');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `grade_level_id` int(11) NOT NULL,
  `section_name` varchar(100) NOT NULL,
  `adviser_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `grade_level_id`, `section_name`, `adviser_id`, `created_at`, `updated_at`) VALUES
(2, 5, 'Mahogani', 116, '2026-06-29 14:15:19', '2026-06-29 15:25:30');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `lrn` varchar(20) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `birth_date` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `address` varchar(50) DEFAULT NULL,
  `school_year_id` int(11) NOT NULL,
  `grade_level_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `lrn`, `first_name`, `middle_name`, `last_name`, `suffix`, `birth_date`, `gender`, `address`, `school_year_id`, `grade_level_id`, `section_id`, `recorded_by`, `created_at`, `updated_at`) VALUES
(3, '20242111365', 'Mark Lester ', 'Suguitan', 'Raguindin', '', '2002-12-20', 'Male', 'Rizal, Roxas, Isabela', 8, 5, 2, 116, '2026-07-01 15:00:28', '2026-07-01 15:00:28'),
(4, '20242110364', 'Armando', 'Suguitan', 'Raguindin', 'Jr', '2004-10-14', 'Male', 'Santiago City, Isabela', 8, 5, 2, 116, '2026-07-01 15:03:38', '2026-07-01 15:03:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `profile_picture`, `created_at`, `updated_at`) VALUES
(3, 'Administrative', 'administrative@gmail.com', '$2y$10$CQASCJeXsOYOvWm4kK03i.S1SxUWsdPMv56Qlz04eq0GazfxE8FSi', 'administrative', 'storage/profiles/pfp_3_1782485485.jpg', '2026-05-09', '2026-06-26'),
(4, 'admin', 'admin@gmail.com', '$2y$10$ihbCVd8WOJO17B4BFQgAUORhb1UEYpIFmpd1Q/ShW6n5uNMkLZ7kq', 'admin', 'storage/profiles/pfp_4_1782273895.jpg', '2026-05-09', '2026-06-24'),
(13, 'Registrar', 'registrar@school.edu.ph', '$2y$10$IEz8YAjPkN2ddoQTR6YRUupEwnweJ6YNzsl8opZsKoXrMMFkaJYZG', 'registrar', 'storage/profiles/pfp_13_1779633057.jpg', '2026-05-15', '2026-05-30'),
(116, 'Mark Lester Raguindin', 'teacher.edu.ph@gmail.com', '$2y$10$LZIVGYOkHmFGyOfUS7Nvo.Hfe1kigmfCJM36QvakHqVaLAKq575UC', 'teacher', 'storage/profiles/pfp_116_1782996344.jpg', '2026-06-24', '2026-07-04');

-- --------------------------------------------------------

--
-- Table structure for table `_migrations`
--

CREATE TABLE `_migrations` (
  `filename` varchar(255) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_profiles`
--
ALTER TABLE `academic_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `school_year_id` (`school_year_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_student_date_session` (`student_id`,`attendance_date`,`session`),
  ADD KEY `school_year_id` (`school_year_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `behavioral_profiles`
--
ALTER TABLE `behavioral_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `school_year_id` (`school_year_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `developmental_profiles`
--
ALTER TABLE `developmental_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `school_year_id` (`school_year_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `grade_levels`
--
ALTER TABLE `grade_levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grade_name` (`grade_name`);

--
-- Indexes for table `health_profiles`
--
ALTER TABLE `health_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_student_health_profile` (`student_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `school_year_id` (`school_year_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `parents_guardians`
--
ALTER TABLE `parents_guardians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `school_year`
--
ALTER TABLE `school_year`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `school_year` (`school_year`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grade_level_id` (`grade_level_id`,`section_name`),
  ADD KEY `fk_section_adviser` (`adviser_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lrn` (`lrn`),
  ADD KEY `fk_students_school_year` (`school_year_id`),
  ADD KEY `fk_students_grade_level` (`grade_level_id`),
  ADD KEY `fk_students_section` (`section_id`),
  ADD KEY `fk_students_recorded_by` (`recorded_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `_migrations`
--
ALTER TABLE `_migrations`
  ADD PRIMARY KEY (`filename`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_profiles`
--
ALTER TABLE `academic_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=311;

--
-- AUTO_INCREMENT for table `behavioral_profiles`
--
ALTER TABLE `behavioral_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `developmental_profiles`
--
ALTER TABLE `developmental_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `grade_levels`
--
ALTER TABLE `grade_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `health_profiles`
--
ALTER TABLE `health_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `parents_guardians`
--
ALTER TABLE `parents_guardians`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `school_year`
--
ALTER TABLE `school_year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_profiles`
--
ALTER TABLE `academic_profiles`
  ADD CONSTRAINT `academic_profiles_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `academic_profiles_ibfk_2` FOREIGN KEY (`school_year_id`) REFERENCES `school_year` (`id`),
  ADD CONSTRAINT `academic_profiles_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`school_year_id`) REFERENCES `school_year` (`id`),
  ADD CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `behavioral_profiles`
--
ALTER TABLE `behavioral_profiles`
  ADD CONSTRAINT `behavioral_profiles_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `behavioral_profiles_ibfk_2` FOREIGN KEY (`school_year_id`) REFERENCES `school_year` (`id`),
  ADD CONSTRAINT `behavioral_profiles_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `developmental_profiles`
--
ALTER TABLE `developmental_profiles`
  ADD CONSTRAINT `developmental_profiles_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `developmental_profiles_ibfk_2` FOREIGN KEY (`school_year_id`) REFERENCES `school_year` (`id`),
  ADD CONSTRAINT `developmental_profiles_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `health_profiles`
--
ALTER TABLE `health_profiles`
  ADD CONSTRAINT `health_profiles_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `health_profiles_ibfk_2` FOREIGN KEY (`school_year_id`) REFERENCES `school_year` (`id`),
  ADD CONSTRAINT `health_profiles_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `parents_guardians`
--
ALTER TABLE `parents_guardians`
  ADD CONSTRAINT `parents_guardians_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parents_guardians_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `fk_section_adviser` FOREIGN KEY (`adviser_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_section_grade_level` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_levels` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_students_grade_level` FOREIGN KEY (`grade_level_id`) REFERENCES `grade_levels` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_students_recorded_by` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_students_school_year` FOREIGN KEY (`school_year_id`) REFERENCES `school_year` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_students_section` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
