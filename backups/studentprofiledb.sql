-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2026 at 06:23 PM
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
-- Database: `studentprofiledb`
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

--
-- Dumping data for table `academic_profiles`
--

INSERT INTO `academic_profiles` (`id`, `student_id`, `school_year_id`, `subject_name`, `grading_period`, `grade`, `remarks`, `recorded_by`, `created_at`, `updated_at`) VALUES
(33, 80, 8, 'English', '1st Quarter', 88.00, 'Passed', 116, '2026-07-23 16:21:14', '2026-07-23 16:21:14'),
(34, 80, 8, 'Mathematics', '1st Quarter', 90.00, 'Passed', 116, '2026-07-23 16:21:14', '2026-07-23 16:21:14'),
(35, 81, 8, 'Science', '1st Quarter', 85.00, 'Passed', 117, '2026-07-23 16:21:14', '2026-07-23 16:21:14'),
(36, 81, 8, 'Filipino', '1st Quarter', 82.00, 'Passed', 117, '2026-07-23 16:21:14', '2026-07-23 16:21:14'),
(37, 82, 8, 'Mathematics', '1st Quarter', 65.00, 'Failed', 116, '2026-07-23 16:21:14', '2026-07-23 16:21:14'),
(38, 83, 8, 'English', '1st Quarter', 91.00, 'Passed', 119, '2026-07-23 16:21:14', '2026-07-23 16:21:14');

-- --------------------------------------------------------

--
-- Table structure for table `achievements_profiles`
--

CREATE TABLE `achievements_profiles` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `category` enum('Academic','Sports','Leadership','Arts & Culture','Co-Curricular','Community Service','Other') NOT NULL,
  `level` enum('School','District','Division','Regional','National','International') NOT NULL,
  `description` text DEFAULT NULL,
  `date_received` date NOT NULL,
  `awarding_body` varchar(150) DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `achievements_profiles`
--

INSERT INTO `achievements_profiles` (`id`, `student_id`, `school_year_id`, `title`, `category`, `level`, `description`, `date_received`, `awarding_body`, `recorded_by`, `created_at`, `updated_at`) VALUES
(11, 80, 8, 'Reading Champion', 'Academic', 'School', 'Top reader for the quarter', '2026-07-24', 'San Jose Sur Elementary', 116, '2026-07-23 16:21:14', '2026-07-23 16:21:14'),
(12, 83, 8, 'Perfect Attendance', 'Other', 'School', 'No absences for the quarter', '2026-07-24', 'San Jose Sur Elementary', 119, '2026-07-23 16:21:14', '2026-07-23 16:21:14');

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

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `school_year_id`, `attendance_date`, `session`, `status`, `remarks`, `recorded_by`, `created_at`) VALUES
(54, 80, 8, '2026-07-24', 'Morning', 'Present', NULL, 116, '2026-07-23 16:21:14'),
(55, 80, 8, '2026-07-23', 'Morning', 'Present', NULL, 116, '2026-07-23 16:21:14'),
(56, 81, 8, '2026-07-24', 'Morning', 'Present', NULL, 117, '2026-07-23 16:21:14'),
(57, 81, 8, '2026-07-23', 'Morning', 'Present', NULL, 117, '2026-07-23 16:21:14'),
(58, 82, 8, '2026-07-14', 'Morning', 'Absent', NULL, 116, '2026-07-23 16:21:14'),
(59, 82, 8, '2026-07-13', 'Morning', 'Absent', NULL, 116, '2026-07-23 16:21:14'),
(60, 82, 8, '2026-07-12', 'Morning', 'Absent', NULL, 116, '2026-07-23 16:21:14'),
(61, 82, 8, '2026-07-11', 'Morning', 'Absent', NULL, 116, '2026-07-23 16:21:14'),
(62, 82, 8, '2026-07-10', 'Morning', 'Absent', NULL, 116, '2026-07-23 16:21:14'),
(63, 83, 8, '2026-07-24', 'Morning', 'Present', NULL, 119, '2026-07-23 16:21:14'),
(64, 83, 8, '2026-07-23', 'Morning', 'Present', NULL, 119, '2026-07-23 16:21:14');

-- --------------------------------------------------------

--
-- Table structure for table `at_risk_insights`
--

CREATE TABLE `at_risk_insights` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `insight_text` text NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `at_risk_insights`
--

INSERT INTO `at_risk_insights` (`id`, `student_id`, `school_year_id`, `insight_text`, `generated_at`) VALUES
(2, 23, 8, 'The co-occurrence of academic struggle and disruptive behavior in a Grade 1 learner strongly suggests that foundational skill gaps or classroom adjustments are causing frustration, which in turn leads to disengagement and missed instructional time. A parent-teacher consultation should be scheduled promptly to explore underlying developmental or home factors contributing to these patterns. Simultaneously, the school should implement targeted academic remediation in the struggling subject alongside a positive behavioral reinforcement plan to stabilize the learner\'s early educational experience.', '2026-07-22 17:07:30'),
(6, 53, 8, 'The co-occurrence of early behavioral challenges and attendance gaps strongly suggests that underlying socio-emotional or adjustment issues are impeding this Grade 1 learner\'s foundational academic focus. I recommend convening a prompt case conference with the section teacher, guidance counselor, and parents to identify root causes and implement a unified behavior-reinforcement and academic remediation plan. Immediate, coordinated intervention at this early stage will stabilize the child\'s developmental trajectory before these learning gaps compound.', '2026-07-23 15:30:46'),
(7, 62, 8, 'The convergence of early behavioral disruptions and attendance issues strongly suggests that non-academic barriers are directly hindering this Grade 1 learner\'s foundational performance. I recommend scheduling a collaborative conference with the parent, teacher, and guidance counselor to identify the root causes behind these behavioral and attendance patterns. From this meeting, a coordinated support plan should be established that combines targeted academic remediation in the failing subject with positive behavioral reinforcement strategies.', '2026-07-23 15:47:01');

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
(310, 116, 'teacher', 'Deleting Student Health', 'Student Health', NULL, '8', 'Mark Lester Raguindin Deleted Health Profile record ', '::1', 'success', '2026-07-09 13:56:55'),
(311, 4, 'admin', 'Assigning Section Teacher', 'Section Teacher Assignment', NULL, NULL, 'admin Assigned teacher 116 to section 2 for school year 8', '::1', 'success', '2026-07-09 14:21:25'),
(312, 4, 'admin', 'Adding new section', 'Section', NULL, 'Created Grade Level', 'admin Created new section Pine', '::1', 'success', '2026-07-09 14:27:41'),
(313, 4, 'admin', 'Deleted Section', 'Section', 4, 'adminDeleted Section', '', '::1', 'success', '2026-07-09 14:30:12'),
(314, 4, 'admin', 'Assigning Section Teacher', 'Section Teacher Assignment', NULL, NULL, 'admin Assigned teacher 116 to section 2 for school year 8', '::1', 'success', '2026-07-09 14:30:27'),
(315, 116, 'teacher', 'Adding a new student', 'Students', NULL, NULL, 'Mark Lester Raguindin Added Zeth Jefferson Guerra', '::1', 'success', '2026-07-09 15:10:25'),
(316, 116, 'teacher', 'Recording Attendance', 'Attendance', NULL, NULL, 'Mark Lester Raguindin recorded attendance for 2026-07-09 (3 records)', '::1', 'success', '2026-07-09 15:10:45'),
(317, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 0 student(s) to a new school year', '::1', 'success', '2026-07-09 15:14:47'),
(318, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 0 student(s) to a new school year', '::1', 'success', '2026-07-09 15:15:03'),
(319, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 0 student(s) to a new school year', '::1', 'success', '2026-07-09 15:18:28'),
(320, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 3 student(s) to a new school year', '::1', 'success', '2026-07-09 15:18:43'),
(321, 4, 'admin', 'Assigning Section Teacher', 'Section Teacher Assignment', NULL, NULL, 'admin Assigned teacher 117 to section 2 for school year 8', '::1', 'success', '2026-07-09 16:05:24'),
(322, 4, 'admin', 'Adding new section', 'Section', NULL, 'Created Grade Level', 'admin Created new section Venus', '::1', 'success', '2026-07-09 16:05:41'),
(323, 117, 'teacher', 'Adding a new student', 'Students', NULL, NULL, 'teacher 1 Added Gia  Guerra', '::1', 'success', '2026-07-10 15:33:27'),
(324, 117, 'teacher', 'Added Parent/Guardian', 'Parent/Guardian', NULL, NULL, 'teacher 1 Added Parent/Guardian Rio Del Rosario', '::1', 'success', '2026-07-10 15:35:03'),
(325, 117, 'teacher', 'Updating Student Behavioral', 'Student Behavioral', NULL, NULL, 'teacher 1 Added Student Behavioral to 14', '::1', 'success', '2026-07-10 15:35:38'),
(326, 117, 'teacher', 'Creating new Developmental Profile', 'Developmental', NULL, NULL, 'teacher 1 Created student developmental for ', '::1', 'success', '2026-07-10 15:36:12'),
(327, 116, 'teacher', 'Deleting a student', 'Students', NULL, NULL, 'Mark Lester Raguindin Deleted a student with ID: 13', '::1', 'success', '2026-07-16 08:24:07'),
(328, 116, 'teacher', 'Deleting a student', 'Students', NULL, NULL, 'Mark Lester Raguindin Deleted a student with ID: 12', '::1', 'success', '2026-07-16 08:24:10'),
(329, 116, 'teacher', 'Deleting a student', 'Students', NULL, NULL, 'Mark Lester Raguindin Deleted a student with ID: 11', '::1', 'success', '2026-07-16 08:24:47'),
(330, 116, 'teacher', 'Deleting a student', 'Students', NULL, NULL, 'Mark Lester Raguindin Deleted a student with ID: 10', '::1', 'success', '2026-07-16 08:24:54'),
(331, 116, 'teacher', 'Deleting a student', 'Students', NULL, NULL, 'Mark Lester Raguindin Deleted a student with ID: 4', '::1', 'success', '2026-07-16 08:24:58'),
(332, 116, 'teacher', 'Deleting a student', 'Students', NULL, NULL, 'Mark Lester Raguindin Deleted a student with ID: 3', '::1', 'success', '2026-07-16 08:25:00'),
(333, 117, 'teacher', 'Recording Student Health', 'Student Health', NULL, NULL, 'teacher 1 Added Health Profile for 14', '::1', 'success', '2026-07-18 07:02:43'),
(334, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 1 student(s) to a new school year', '::1', 'success', '2026-07-18 07:13:51'),
(335, 117, 'teacher', 'Adding a new student', 'Students', NULL, NULL, 'teacher 1 Added Mark Lester  Raguindin', '::1', 'success', '2026-07-18 07:26:40'),
(336, 117, 'teacher', 'Creating new Developmental Profile', 'Developmental', NULL, NULL, 'teacher 1 Created student developmental for ', '::1', 'success', '2026-07-18 07:27:10'),
(337, 117, 'teacher', 'Updating Student Behavioral', 'Student Behavioral', NULL, NULL, 'teacher 1 Added Student Behavioral to 18', '::1', 'success', '2026-07-18 07:27:41'),
(338, 117, 'teacher', 'Adding an academic profile', 'Academic Profile', NULL, NULL, 'teacher 1 Added an academic profile for student ID: 18', '::1', 'success', '2026-07-18 07:28:03'),
(339, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 1 student(s) to a new school year', '::1', 'success', '2026-07-18 07:28:27'),
(340, 116, 'teacher', 'Adding a new student', 'Students', NULL, NULL, 'Mark Lester Raguindin Added Mark Lester  Raguindin', '::1', 'success', '2026-07-18 12:39:00'),
(341, 116, 'teacher', 'Adding a new student', 'Students', NULL, NULL, 'Mark Lester Raguindin Added Gia  Guerra', '::1', 'success', '2026-07-19 16:08:41'),
(342, 116, 'teacher', 'Adding a new student', 'Students', NULL, NULL, 'Mark Lester Raguindin Added asdfsf dasda', '::1', 'success', '2026-07-22 13:44:39'),
(343, 116, 'teacher', 'Deleting a student', 'Students', NULL, NULL, 'Mark Lester Raguindin Deleted a student with ID: 22', '::1', 'success', '2026-07-22 13:44:43'),
(344, 117, 'teacher', 'Deleting a student', 'Students', NULL, NULL, 'teacher 1 Deleted a student with ID: 19', '::1', 'success', '2026-07-22 13:58:27'),
(345, 116, 'teacher', 'Deleting a student', 'Students', NULL, NULL, 'Mark Lester Raguindin Deleted a student with ID: 21', '::1', 'success', '2026-07-22 13:58:40'),
(346, 116, 'teacher', 'Deleting a student', 'Students', NULL, NULL, 'Mark Lester Raguindin Deleted a student with ID: 20', '::1', 'success', '2026-07-22 13:58:42'),
(347, 116, 'teacher', 'Adding a new student', 'Students', NULL, NULL, 'Mark Lester Raguindin Added Mark Lester  Raguindin', '::1', 'success', '2026-07-22 14:04:03'),
(348, 116, 'teacher', 'Added Parent/Guardian', 'Parent/Guardian', NULL, NULL, 'Mark Lester Raguindin Added Parent/Guardian Melba Raguindin', '::1', 'success', '2026-07-22 14:04:36'),
(349, 116, 'teacher', 'Creating new Developmental Profile', 'Developmental', NULL, NULL, 'Mark Lester Raguindin Created student developmental for ', '::1', 'success', '2026-07-22 14:05:00'),
(350, 116, 'teacher', 'Updating Student Behavioral', 'Student Behavioral', NULL, NULL, 'Mark Lester Raguindin Added Student Behavioral to 23', '::1', 'success', '2026-07-22 14:05:31'),
(351, 116, 'teacher', 'Recording Attendance', 'Attendance', NULL, NULL, 'Mark Lester Raguindin recorded attendance for 2026-07-22 (1 records)', '::1', 'success', '2026-07-22 14:05:40'),
(352, 116, 'teacher', 'Recording Attendance', 'Attendance', NULL, NULL, 'Mark Lester Raguindin recorded attendance for 2026-07-22 (1 records)', '::1', 'success', '2026-07-22 14:05:46'),
(353, 116, 'teacher', 'Adding an academic profile', 'Academic Profile', NULL, NULL, 'Mark Lester Raguindin Added an academic profile for student ID: 23', '::1', 'success', '2026-07-22 14:06:29'),
(354, 116, 'teacher', 'Adding an achievement profile', 'Achievement Profile', NULL, NULL, 'Mark Lester Raguindin Added an achievement profile for student ID: 23', '::1', 'success', '2026-07-22 14:07:15'),
(355, 116, 'teacher', 'Recording Student Health', 'Student Health', NULL, NULL, 'Mark Lester Raguindin Added Health Profile for 23', '::1', 'success', '2026-07-22 14:08:15'),
(356, 3, 'administrative', 'Generated AI insight', 'Students', 23, NULL, 'Administrative generated an AI insight for student ID: 23', '::1', 'success', '2026-07-22 16:24:09'),
(357, 3, 'administrative', 'Generated AI insight', 'Students', 23, NULL, 'Administrative generated an AI insight for student ID: 23', '::1', 'success', '2026-07-22 16:25:06'),
(358, 3, 'administrative', 'Generated AI dashboard summary', 'Students', NULL, NULL, 'Administrative generated an AI dashboard summary for school year ID: 8', '::1', 'success', '2026-07-22 16:36:36'),
(359, 3, 'administrative', 'Generated AI dashboard summary', 'Students', NULL, NULL, 'Administrative generated an AI dashboard summary for school year ID: 8', '::1', 'success', '2026-07-22 16:37:02'),
(360, 3, 'administrative', 'Generated AI dashboard summary', 'Students', NULL, NULL, 'Administrative generated an AI dashboard summary for school year ID: 8', '::1', 'success', '2026-07-22 16:37:17'),
(361, 3, 'administrative', 'Generated AI dashboard summary', 'Students', NULL, NULL, 'Administrative generated an AI dashboard summary for school year ID: 8', '::1', 'success', '2026-07-22 16:38:31'),
(362, 3, 'administrative', 'Generated AI dashboard summary', 'Students', NULL, NULL, 'Test Administrative generated an AI dashboard summary for school year ID: 8', 'UNKNOWN', 'success', '2026-07-22 16:40:19'),
(363, 3, 'administrative', 'Generated AI dashboard summary', 'Students', NULL, NULL, 'Test Administrative generated an AI dashboard summary for school year ID: 8', '::1', 'success', '2026-07-22 16:42:03'),
(364, 3, 'administrative', 'Generated AI dashboard summary', 'Students', NULL, NULL, 'Administrative generated an AI dashboard summary for school year ID: 8', '::1', 'success', '2026-07-22 16:42:39'),
(365, 3, 'administrative', 'Generated AI dashboard summary', 'Students', NULL, NULL, 'Administrative generated an AI dashboard summary for school year ID: 8', '::1', 'success', '2026-07-22 16:46:37'),
(366, 116, 'teacher', 'Generated AI insight', 'Students', 23, NULL, 'Mark Lester Raguindin generated an AI insight for student ID: 23', '::1', 'success', '2026-07-22 17:02:41'),
(367, 116, 'teacher', 'Generated AI insight', 'Students', 23, NULL, 'Mark Lester Raguindin generated an AI insight for student ID: 23', '::1', 'success', '2026-07-22 17:07:30'),
(368, 3, 'administrative', 'Generated AI dashboard summary', 'Students', NULL, NULL, 'Administrative generated an AI dashboard summary for school year ID: 8', '::1', 'success', '2026-07-22 17:08:16'),
(369, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 1 student(s) to a new school year', '::1', 'success', '2026-07-23 12:57:25'),
(370, 116, 'teacher', 'Adding a new student', 'Students', NULL, NULL, 'Mark Lester Raguindin Added Mark Lester  Raguindin', '::1', 'success', '2026-07-23 13:01:45'),
(371, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 1 student(s) to a new school year', '::1', 'success', '2026-07-23 13:02:12'),
(372, 3, 'administrative', 'Generated AI dashboard summary', 'Students', NULL, NULL, 'Administrative generated an AI dashboard summary for school year ID: 14', '::1', 'success', '2026-07-23 13:24:51'),
(373, 116, 'teacher', 'Adding a new student', 'Students', NULL, NULL, 'Mark Lester Raguindin Added Mark Lester  Raguindin', '::1', 'success', '2026-07-23 13:31:02'),
(374, 116, 'teacher', 'Added Parent/Guardian', 'Parent/Guardian', NULL, NULL, 'Mark Lester Raguindin Added Parent/Guardian Melba Raguindin', '::1', 'success', '2026-07-23 13:31:20'),
(375, 116, 'teacher', 'Creating new Developmental Profile', 'Developmental', NULL, NULL, 'Mark Lester Raguindin Created student developmental for ', '::1', 'success', '2026-07-23 13:31:55'),
(376, 116, 'teacher', 'Updating Student Behavioral', 'Student Behavioral', NULL, NULL, 'Mark Lester Raguindin Added Student Behavioral to 35', '::1', 'success', '2026-07-23 13:32:23'),
(377, 116, 'teacher', 'Recording Attendance', 'Attendance', NULL, NULL, 'Mark Lester Raguindin recorded attendance for 2026-07-23 (2 records)', '::1', 'success', '2026-07-23 13:32:33'),
(378, 116, 'teacher', 'Adding an academic profile', 'Academic Profile', NULL, NULL, 'Mark Lester Raguindin Added an academic profile for student ID: 35', '::1', 'success', '2026-07-23 13:32:58'),
(379, 116, 'teacher', 'Adding an achievement profile', 'Achievement Profile', NULL, NULL, 'Mark Lester Raguindin Added an achievement profile for student ID: 35', '::1', 'success', '2026-07-23 13:33:21'),
(380, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 1 student(s) to a new school year', '::1', 'success', '2026-07-23 13:35:10'),
(381, 116, 'teacher', 'Adding a new student', 'Students', NULL, NULL, 'Mark Lester Raguindin Added Mark Lester  Raguindin', '::1', 'success', '2026-07-23 13:38:15'),
(382, 116, 'teacher', 'Added Parent/Guardian', 'Parent/Guardian', NULL, NULL, 'Mark Lester Raguindin Added Parent/Guardian Melba Raguindin', '::1', 'success', '2026-07-23 13:38:36'),
(383, 116, 'teacher', 'Creating new Developmental Profile', 'Developmental', NULL, NULL, 'Mark Lester Raguindin Created student developmental for ', '::1', 'success', '2026-07-23 13:38:49'),
(384, 116, 'teacher', 'Updating Student Behavioral', 'Student Behavioral', NULL, NULL, 'Mark Lester Raguindin Added Student Behavioral to 37', '::1', 'success', '2026-07-23 13:39:04'),
(385, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 1 student(s) to a new school year', '::1', 'success', '2026-07-23 13:39:48'),
(386, 116, 'teacher', 'Adding a new student', 'Students', NULL, NULL, 'Mark Lester Raguindin Added Mark Lester  Raguindin', '::1', 'success', '2026-07-23 14:00:41'),
(387, 116, 'teacher', 'Updating Student Behavioral', 'Student Behavioral', NULL, NULL, 'Mark Lester Raguindin Added Student Behavioral to 39', '::1', 'success', '2026-07-23 14:01:00'),
(388, 116, 'teacher', 'Creating new Developmental Profile', 'Developmental', NULL, NULL, 'Mark Lester Raguindin Created student developmental for ', '::1', 'success', '2026-07-23 14:01:15'),
(389, 116, 'teacher', 'Adding an academic profile', 'Academic Profile', NULL, NULL, 'Mark Lester Raguindin Added an academic profile for student ID: 39', '::1', 'success', '2026-07-23 14:01:34'),
(390, 116, 'teacher', 'Adding an achievement profile', 'Achievement Profile', NULL, NULL, 'Mark Lester Raguindin Added an achievement profile for student ID: 39', '::1', 'success', '2026-07-23 14:01:54'),
(391, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 1 student(s) to a new school year', '::1', 'success', '2026-07-23 14:02:36'),
(392, 116, 'teacher', 'Adding a new student', 'Students', NULL, NULL, 'Mark Lester Raguindin Added Mark Lester  Raguindin', '::1', 'success', '2026-07-23 14:24:51'),
(393, 116, 'teacher', 'Creating new Developmental Profile', 'Developmental', NULL, NULL, 'Mark Lester Raguindin Created student developmental for ', '::1', 'success', '2026-07-23 14:25:05'),
(394, 116, 'teacher', 'Updating Student Behavioral', 'Student Behavioral', NULL, NULL, 'Mark Lester Raguindin Added Student Behavioral to 41', '::1', 'success', '2026-07-23 14:25:31'),
(395, 116, 'teacher', 'Recording Student Health', 'Student Health', NULL, NULL, 'Mark Lester Raguindin Added Health Profile for 41', '::1', 'success', '2026-07-23 14:26:06'),
(396, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 1 student(s) to a new school year', '::1', 'success', '2026-07-23 14:26:55'),
(397, 116, 'teacher', 'Adding a new student', 'Students', NULL, NULL, 'Mark Lester Raguindin Added Mark Lester  Raguindin', '::1', 'success', '2026-07-23 14:39:09'),
(398, 116, 'teacher', 'Creating new Developmental Profile', 'Developmental', NULL, NULL, 'Mark Lester Raguindin Created student developmental for ', '::1', 'success', '2026-07-23 14:39:23'),
(399, 116, 'teacher', 'Updating Student Behavioral', 'Student Behavioral', NULL, NULL, 'Mark Lester Raguindin Added Student Behavioral to 43', '::1', 'success', '2026-07-23 14:39:40'),
(400, 116, 'teacher', 'Recording Attendance', 'Attendance', NULL, NULL, 'Mark Lester Raguindin recorded attendance for 2026-07-23 (2 records)', '::1', 'success', '2026-07-23 14:39:48'),
(401, 116, 'teacher', 'Adding an academic profile', 'Academic Profile', NULL, NULL, 'Mark Lester Raguindin Added an academic profile for student ID: 43', '::1', 'success', '2026-07-23 14:40:08'),
(402, 116, 'teacher', 'Adding an achievement profile', 'Achievement Profile', NULL, NULL, 'Mark Lester Raguindin Added an achievement profile for student ID: 43', '::1', 'success', '2026-07-23 14:40:29'),
(403, 116, 'teacher', 'Recording Student Health', 'Student Health', NULL, NULL, 'Mark Lester Raguindin Added Health Profile for 43', '::1', 'success', '2026-07-23 14:41:04'),
(404, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 1 student(s) to a new school year', '::1', 'success', '2026-07-23 14:42:26'),
(405, 116, 'teacher', 'Adding a new student', 'Students', NULL, NULL, 'Mark Lester Raguindin Added ADA ASDA', '::1', 'success', '2026-07-23 15:14:52'),
(406, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 1 student(s) to a new school year', '::1', 'success', '2026-07-23 15:15:48'),
(407, 3, 'administrative', 'Generated AI dashboard summary', 'Students', NULL, NULL, 'Administrative generated an AI dashboard summary for school year ID: 8', '::1', 'success', '2026-07-23 15:30:23'),
(408, 3, 'administrative', 'Generated AI insight', 'Students', 53, NULL, 'Administrative generated an AI insight for student ID: 53', '::1', 'success', '2026-07-23 15:30:46'),
(409, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 4 student(s) to a new school year', '::1', 'success', '2026-07-23 15:31:54'),
(410, 4, 'admin', 'Reset user password', 'Users', 0, NULL, 'admin reset the password for user ID: ', '::1', 'success', '2026-07-23 15:34:41'),
(411, 4, 'admin', 'Reset user password', 'Users', 0, NULL, 'admin reset the password for user ID: ', '::1', 'success', '2026-07-23 15:36:18'),
(412, 116, 'teacher', 'Generated AI insight', 'Students', 62, NULL, 'Mark Lester Raguindin generated an AI insight for student ID: 62', '::1', 'success', '2026-07-23 15:47:01'),
(413, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 4 student(s) to a new school year', '::1', 'success', '2026-07-23 15:54:02'),
(414, 4, 'admin', 'Deactivated user', 'Users', 119, NULL, 'admin deactivated user ID: 119', '::1', 'success', '2026-07-23 15:57:50'),
(415, 4, 'admin', 'Created user', 'Users', NULL, NULL, 'admin created a new teacher account for April Berbon', '::1', 'success', '2026-07-23 15:58:18'),
(416, 4, 'admin', 'Grade Level', 'Grade Level', NULL, 'Created Grade Level', 'admin Created Grade Level Grade 3', '::1', 'success', '2026-07-23 15:58:37'),
(417, 4, 'admin', 'Adding new section', 'Section', NULL, 'Created Grade Level', 'admin Created new section Andres Bonifacio', '::1', 'success', '2026-07-23 15:58:59'),
(418, 3, 'administrative', 'Generated AI dashboard summary', 'Students', NULL, NULL, 'Administrative generated an AI dashboard summary for school year ID: 8', '::1', 'success', '2026-07-23 16:03:28'),
(419, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 4 student(s) to a new school year', '::1', 'success', '2026-07-23 16:04:50'),
(420, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 1 student(s) to a new school year', '::1', 'success', '2026-07-23 16:16:44'),
(421, 3, 'administrative', 'Rolling over students to new school year', 'Students', NULL, NULL, 'Administrative rolled over 4 student(s) to a new school year', '::1', 'success', '2026-07-23 16:22:07');

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
(34, 80, 8, '2026-07-24', 'Positive Behavior', 'Helped a classmate with schoolwork.', 'None needed', 'Keep it up', 116, '2026-07-23 16:21:14', '2026-07-23 16:21:14'),
(35, 82, 8, '2026-07-22', 'Disciplinary', 'Disrupted class activity.', 'Verbal warning given.', 'Monitor closely', 116, '2026-07-23 16:21:14', '2026-07-23 16:21:14'),
(36, 82, 8, '2026-07-21', 'Disciplinary', 'Disrupted class activity.', 'Verbal warning given.', 'Monitor closely', 116, '2026-07-23 16:21:14', '2026-07-23 16:21:14'),
(37, 82, 8, '2026-07-20', 'Disciplinary', 'Disrupted class activity.', 'Verbal warning given.', 'Monitor closely', 116, '2026-07-23 16:21:14', '2026-07-23 16:21:14');

-- --------------------------------------------------------

--
-- Table structure for table `dashboard_ai_summaries`
--

CREATE TABLE `dashboard_ai_summaries` (
  `id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `summary_text` text NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dashboard_ai_summaries`
--

INSERT INTO `dashboard_ai_summaries` (`id`, `school_year_id`, `summary_text`, `generated_at`) VALUES
(2, 8, 'For the 2026–2027 school year, foundational staffing and section advisory roles are fully established, but immediate administrative attention is required in **Grade 1 - Mahogani**. The primary concern in this section is a compounding risk pattern where severe academic struggle, chronic absenteeism, and recurring disciplinary incidents intersect at once. In the coming weeks, leadership should focus on deploying a coordinated, wrap-around intervention plan for Grade 1 - Mahogani to simultaneously address behavioral, attendance, and learning challenges before they further destabilize student progress.', '2026-07-23 16:03:28'),
(11, 14, 'For the 2027–2028 school year, operational stability is exceptionally strong, with full advisory oversight established and currently no specific grade levels or sections exhibiting flagged at-risk learners across academic, attendance, or disciplinary metrics. This pristine baseline highlights optimal initial engagement and equitable support across both active learning cohorts. To maintain this high standard, the recommended focus area for the coming weeks is to establish routine, proactive early-warning checks to ensure any subtle shifts in learner performance or attendance are caught well before formal intervention is required.', '2026-07-23 13:24:51');

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

--
-- Dumping data for table `developmental_profiles`
--

INSERT INTO `developmental_profiles` (`id`, `student_id`, `school_year_id`, `domain`, `observation`, `recommendation`, `recorded_by`, `created_at`, `updated_at`) VALUES
(15, 81, 8, '', 'Works well in group activities.', 'Continue encouraging peer collaboration.', 117, '2026-07-23 16:21:14', '2026-07-23 16:21:14');

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
(5, 'Grade 1', '2026-06-28 14:50:48', '2026-06-28 14:50:48'),
(6, 'Grade 2', '2026-07-23 15:27:49', '2026-07-23 15:27:49'),
(8, 'Grade 3', '2026-07-23 15:58:37', '2026-07-23 15:58:37');

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

--
-- Dumping data for table `health_profiles`
--

INSERT INTO `health_profiles` (`id`, `student_id`, `school_year_id`, `height_cm`, `weight_kg`, `bmi`, `bmi_classification`, `blood_type`, `allergies`, `medical_conditions`, `vision_screening_result`, `hearing_screening_result`, `immunization_status`, `recorded_by`, `created_at`, `updated_at`) VALUES
(16, 80, 8, 115.00, 22.00, 16.60, 'Normal', 'O+', 'None', 'None', 'Normal', 'Normal', 'Complete', 116, '2026-07-23 16:21:14', '2026-07-23 16:21:14');

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
(8, '2026-2027', '2026-06-08', '2027-04-05', 'inactive', '2026-05-15 05:40:37', '2026-07-23 16:22:07'),
(14, '2027-2028', '2027-06-01', '2028-04-01', 'active', '2026-07-09 15:17:37', '2026-07-23 16:22:07');

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
(2, 5, 'Mahogani', 116, '2026-06-29 14:15:19', '2026-06-29 15:25:30'),
(8, 5, 'Venus', 117, '2026-07-09 16:05:41', '2026-07-09 16:05:41'),
(10, 6, 'Neptune', 119, '2026-07-23 15:27:49', '2026-07-23 15:27:49'),
(11, 8, 'Andres Bonifacio', 120, '2026-07-23 15:58:59', '2026-07-23 15:58:59');

-- --------------------------------------------------------

--
-- Table structure for table `section_teacher_assignments`
--

CREATE TABLE `section_teacher_assignments` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section_teacher_assignments`
--

INSERT INTO `section_teacher_assignments` (`id`, `section_id`, `teacher_id`, `school_year_id`, `created_at`, `updated_at`) VALUES
(5, 2, 116, 14, '2026-07-09 15:21:09', '2026-07-09 15:21:09'),
(11, 11, 120, 8, '2026-07-23 15:58:59', '2026-07-23 15:58:59');

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
  `status` enum('active','archived') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `lrn`, `first_name`, `middle_name`, `last_name`, `suffix`, `birth_date`, `gender`, `address`, `school_year_id`, `grade_level_id`, `section_id`, `recorded_by`, `status`, `created_at`, `updated_at`) VALUES
(77, 'TEST-AUTOACT-1784823', 'AutoActivate', '', 'Test', '', '2015-01-01', 'Male', 'Test Address', 8, 5, 2, 116, 'archived', '2026-07-23 16:15:31', '2026-07-23 16:16:44'),
(78, 'TEST-AUTOACT-1784823', 'AutoActivate', '', 'Test', '', '2015-01-01', 'Male', 'Test Address', 14, 8, 11, 3, 'active', '2026-07-23 16:16:44', '2026-07-23 16:16:44'),
(80, '100000000001', 'Juan', '', 'Dela Cruz', '', '2019-03-14', 'Male', 'San Jose Sur, Mallig, Isabela', 8, 5, 2, 116, 'archived', '2026-07-23 16:21:14', '2026-07-23 16:22:07'),
(81, '100000000002', 'Maria', '', 'Santos', '', '2019-07-22', 'Female', 'San Jose Sur, Mallig, Isabela', 8, 5, 8, 117, 'archived', '2026-07-23 16:21:14', '2026-07-23 16:22:07'),
(82, '100000000003', 'Pedro', '', 'Reyes', '', '2019-01-30', 'Male', 'San Jose Sur, Mallig, Isabela', 8, 5, 2, 116, 'archived', '2026-07-23 16:21:14', '2026-07-23 16:22:07'),
(83, '100000000004', 'Ana', '', 'Lopez', '', '2018-11-05', 'Female', 'San Jose Sur, Mallig, Isabela', 8, 6, 10, 119, 'archived', '2026-07-23 16:21:14', '2026-07-23 16:22:07'),
(84, '100000000001', 'Juan', '', 'Dela Cruz', '', '2019-03-14', 'Male', 'San Jose Sur, Mallig, Isabela', 14, 8, 11, 3, 'active', '2026-07-23 16:22:07', '2026-07-23 16:22:07'),
(85, '100000000004', 'Ana', '', 'Lopez', '', '2018-11-05', 'Female', 'San Jose Sur, Mallig, Isabela', 14, 8, 11, 3, 'active', '2026-07-23 16:22:07', '2026-07-23 16:22:07'),
(86, '100000000003', 'Pedro', '', 'Reyes', '', '2019-01-30', 'Male', 'San Jose Sur, Mallig, Isabela', 14, 8, 11, 3, 'active', '2026-07-23 16:22:07', '2026-07-23 16:22:07'),
(87, '100000000002', 'Maria', '', 'Santos', '', '2019-07-22', 'Female', 'San Jose Sur, Mallig, Isabela', 14, 8, 11, 3, 'active', '2026-07-23 16:22:07', '2026-07-23 16:22:07');

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
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp(),
  `updated_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `status`, `profile_picture`, `created_at`, `updated_at`) VALUES
(3, 'Administrative', 'administrative@gmail.com', '$2y$10$CQASCJeXsOYOvWm4kK03i.S1SxUWsdPMv56Qlz04eq0GazfxE8FSi', 'administrative', 'active', 'storage/profiles/pfp_3_1782485485.jpg', '2026-05-09', '2026-06-26'),
(4, 'admin', 'admin@gmail.com', '$2y$10$ihbCVd8WOJO17B4BFQgAUORhb1UEYpIFmpd1Q/ShW6n5uNMkLZ7kq', 'admin', 'active', 'storage/profiles/pfp_4_1782273895.jpg', '2026-05-09', '2026-06-24'),
(13, 'Registrar', 'registrar@school.edu.ph', '$2y$10$IEz8YAjPkN2ddoQTR6YRUupEwnweJ6YNzsl8opZsKoXrMMFkaJYZG', 'registrar', 'active', 'storage/profiles/pfp_13_1779633057.jpg', '2026-05-15', '2026-05-30'),
(116, 'Mark Lester Raguindin', 'teacher.edu.ph@gmail.com', '$2y$10$LZIVGYOkHmFGyOfUS7Nvo.Hfe1kigmfCJM36QvakHqVaLAKq575UC', 'teacher', 'active', 'storage/profiles/pfp_116_1782996344.jpg', '2026-06-24', '2026-07-04'),
(117, 'teacher 1', 'teacher2@gmail.com', '$2y$10$VTwGP0epmVJOKSpuu6YtfO2Fs1.vjfHINVlb49tDQtyY1fnEKSKDS', 'teacher', 'active', 'storage/profiles/pfp_117_1783614517.png', '2026-07-10', '2026-07-10'),
(119, 'Teacher Two', 'teacher2@school.edu.ph', '$2y$10$.m5KtR/BU72SUnvWAi91k.mdgdvM8UPfRShh0EzxpEbdJYmvjpfAm', 'teacher', 'inactive', NULL, '2026-07-23', '0000-00-00'),
(120, 'April Berbon', 'april@gmail.com', '$2y$10$v0v1QI1cenFT2xRhXqWvOOswx77JVEMv1xStVaEFcALZEGL/FUfp2', 'teacher', 'active', NULL, '2026-07-23', '0000-00-00');

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
-- Indexes for table `achievements_profiles`
--
ALTER TABLE `achievements_profiles`
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
-- Indexes for table `at_risk_insights`
--
ALTER TABLE `at_risk_insights`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_year_unique` (`student_id`,`school_year_id`);

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
-- Indexes for table `dashboard_ai_summaries`
--
ALTER TABLE `dashboard_ai_summaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `school_year_unique` (`school_year_id`);

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
-- Indexes for table `section_teacher_assignments`
--
ALTER TABLE `section_teacher_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_section_year` (`section_id`,`school_year_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `school_year_id` (`school_year_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_lrn_school_year` (`lrn`,`school_year_id`),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `achievements_profiles`
--
ALTER TABLE `achievements_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `at_risk_insights`
--
ALTER TABLE `at_risk_insights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=422;

--
-- AUTO_INCREMENT for table `behavioral_profiles`
--
ALTER TABLE `behavioral_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `dashboard_ai_summaries`
--
ALTER TABLE `dashboard_ai_summaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `developmental_profiles`
--
ALTER TABLE `developmental_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `grade_levels`
--
ALTER TABLE `grade_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `health_profiles`
--
ALTER TABLE `health_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `parents_guardians`
--
ALTER TABLE `parents_guardians`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `school_year`
--
ALTER TABLE `school_year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `section_teacher_assignments`
--
ALTER TABLE `section_teacher_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

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
-- Constraints for table `achievements_profiles`
--
ALTER TABLE `achievements_profiles`
  ADD CONSTRAINT `achievements_profiles_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `achievements_profiles_ibfk_2` FOREIGN KEY (`school_year_id`) REFERENCES `school_year` (`id`),
  ADD CONSTRAINT `achievements_profiles_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

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
-- Constraints for table `section_teacher_assignments`
--
ALTER TABLE `section_teacher_assignments`
  ADD CONSTRAINT `sta_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sta_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `sta_ibfk_3` FOREIGN KEY (`school_year_id`) REFERENCES `school_year` (`id`);

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
