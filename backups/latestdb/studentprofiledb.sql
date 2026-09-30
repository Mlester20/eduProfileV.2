-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 30, 2026 at 02:59 PM
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
(1, 116, 'teacher', 'Importing students from Excel', 'Students', NULL, NULL, 'Mark Lester Raguindin Imported 0 student(s) via Excel', '::1', 'success', '2026-08-22 14:54:44'),
(2, 116, 'teacher', 'Importing students from Excel', 'Students', NULL, NULL, 'Mark Lester Raguindin Imported 1 student(s) via Excel', '::1', 'success', '2026-08-22 14:55:12'),
(3, 116, 'teacher', 'Deleting a student', 'Students', NULL, NULL, 'Mark Lester Raguindin Deleted a student with ID: 148', '::1', 'success', '2026-09-23 06:56:38'),
(4, 116, 'teacher', 'Importing students from Excel', 'Students', NULL, NULL, 'Mark Lester Raguindin Imported 1 student(s) via Excel', '::1', 'success', '2026-09-23 06:57:59'),
(5, 4, 'admin', 'Created user', 'Users', NULL, NULL, 'admin created a new teacher account for REMA BAGAMASPAD IGUAN', '::1', 'success', '2026-09-29 12:27:05'),
(6, 4, 'admin', 'Adding new section', 'Section', NULL, 'Created Grade Level', 'admin Created new section Yellow', '::1', 'success', '2026-09-29 12:27:21'),
(7, 121, 'teacher', 'Importing students from Excel', 'Students', NULL, NULL, 'REMA BAGAMASPAD IGUAN Imported 19 student(s) via Excel', '::1', 'success', '2026-09-29 12:33:25'),
(8, 4, 'admin', 'Created user', 'Users', NULL, NULL, 'admin created a new teacher account for MARICEL MANUEL BESA', '::1', 'success', '2026-09-29 12:57:35'),
(9, 4, 'admin', 'Grade Level', 'Grade Level', NULL, 'Created Grade Level', 'admin Created Grade Level Grade 5', '::1', 'success', '2026-09-29 12:57:56'),
(10, 4, 'admin', 'Grade Level', 'Grade Level', NULL, 'Created Grade Level', 'admin Created Grade Level Grade 6', '::1', 'success', '2026-09-29 12:58:05'),
(11, 4, 'admin', 'Adding new section', 'Section', NULL, 'Created Grade Level', 'admin Created new section Emerald Green', '::1', 'success', '2026-09-29 12:58:33'),
(12, 122, 'teacher', 'Importing students from Excel', 'Students', NULL, NULL, 'MARICEL MANUEL BESA Imported 33 student(s) via Excel', '::1', 'success', '2026-09-29 12:58:58'),
(13, 4, 'admin', 'Created user', 'Users', NULL, NULL, 'admin created a new teacher account for CATHERINE VALDEZ MALLILLIN', '::1', 'success', '2026-09-30 12:18:25'),
(14, 4, 'admin', 'Created user', 'Users', NULL, NULL, 'admin created a new teacher account for LOIDA SANTIAGO BALILA', '::1', 'success', '2026-09-30 12:18:54'),
(15, 4, 'admin', 'Created user', 'Users', NULL, NULL, 'admin created a new teacher account for JENNIFER MILAN BESA', '::1', 'success', '2026-09-30 12:19:23'),
(16, 4, 'admin', 'Created user', 'Users', NULL, NULL, 'admin created a new teacher account for AMELITA IGLESIA POLICARPIO', '::1', 'success', '2026-09-30 12:21:32'),
(17, 4, 'admin', 'Created user', 'Users', NULL, NULL, 'admin created a new teacher account for LHENNIE JANE BAGAMASPAD RAFAEL', '::1', 'success', '2026-09-30 12:21:58'),
(18, 4, 'admin', 'Created user', 'Users', NULL, NULL, 'admin created a new teacher account for ANALYN PILAR OCTAVIANO', '::1', 'success', '2026-09-30 12:22:22'),
(19, 4, 'admin', 'Adding new section', 'Section', NULL, 'Created Grade Level', 'admin Created new section Purple', '::1', 'success', '2026-09-30 12:23:12'),
(20, 4, 'admin', 'Adding new section', 'Section', NULL, 'Created Grade Level', 'admin Created new section Red', '::1', 'success', '2026-09-30 12:23:29'),
(21, 4, 'admin', 'Adding new section', 'Section', NULL, 'Created Grade Level', 'admin Created new section Royal Blue', '::1', 'success', '2026-09-30 12:23:57'),
(22, 4, 'admin', 'Grade Level', 'Grade Level', NULL, 'Created Grade Level', 'admin Created Grade Level Grade 4', '::1', 'success', '2026-09-30 12:29:52'),
(23, 4, 'admin', 'Adding new section', 'Section', NULL, 'Created Grade Level', 'admin Created new section Baby-Pink', '::1', 'success', '2026-09-30 12:31:49'),
(24, 4, 'admin', 'Adding new section', 'Section', NULL, 'Created Grade Level', 'admin Created new section FUCHSIA-PINK', '::1', 'success', '2026-09-30 12:33:04'),
(25, 4, 'admin', 'Grade Level', 'Grade Level', NULL, 'Created Grade Level', 'admin Created Grade Level Kinder', '::1', 'success', '2026-09-30 12:33:35'),
(26, 4, 'admin', 'Adding new section', 'Section', NULL, 'Created Grade Level', 'admin Created new section APPLE-GREEN-1', '::1', 'success', '2026-09-30 12:34:15'),
(27, 123, 'teacher', 'Importing students from Excel', 'Students', NULL, NULL, 'CATHERINE VALDEZ MALLILLIN Imported 16 student(s) via Excel', '::1', 'success', '2026-09-30 12:43:57'),
(28, 124, 'teacher', 'Importing students from Excel', 'Students', NULL, NULL, 'LOIDA SANTIAGO BALILA Imported 23 student(s) via Excel', '::1', 'success', '2026-09-30 12:51:51'),
(29, 125, 'teacher', 'Importing students from Excel', 'Students', NULL, NULL, 'JENNIFER MILAN BESA Imported 30 student(s) via Excel', '::1', 'success', '2026-09-30 12:53:26'),
(30, 126, 'teacher', 'Importing students from Excel', 'Students', NULL, NULL, 'AMELITA IGLESIA POLICARPIO Imported 27 student(s) via Excel', '::1', 'success', '2026-09-30 12:55:25'),
(31, 127, 'teacher', 'Importing students from Excel', 'Students', NULL, NULL, 'LHENNIE JANE BAGAMASPAD RAFAEL Imported 33 student(s) via Excel', '::1', 'success', '2026-09-30 12:56:14'),
(32, 128, 'teacher', 'Importing students from Excel', 'Students', NULL, NULL, 'ANALYN PILAR OCTAVIANO Imported 20 student(s) via Excel', '::1', 'success', '2026-09-30 12:57:26');

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
(11, 14, 'For the 2027–2028 school year, all four sections are fully staffed with assigned advisers, and no specific grade levels or sections currently contain learners flagged for academic, attendance, or behavioral risks. The most critical operational pattern requiring immediate attention is a severe imbalance between capacity and enrollment, as full advisory resources are actively maintained across four sections for only a single enrolled student. Over the coming weeks, the primary focus area should be conducting a comprehensive enrollment audit and recruitment initiative to reconcile section allocations with actual student roster data.', '2026-08-06 14:57:16');

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
(5, 'Grade 1', '2026-06-28 14:50:48', '2026-06-28 14:50:48'),
(6, 'Grade 2', '2026-07-23 15:27:49', '2026-07-23 15:27:49'),
(8, 'Grade 3', '2026-07-23 15:58:37', '2026-07-23 15:58:37'),
(11, 'Grade 5', '2026-09-29 12:57:56', '2026-09-29 12:57:56'),
(12, 'Grade 6', '2026-09-29 12:58:05', '2026-09-29 12:58:05'),
(13, 'Grade 4', '2026-09-30 12:29:52', '2026-09-30 12:29:52'),
(14, 'Kinder', '2026-09-30 12:33:35', '2026-09-30 12:33:35');

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

--
-- Dumping data for table `parents_guardians`
--

INSERT INTO `parents_guardians` (`id`, `student_id`, `recorded_by`, `father_name`, `father_occupation`, `father_contact`, `mother_name`, `mother_occupation`, `mother_contact`, `guardian_name`, `guardian_relationship`, `guardian_contact`, `created_at`, `updated_at`) VALUES
(37, 149, 116, 'Pedro Dela Cruz', 'Farmer', '09171234567', 'Maria Dela Cruz', 'Vendor', '09179876543', '', '', '', '2026-09-23 06:57:59', '2026-09-23 06:57:59'),
(38, 150, 121, 'BALILA, VILMAR PAAZ', '', '', 'HERNAL,LAIKHA,AULO', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(39, 151, 121, 'BALINAGAY, JOSHUA HERRERA', '', '', 'REYES,JONALYN,REYES', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(40, 152, 121, 'BULATAO, ROMULO VILLALON', '', '', 'CASTILLO,MENCHU,DUQUE', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(41, 153, 121, 'CARIASO, MARK ALVIN FLORES', '', '', 'BARTOLOME,RONALYN,ASPREC', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(42, 154, 121, 'GAGELONIA, RENGIE ACOSTA', '', '', 'COLLADO,ROSE ANN,SANTOS', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(43, 155, 121, 'GAMBOA, ELMER BUENO', '', '', 'ALIPIO,JOSEPHINE,RAMOS', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(44, 156, 121, 'GEBAÑA, JOHN KHEVIN -', '', '', 'FLORES,DIANA MARIE,ENCISO', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(45, 157, 121, 'JULIANO, ANGELITO ERCELLA', '', '', 'BONITA,VERNALIN,VALDEZ', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(46, 158, 121, 'RAFAEL, HAROLD TORIBIO', '', '', 'BAGAMASPAD,LHENNIE JANE,AGATEP', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(47, 159, 121, 'SOBREPEÑA, RICO VALDOZ', '', '', 'MAURO,REYKA,SERAFICA', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(48, 160, 121, 'ABALOS, RAY JOHN PINEDA', '', '', 'PARAISO,AIZA,POLICARPIO', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(49, 161, 121, 'BAUTISTA, JIMBOY JOSE', '', '', 'ELARDE,MARIVIC,ORDONIA', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(50, 162, 121, 'BUENO, MHILFORD BALA', '', '', 'GANIR,MARVIE,-', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(51, 163, 121, 'BUNAO, JOMER MABAZZA', '', '', 'HELARDO,REGINE,MOLINA', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(52, 164, 121, 'LABE, JORDAN DAVE CADAY', '', '', 'LOPEZ,GENALYN,DOMRIQUE', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(53, 165, 121, 'OCTAVIANO, KENNETH JAMES TONOG', '', '', 'PATTUGALAN,HAZEL,AQUINO', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(54, 166, 121, 'RAMOS, JEFFRY TAGARAO', '', '', 'VIERNES,ANNALIZA,ONIA', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(55, 167, 121, 'RAMOS, FIDEL VILLAROMAN', '', '', 'BESA,HAZEL,MACADANGDANG', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(56, 168, 121, 'SOMERA, NELSON ORERO', '', '', 'SANTIAGO,AGNES,VERA', '', '', '', '', '', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(57, 169, 122, 'ABALOS, REY JOHN PINEDA SR', '', '', 'PARAISO,AIZA,POLICARPIO', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(58, 170, 122, 'ALVARADO, SANDY MATA', '', '', 'JOVILLANOS,ANAMARIE', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(59, 171, 122, 'CASTILLO, MARLON GARCIA', '', '', 'GASPAR,MARITES,BAJACAN', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(60, 172, 122, 'CORPUZ, ROBERT SURAT SR', '', '', 'DELA CRUZ,JING JING,BAUTISTA', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(61, 173, 122, 'FELIX, LORETO SUNIO', '', '', 'FLORES,ARFLOR,JULIANO', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(62, 174, 122, 'MENOR, JOVITO SAMSON', '', '', 'FERRER,MARITES,CASTRO', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(63, 175, 122, 'PARAISO, REYMAR POLICARPIO', '', '', 'BARTOLOME,EVANGELINE,MACAPULAY', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(64, 176, 122, 'VALDEZ, ORLY ACOBA', '', '', 'SOBREPEÑA,LANIELYN,VALDOZ', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(65, 177, 122, 'ALVARADO, SANDY MATA SR', '', '', 'JOVILLANOS,ANA MARIE,VILORIA', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(66, 178, 122, 'ANCHETA, MARTY MARAVE', '', '', 'GIRAO,VANESSA FAYE,ESTIGOY', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(67, 179, 122, 'ANTALAN, GILBERT CORTEZ', '', '', 'BACCAY,BABYLYN,LACTAO', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(68, 180, 122, 'AQUILO, MARCIAL TABBU', '', '', 'COSTALES,GRACE,GALAPON', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(69, 181, 122, 'BALICAO, ROEL ANCHETA', '', '', 'AULO,GENYBE,MARCOS', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(70, 182, 122, 'BONITA, ROLANDO BARROZO', '', '', 'SANTUYO,RACHEL,PINDOR', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(71, 183, 122, 'BUENO, JOEMER YUSON', '', '', 'OCTAVIANO,PRINCESS,MAGDAY', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(72, 184, 122, 'CAPANANG, MARK ANTHONY TOLENTINO', '', '', 'ARETAÑO,GLENDA,EPOC', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(73, 185, 122, 'DAGA, CLEMENTE GUBGUBAN', '', '', 'RAMISCAL,CARMELA,LIZARDO', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(74, 186, 122, 'DOMINGO, DANILO TABELI', '', '', 'CACHO,CLARENCE,TUNGPALAN', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(75, 187, 122, '', '', '', 'FERRER,JIJI,GUIAM', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(76, 188, 122, 'FLORENTINO, MARIO LAMINA', '', '', 'ALIPIO,MARY ROSE,RAMOS', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(77, 189, 122, 'GASPAR, ROMEO DELA CRUZ', '', '', 'BALA,FELY,RAMOS', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(78, 190, 122, 'GONGORA, HENRY GERMAN', '', '', 'GALINDEZ,RHEACEL,SIBLAG', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(79, 191, 122, 'GUIANG, RICHARD SIBORBORO', '', '', 'ALCANTARA,LANIE,DUCUSIN', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(80, 192, 122, 'JULIANO, ROMY ERCILLA', '', '', 'MACADANGDANG,EDMALYN,LEAL', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(81, 193, 122, 'OCTAVIANO, ERROL ORZAME', '', '', 'BERNANDINO,HELEN,MAGDALUYO', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(82, 194, 122, 'OPERIO, JOMAR GAMAS', '', '', 'CUEVAS,SHELLA MAE,AQUI', '', '', '', '', '', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(83, 195, 122, 'PINEDA, FREDDIE SINECA', '', '', 'GAONA,JENNY,ESPIRITU', '', '', '', '', '', '2026-09-29 12:58:58', '2026-09-29 12:58:58'),
(84, 196, 122, 'RAMISCAL, ORLAN POLICARPIO', '', '', 'VALLES,MELODY,MENZI', '', '', '', '', '', '2026-09-29 12:58:58', '2026-09-29 12:58:58'),
(85, 197, 122, 'RAMISCAL, ERWIN ORZAME', '', '', 'DELA CRUZ,JOCELYN,MANAHAN', '', '', '', '', '', '2026-09-29 12:58:58', '2026-09-29 12:58:58'),
(86, 198, 122, 'REGINIO, WILFRED ABALOS', '', '', 'FLORES,REGINE,DAWAGAN', '', '', '', '', '', '2026-09-29 12:58:58', '2026-09-29 12:58:58'),
(87, 199, 122, 'RELLORTA, REYNITO COLOMA', '', '', 'TOMAS,CHRISTINE,IBAÑES', '', '', '', '', '', '2026-09-29 12:58:58', '2026-09-29 12:58:58'),
(88, 200, 122, 'SOBREPEÑA, ARIES VALDOZ', '', '', 'VALDEZ,DARLINA,VELASQUEZ', '', '', '', '', '', '2026-09-29 12:58:58', '2026-09-29 12:58:58'),
(89, 201, 122, 'SOMERA, GERIEL VICTORIO', '', '', 'ABRIAM,ELIZABETH,PAOLO', '', '', '', '', '', '2026-09-29 12:58:58', '2026-09-29 12:58:58'),
(90, 202, 123, 'ABALOS, RAY JOHN PINEDA', '', '', 'PARAISO,AIZA,POLICARPIO', '', '', '', '', '', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(91, 203, 123, 'AGREGADO, ARLEIGH ANDRADA', '', '', 'RAMOS,ROCHELLE,AUSTRIA', '', '', '', '', '', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(92, 204, 123, 'CERA, ROHAN HONG', '', '', 'MATIAS,MARY JOI,IGNACIO', '', '', '', '', '', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(93, 205, 123, 'GALAPON, JANUARD ESPIRITU', '', '', 'FORTO,JAMELLAH,SANTIAGO', '', '', '', '', '', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(94, 206, 123, 'GONGORA, HENRY GERMAN', '', '', 'GALINDEZ,RHEACEL,SIBLAG', '', '', '', '', '', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(95, 207, 123, 'ITLIONG, REYNALDO OPEÑANO', '', '', 'GASPAR,ANNABEL,DELA CRUZ', '', '', '', '', '', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(96, 208, 123, 'JOSE, ROGEL SOMERA', '', '', 'ANTING,RUTCHEL,DIAZ', '', '', '', '', '', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(97, 209, 123, 'NUDO, ARIEL DUMAGUING', '', '', 'FLORES,MARINEL,SISON', '', '', '', '', '', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(98, 210, 123, 'RAMISCAL, MENECIO DELA CRUZ JR', '', '', 'MAPILI,ROSALIE,NIDUAZA', '', '', '', '', '', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(99, 211, 123, 'RAMOS, JONATHAN TAGARAO', '', '', 'MASIDDO,MARILOU,CARA', '', '', '', '', '', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(100, 212, 123, 'ANCHETA, MARTY MARAVE', '', '', 'GIRAO,VANESSA FAYE,ESTIGOY', '', '', '', '', '', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(101, 213, 123, 'CABENIAN, EFREN PARUCHA', '', '', 'CERA,LUISA,HONG', '', '', '', '', '', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(102, 214, 123, 'LARDIZABAL, DOMINADOR BONITA JR', '', '', 'LAPITAN,EDEN,ESTEVES', '', '', '', '', '', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(103, 215, 123, 'RASPADO, JUNBOY PASTOR', '', '', 'DEL ROSARIO,IRISH NICOLE,CORPUZ', '', '', '', '', '', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(104, 216, 123, 'SOMERA, GERIEL VICTORIO', '', '', 'ABRIAM,ELIZABETH,PAULO', '', '', '', '', '', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(105, 217, 123, 'SOMERA, VIRGILIO BALABA JR', '', '', 'COLLADO,MARY ANNE,FERRER', '', '', '', '', '', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(106, 218, 124, 'BALAGAN, KELVIN NELSON CALLEJO', '', '', 'RAMISCAL,MARILYN,LIZARDO', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(107, 219, 124, 'BENEDICTO, JOHN ERIC ELEFANTE', '', '', 'MANAGUELOD,RODALYN,LAURETA', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(108, 220, 124, 'BONITA, REGIE BARUZO', '', '', 'TARIGA,GLAIZA,SEVILLA', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(109, 221, 124, 'CARIAGA, JOHN LLOYD FERRER', '', '', 'UBALDE,JERLYN,CASTILLO', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(110, 222, 124, 'CORPUZ, IAN KING PASAMONTE', '', '', 'MONONG,REGINE,SALWAGAN', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(111, 223, 124, 'GAGELONIA, MARIANO ACOSTA JR', '', '', 'GUINAPON,ELVIRA,DAYAG', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(112, 224, 124, 'IBAÑEZ, DANILO REVOCAL', '', '', 'CARLOS,MARIFE,SOMERA', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(113, 225, 124, 'LAUD, RAYMOND - SR', '', '', 'TABADA,DIVINA,-', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(114, 226, 124, 'MAGCALAS, JOMAR MALLARI', '', '', 'BENEDICTO,MIRASOL,ELEFANTE', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(115, 227, 124, 'PALAFOX, DENNIS BALINAGAY', '', '', 'DELA CRUZ,JACQUELINE,BAUTISTA', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(116, 228, 124, 'RAMISCAL, ANGELITO ORZAME JR', '', '', 'FRAGIO,STEPHANY,ASPREC', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(117, 229, 124, 'SOBREPEÑA, DOMINIC -', '', '', 'TABADA,ROSE ANNE,GASPAR', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(118, 230, 124, 'SOMERA, RICHARD VICTORIA', '', '', 'LAUD,EMMA,VELASCO', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(119, 231, 124, 'VALDEZ, MIGUELITO OLIVIA', '', '', 'RESPICIO,HANNA JANE,CARBONEL', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(120, 232, 124, 'VILLAROMAN, JEJOMAR ESTRADA', '', '', 'VICENTE,ERNALYN,ESCALANTE', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(121, 233, 124, 'BALUGA, JASON BALIGOD', '', '', 'GAGELONIA,EVERGREEN,GUINAPON', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(122, 234, 124, 'CORPUZ, JACKSON MINA', '', '', 'DOCTOR,JANILYN,DIAZ', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(123, 235, 124, 'ESTIGOY, JUMER POLICARPIO', '', '', 'RAMOS,LOVE JOY,BUENO', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(124, 236, 124, 'FERRER, ELMER TIAM', '', '', 'GAGELONIA,MARILOU,ARZADON', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(125, 237, 124, 'FRANCISCO, RELIO MANABAT', '', '', 'DAGA,EVELYN,BAGUNO', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(126, 238, 124, 'GUEVARRA, RODEL GUIAO', '', '', 'RAMISCAL,APRIL JADE,BUCAGO', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(127, 239, 124, 'LAUD, RAYMUND ESPIRITU', '', '', 'TABADA,DIVINA GRACE,GASPAR', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(128, 240, 124, 'NATIVIDAD, JEFFREY ARIOLA', '', '', 'ANCHETA,SIMERHOSE,MOLINA', '', '', '', '', '', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(129, 241, 125, 'CERA, MELVIN DIMAANO', '', '', 'LOTTO,RODALYN,SEMILLANO', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(130, 242, 125, 'CREDO, DOMINADOR CABUNGGAN JR', '', '', 'SICAM,REMILYN,TOLARBA', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(131, 243, 125, '', '', '', 'DAGA,LIZARDO,NORMALYN', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(132, 244, 125, 'DUMAYAS, JUN-JUN VIRAY', '', '', 'GABAY,CRISEL,BAUTISTA', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(133, 245, 125, '', '', '', 'FAUSTINO,MAY,BALILA', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(134, 246, 125, 'GAMBOA, JAYSON REYES', '', '', 'DELA CRUZ,ELMA,FUENTES', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(135, 247, 125, 'GEBAÑA, JOHN KHEVIN -', '', '', 'FLORES,DIANA MARIE,ENCISO', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(136, 248, 125, 'GOMGOM-O, FRANCIS BACOCO', '', '', 'CATAMEN,FE,BAGTANG', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(137, 249, 125, 'GONGORA, HENRY GERMAN', '', '', 'GALINDEZ,RHEACEL,SIBLAG', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(138, 250, 125, 'GUMIRAN, RONALD GOYAGOY', '', '', 'PINEDA,MAUREEN,SANTOS', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(139, 251, 125, 'RELLORTA, REYNITO COLOMA', '', '', 'TOMAS,CRISTINE,IBAÑEZ', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(140, 252, 125, 'ROMERO, RENANTE MARTIN', '', '', 'HERNAL,MARISSA JOY,AULO', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(141, 253, 125, 'SOMERA, HENRY BULALAYAO', '', '', 'OLIGO,PRIMA,APOSTOL', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(142, 254, 125, 'ANTONIO, JOHNNY SR', '', '', 'PALOMA,FLORENDA', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(143, 255, 125, 'BALINAGAY, JAYSON HERRERA', '', '', 'GANATICE,ROSEBEL,AGUSTIN', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(144, 256, 125, 'BALINTAG, CANDIE PENTECOSTES', '', '', 'DELA CRUZ,AMICA,MANIPON', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(145, 257, 125, 'BONITA, MARIO LAYOGAN', '', '', 'DOLORIDO,MARJORIE,ESTREMOS', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(146, 258, 125, 'BUENO, NOBEJIO TORES', '', '', 'CABUSAO,MARIA THERESA,BERNAL', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(147, 259, 125, 'CAPANANG, FERDINAND TOLENTINO', '', '', 'VIERNES,ABEGAIL,BALTASAR', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(148, 260, 125, 'CORPUZ, DAVID DAYAG', '', '', 'BALA,DAISELYN,ANUNCIACION', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(149, 261, 125, 'DE LEON, JONY SOMERA', '', '', 'GIL,JESSA,VALDERAS', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(150, 262, 125, 'FERRER, PAUL AARON ORZAME', '', '', 'DAMASCO,JANE JOY,BAUTISTA', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(151, 263, 125, 'JOSE, RUBEN SOMERA', '', '', 'FERRER,SHARLENE,ORZAME', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(152, 264, 125, 'JULIANO, ARIEL ERCILLA', '', '', 'GABUYA,JOAN,RAMOS', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(153, 265, 125, 'MINA, ARNOLD BENEDICTO', '', '', 'CAPILI,ANECITA,MAGPAROK', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(154, 266, 125, 'OCTAVIANO, ERROL ORZAME', '', '', 'BERDANDINO,HELEN,MAGDALUYO', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(155, 267, 125, 'PINEDA, FREDDIE SENICA', '', '', 'GAONA,JENNY,ESPIRITU', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(156, 268, 125, 'RAMOS, JEFFRY TARAGAO', '', '', 'VIERNES,ANNALIZA,ONIA', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(157, 269, 125, 'RAMOS, JOHN FLORENCE MAGAWAY', '', '', 'FERRER,MARIBEL,AMBATALI', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(158, 270, 125, 'SOMERA, GERIEL VICTORIO', '', '', 'ABRIAM,ELIZABETH,PAULO', '', '', '', '', '', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(159, 271, 126, 'BALILA, RUNEL BALICAO', '', '', 'PASTOR,CARMELA,FACON', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(160, 272, 126, 'BUNAO, JESSIE JIM FERRES', '', '', 'EUGENIO,MARY GRACE,-', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(161, 273, 126, 'DE GUZMAN, JEFFERSON TOMAS', '', '', 'COLOMA,GLENDA JOY,BESA', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(162, 274, 126, 'GALAPON, ALDRIN -', '', '', 'DELOS SANTOS,KRISHALY,TEJERO', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(163, 275, 126, 'GAMBOA, FRANCIS VERA', '', '', 'CASTIL,NERILYN,CAADIANG', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(164, 276, 126, '', '', '', 'GASPAR,JESSALYN,DECANO', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(165, 277, 126, 'GOMBIO, REMEGIO JR VELEZ', '', '', 'BUENO,RACHEL MAY,DUQUE', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(166, 278, 126, 'JOSE, ROMMEL SOMERA', '', '', 'GAVINO,NENITA,AGUB', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(167, 279, 126, 'MARCOS, ROGELIO CABANA', '', '', 'ADVENCULA,MARILOU,TANGONAN', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(168, 280, 126, 'MENDIORO, REY JOHN HERNANDO', '', '', 'BACCAY,GINALYN,LACTAO', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(169, 281, 126, 'NALLAWARIGE, WARUNA SHASHIKA SUMANASIRI', '', '', 'FAUSTINO,MAY,BALILA', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(170, 282, 126, 'OCTAVIANO, MARVIN MADRIAGA', '', '', 'PILAR,ANALYN,RODOLFO', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(171, 283, 126, 'PINEDA, JERWIN DELOS REYES', '', '', 'GUZMAN,CHARO,ABAD', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(172, 284, 126, 'ALEJO, ALFREDO VENTURA', '', '', 'SISON,NELLIE MAR,MARIANO', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(173, 285, 126, 'BAUTISTA, JAYMAR FERRER', '', '', 'DELA CRUZ,EDEN,MANIPON', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(174, 286, 126, 'BUENO, JOEMER YUZON', '', '', 'OCTAVIANO,PRINCESS,MAGDAY', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(175, 287, 126, 'ERCILLA, JOSE RAMISCAL', '', '', 'LICUBEN,CECILLE,DANGILAN', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(176, 288, 126, 'ESTIGOY, JUMER POLICARPIO', '', '', 'RAMOS,LOVE JOY,BUENO', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(177, 289, 126, 'GAGELONIA, MARIANO ACOSTA JR', '', '', 'GUINAPON,ELVIRA,DAYAG', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(178, 290, 126, 'GALAPON, JANUARD ESPIRITU', '', '', 'FORTO,JAMELLAH,SANTIAGO', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(179, 291, 126, 'GAURAN, JAYNARD MADRIAGA', '', '', 'OLIGO,JESSA,GRAGASIN', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(180, 292, 126, 'INERE, RALPH LAURENCE HERNANDEZ', '', '', 'RAMISCAL,JASMIN BLESSING,CABANERO', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(181, 293, 126, 'LOPEZ, JOEL DOMRIQUE', '', '', 'DUMAYAS,JULIE,VERAY', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(182, 294, 126, 'OKATCH, ADAMSON MIDEGA', '', '', 'OCTAVIANO,DARYL,MAGDAY', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(183, 295, 126, 'PALADAN, NOMER FLORES', '', '', 'NACION,JOYCE,SERRANO', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(184, 296, 126, 'RASPADO, JUNBOY PASTOR', '', '', 'DEL ROSARIO,IRISH NICOLE,CORPUZ', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(185, 297, 126, 'SOMERA, WARLITO BULALAYO', '', '', 'DOMINGO,DEBELYN,RUDIO', '', '', '', '', '', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(186, 298, 127, '', '', '', 'ALAMBRA,MELANIE,GASPAR', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(187, 299, 127, 'AQUILO, MARCIAL TABBU', '', '', 'COSTALES,GRACE,GALAPON', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(188, 300, 127, 'BALICAO, ROGER MARCOS', '', '', 'ACOB,JULIE BETH,PADUA', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(189, 301, 127, 'BALICAO, ROMMELSON MARCOS SR', '', '', 'MEJIA,EVERLYN,MAGDAY', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(190, 302, 127, 'DELA CRUZ, DENNIS ALAMBRA', '', '', 'PASCUA,MELODIA,PIANO', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(191, 303, 127, 'DELA CRUZ, WARLITO SAWIT JR', '', '', 'OPINION,CLAIRE,OBUNGEN', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(192, 304, 127, 'MAGNO, BELGION RAMOS', '', '', 'RAMOS,ANGELA,TASANI', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(193, 305, 127, 'MAGNO, BELGION RAMOS', '', '', 'RAMOS,ANGELA,TASANI', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(194, 306, 127, 'PARAISO, REYMAR POLICARPIO', '', '', 'BARTOLOME,EVANGELINE,MACAPULAY', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(195, 307, 127, 'PINEDA, FRANKLIN SENICA', '', '', 'SOLEDAD,KARLA MAE,DAQUIOAG', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(196, 308, 127, '', '', '', 'CIMATO,CLARISA,CALZADA', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(197, 309, 127, 'SOMERA, ROLANDO VICTORIO JR', '', '', 'ANTING,RUTCHEL,DIAZ', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(198, 310, 127, 'SOMERA, RICHARD VICTORIO', '', '', 'LAUD,EMMA,VELASCO', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(199, 311, 127, 'VALDEZ, ORLY ACOBA', '', '', 'SOBREPEÑA,LANILYN,VALDOZ', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(200, 312, 127, 'ANQUILLANO, ROGELIO BARROGA JR', '', '', 'GAURAN,JULIE,SANTOS', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(201, 313, 127, 'BAJACAN, RODOLFO DEL ROSARIO', '', '', 'DELA CRUZ,NORA,BAUTISTA', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(202, 314, 127, 'BALICAO, ROEL ANCHETA', '', '', 'AULO,GENEVIE,MARCOS', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(203, 315, 127, 'BONITA, REGIE BARUZO', '', '', 'TARIGA,GLAIZA,SEVILLA', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(204, 316, 127, 'BONITA, MARIO LAYUGAN', '', '', 'DOLORIDO,MARJORIE,ESTREMOS', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(205, 317, 127, 'CABUTUTAN, EDDIE VILLAROMAN', '', '', 'SALAZAR,LOREVEN,VIERNES', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(206, 318, 127, 'CASTRO, NOMER DELA CRUZ', '', '', 'RAMOS,MARITES,MADAYAG', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(207, 319, 127, 'CORPUZ, ROBERT SURAT', '', '', 'DELA CRUZ,JING-JING,BAUTISTA', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(208, 320, 127, 'DAGA, CESAR LIZARDO', '', '', 'PINEDA,NICOHL,SANTOS', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(209, 321, 127, 'DOMINGO, RODRIGO PALADAN JR', '', '', 'DALSON,VENUS,NAGANAG', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(210, 322, 127, 'FRANCISCO, RELIO MANABAT', '', '', 'DAGA,EVELYN,BAGUNO', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(211, 323, 127, 'LOPEZ, JOEL DUMRIQUE', '', '', 'DUMAYAS,JULIE,VERAY', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(212, 324, 127, 'MANIPON, FREDILY LASCOTA', '', '', 'FLORES,RHEDEL,DAWAGAN', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(213, 325, 127, 'MEDRANO, ARVIN CATARROJA', '', '', 'FORTUNA,VIVA,COLOMA', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(214, 326, 127, 'OCTAVIANO, KENNETH JAMES TONOG', '', '', 'PATTUGALAN,HAZEL,AQUINO', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(215, 327, 127, 'PALADAN, NOMER FLORES', '', '', 'NACION,JOYCE,SERRANO', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(216, 328, 127, 'PULIDO, MARVIC ANDULOY', '', '', 'ADRIOSULA,RENALYN,VERGINIZA', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(217, 329, 127, 'RAMISCAL, ANGELITO ORZAME JR', '', '', 'FRAGIO,STEPHANY,ASPREC', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(218, 330, 127, 'SAMONTE, ALLAN PIDA', '', '', 'GAMBOA,REMA,BUENO', '', '', '', '', '', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(219, 331, 128, 'ANTOLIN, RAMSES CORNITA', '', '', 'CUARESMA,MARIVIC,DELOS SANTOS', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(220, 332, 128, 'DOMINGO, JAMES GAMBOA', '', '', 'AGURIN,MYLA,ORIAL', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(221, 333, 128, 'GAGELONIA, ADRIANO ACOSTA', '', '', 'GALBANIO,MARIVICK,MENTANG', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(222, 334, 128, 'GASPAR, JOENEL GAMBOA', '', '', 'TEJADA,SILVIA,SAWIT', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(223, 335, 128, 'IGUAN, RUDY PABLO JR', '', '', 'BAGAMASPAD,REMA,AGATEP', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(224, 336, 128, 'RAMISCAL, KAISER DELA CRUZ', '', '', 'ALAVA,REMELYN,UGERIO', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(225, 337, 128, 'SANTOS, JOHN PAUL ALIPIO', '', '', 'LEYSA,APRIL JANE,DAILEG', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(226, 338, 128, 'SOMERA, NELSON ORERO', '', '', 'SANTIAGO,AGNES,VERA', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(227, 339, 128, 'TORRES, SANNY BOY -', '', '', 'BAUTISTA,JENELLA JOY,JOSE', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(228, 340, 128, '', '', '', 'BALMORES,MELODY,AGUILAR', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(229, 341, 128, 'BUSTAMANTE, JON GIOFFREY ANGELES', '', '', 'MALSI,KRISTEL,SANTIAGO', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(230, 342, 128, 'CASIMIRO, REYNALDO SANTIAGO JR', '', '', 'SAGUN,MARLYN,SEVILLANO', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(231, 343, 128, 'CATUBIG, REYMART ROSQUETA', '', '', 'PARIO,ALMA,COLOMA', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(232, 344, 128, 'DUQUE, LODRIGO OTTAAL JR', '', '', 'ASIS,MARICRIS,UNCIANO', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(233, 345, 128, 'GABALDON, JESUS PAULO JR', '', '', 'RAMISCAL,REGINE,ORZAME', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(234, 346, 128, 'PINEDA, FREDDIE SENICA', '', '', 'GAONA,JENNY,ESPIRITU', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(235, 347, 128, 'RAFAEL, HAROLD TORIBIO', '', '', 'BAGAMASPAD,LHENNIE JANE,AGATEP', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(236, 348, 128, 'RAMISCAL, ERWIN ORZAME', '', '', 'DELA CRUZ,JOCELYN,MANAHAN', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(237, 349, 128, 'VALDEZ, ORLY ACOBA', '', '', 'SOBREPEÑA,LANILYN,BALDOS', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(238, 350, 128, 'VILLAROMAN, SILVERIO OCTAVIANO', '', '', 'DELA CRUZ,MARY GRACE,SERRANO', '', '', '', '', '', '2026-09-30 12:57:26', '2026-09-30 12:57:26');

-- --------------------------------------------------------

--
-- Table structure for table `reading_levels`
--

CREATE TABLE `reading_levels` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `reading_level` enum('Non-reader','Frustration','Instructional','Independent') NOT NULL,
  `reading_language` enum('English','Filipino','MTB') NOT NULL,
  `assessment_date` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school_settings`
--

CREATE TABLE `school_settings` (
  `id` int(11) NOT NULL DEFAULT 1,
  `school_name` varchar(150) NOT NULL DEFAULT 'San Jose Sur Elementary',
  `school_id` varchar(20) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `division` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `school_settings`
--

INSERT INTO `school_settings` (`id`, `school_name`, `school_id`, `region`, `division`, `district`, `updated_by`, `updated_at`) VALUES
(1, 'San Jose Sur Elementary', '103503', 'Region II', 'Division of Isabela', 'District of Mallig', NULL, '2026-08-15 15:07:39');

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
(8, '2026-2027', '2026-06-08', '2027-04-05', 'active', '2026-05-15 05:40:37', '2026-08-15 12:50:34'),
(14, '2027-2028', '2027-06-01', '2028-04-01', 'inactive', '2026-07-09 15:17:37', '2026-08-22 14:51:05');

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
(11, 8, 'Andres Bonifacio', 120, '2026-07-23 15:58:59', '2026-07-23 15:58:59'),
(15, 5, 'Yellow', 121, '2026-09-29 12:27:21', '2026-09-29 12:27:21'),
(16, 12, 'Emerald Green', 122, '2026-09-29 12:58:33', '2026-09-29 12:58:33'),
(17, 5, 'Purple', 123, '2026-09-30 12:23:12', '2026-09-30 12:23:12'),
(18, 6, 'Red', 124, '2026-09-30 12:23:29', '2026-09-30 12:23:29'),
(19, 8, 'Royal Blue', 125, '2026-09-30 12:23:57', '2026-09-30 12:23:57'),
(20, 13, 'Baby-Pink', 126, '2026-09-30 12:31:49', '2026-09-30 12:31:49'),
(21, 11, 'FUCHSIA-PINK', 127, '2026-09-30 12:33:04', '2026-09-30 12:33:04'),
(22, 14, 'APPLE-GREEN-1', 128, '2026-09-30 12:34:15', '2026-09-30 12:34:15');

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
(11, 11, 120, 8, '2026-07-23 15:58:59', '2026-07-23 15:58:59'),
(17, 15, 121, 8, '2026-09-29 12:27:21', '2026-09-29 12:27:21'),
(18, 16, 122, 8, '2026-09-29 12:58:33', '2026-09-29 12:58:33'),
(19, 17, 123, 8, '2026-09-30 12:23:12', '2026-09-30 12:23:12'),
(20, 18, 124, 8, '2026-09-30 12:23:29', '2026-09-30 12:23:29'),
(21, 19, 125, 8, '2026-09-30 12:23:57', '2026-09-30 12:23:57'),
(22, 20, 126, 8, '2026-09-30 12:31:49', '2026-09-30 12:31:49'),
(23, 21, 127, 8, '2026-09-30 12:33:04', '2026-09-30 12:33:04'),
(24, 22, 128, 8, '2026-09-30 12:34:15', '2026-09-30 12:34:15');

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
  `age_as_of_june` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Age as of the 1st Friday of June',
  `gender` enum('Male','Female') NOT NULL,
  `mother_tongue` varchar(50) DEFAULT NULL COMMENT 'Grade 1 to Grade 3 only',
  `ip_ethnic_group` varchar(100) DEFAULT NULL COMMENT 'Indigenous People / ethnic group',
  `religion` varchar(100) DEFAULT NULL,
  `address` varchar(50) DEFAULT NULL,
  `house_number` varchar(20) DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `sitio` varchar(100) DEFAULT NULL,
  `purok` varchar(100) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `city_municipality` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `learning_modality` varchar(50) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
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

INSERT INTO `students` (`id`, `lrn`, `first_name`, `middle_name`, `last_name`, `suffix`, `birth_date`, `age_as_of_june`, `gender`, `mother_tongue`, `ip_ethnic_group`, `religion`, `address`, `house_number`, `street`, `sitio`, `purok`, `barangay`, `city_municipality`, `province`, `learning_modality`, `remarks`, `school_year_id`, `grade_level_id`, `section_id`, `recorded_by`, `status`, `created_at`, `updated_at`) VALUES
(149, '123456789012', 'Juan', 'Santos', 'Dela Cruz', NULL, '2018-06-15', 7, 'Male', 'Tagalog', 'Ilocano', 'Roman Catholic', NULL, '123', 'Rizal St.', 'Sitio Malaya', 'Purok 3', 'San Jose Sur', 'Rosario', 'Batangas', 'Face-to-Face', NULL, 8, 5, 2, 116, 'active', '2026-09-23 06:57:59', '2026-09-23 06:57:59'),
(150, '103503250003', 'LIAM ARVIL', 'HERNAL', 'BALILA', NULL, '2019-10-09', 6, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(151, '103503240003', 'JUSTINE', 'REYES', 'BALINAGAY', NULL, '2018-10-16', 7, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(152, '103503250004', 'JARED', 'CASTILLO', 'BULATAO', NULL, '2020-03-16', 6, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(153, '103503250005', 'USHER', 'BARTOLOME', 'CARIASO', NULL, '2020-08-23', 6, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(154, '103503250007', 'ANGELO', 'COLLADO', 'GAGELONIA', NULL, '2020-03-23', 6, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(155, '103503250009', 'ELMARK', 'ALIPIO', 'GAMBOA', NULL, '2020-10-31', 5, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(156, '103503250010', 'KHERWIN', 'FLORES', 'GEBAÑA', NULL, '2020-05-19', 6, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(157, '103503250015', 'KURT ETHAN', 'BONITA', 'JULIANO', NULL, '2020-07-16', 6, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(158, '103503250017', 'HANZ', 'BAGAMASPAD', 'RAFAEL', NULL, '2019-10-28', 6, 'Male', 'Ilocano', 'Tingguian / Itneg', 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE NORTE I', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(159, '103503250020', 'REYMOND', 'MAURO', 'SOBREPEÑA', NULL, '2019-11-01', 6, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(160, '103503250021', 'RAIZA', 'PARAISO', 'ABALOS', NULL, '2020-02-02', 6, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(161, '103503250023', 'KATE ZIA', 'ELARDO', 'BAUTISTA', NULL, '2020-09-27', 5, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(162, '103503250024', 'AZALEA BELLE', 'GANIR', 'BUENO', NULL, '2020-05-04', 6, 'Female', 'English', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(163, '103503250025', 'LADY JOY', 'HELARDO', 'BUNAO', NULL, '2020-02-08', 6, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(164, '103503250029', 'PRINCESS HEART', 'LOPEZ', 'LABE', NULL, '2020-05-27', 6, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(165, '103503250031', 'HASIAH KATE', 'PATTUGALAN', 'OCTAVIANO', NULL, '2019-10-23', 6, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(166, '103503250032', 'GRACIANE', 'VIERNES', 'RAMOS', NULL, '2020-06-20', 6, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(167, '103503250033', 'QUINN FIELA ICYLLE', 'BESA', 'RAMOS', NULL, '2019-12-20', 6, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(168, '103503250037', 'SAFHIERA GRACE', 'SANTIAGO', 'SOMERA', NULL, '2020-05-18', 6, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 15, 121, 'active', '2026-09-29 12:33:25', '2026-09-29 12:33:25'),
(169, '103503190001', 'AERON JADE', 'PARAISO', 'ABALOS', NULL, '2013-10-18', 12, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(170, '103503170032', 'SANDY', 'JR JOVILLANOS', 'ALVARADO', NULL, '2010-04-30', 16, 'Male', 'Iloko', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(171, '103503200002', 'JOHN LESTER', 'GASPAR', 'CASTILLO', NULL, '2015-02-06', 11, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(172, '103503190007', 'ROBERT', 'JR DELA CRUZ', 'CORPUZ', NULL, '2013-10-29', 12, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(173, '103502200020', 'XYDER BRYLLE', 'FLORES', 'FELIX', NULL, '2015-08-27', 11, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'BANTUG', 'UMINGAN', 'PANGASINAN', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(174, '101287200008', 'PRINCE JOVIT', 'FERRER', 'MENOR', NULL, '2014-10-15', 11, 'Male', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'BUAYAEN', 'BAYAMBANG', 'PANGASINAN', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(175, '103503200004', 'MARK IVAN', 'BARTOLOME', 'PARAISO', NULL, '2014-09-11', 11, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(176, '103503200006', 'LEO', 'SOBREPENA', 'VALDEZ', NULL, '2014-09-02', 12, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'RANG-AYAN', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(177, '103503190019', 'ALTHEA', 'JOVILLANOS', 'ALVARADO', NULL, '2014-01-15', 12, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(178, '103503200008', 'MATHEA VENHIZE', 'GIRAO', 'ANCHETA', NULL, '2014-11-19', 11, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(179, '103503200029', 'KRISA MAE', 'BACCAY', 'ANTALAN', NULL, '2015-05-09', 10, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(180, '103503200009', 'HANNAH', 'COSTALES', 'AQUILO', NULL, '2014-11-28', 11, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(181, '103503200010', 'KENDRA MEGHAN', 'AULO', 'BALICAO', NULL, '2015-09-08', 11, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(182, '103503200011', 'ROXANE', 'SANTUYO', 'BONITA', NULL, '2014-11-24', 11, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(183, '103503200012', 'ANGELA', 'OCTAVIANO', 'BUENO', NULL, '2014-12-15', 11, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(184, '103503200030', 'MARIA LOURDES', 'ARETAÑO', 'CAPANANG', NULL, '2015-08-23', 11, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(185, '103503200013', 'HYACINTH', 'RAMISCAL', 'DAGA', NULL, '2014-10-20', 11, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(186, '103503200014', 'CLARISA', 'CACHO', 'DOMINGO', NULL, '2014-12-18', 11, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(187, '103503200015', 'GIFT', '-', 'FERRER', NULL, '2015-08-29', 10, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(188, '159011200045', 'PRINCESS JUNELLA', 'ALIPIO', 'FLORENTINO', NULL, '2015-06-18', 11, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'TAYABO', 'SAN JOSE CITY', 'NUEVA ECIJA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(189, '103503200016', 'YSANG', 'BALA', 'GASPAR', NULL, '2015-01-03', 11, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(190, '103503200017', 'RHYCELLE KEITH', 'GALINDEZ', 'GONGORA', NULL, '2015-05-21', 11, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(191, '103503200018', 'STRAWBERRY', 'ALCANTARA', 'GUIANG', NULL, '2015-08-09', 10, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(192, '103503200019', 'ALIZA MAE', 'MACADANGDANG', 'JULIANO', NULL, '2015-02-25', 11, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(193, '103503200020', 'QUEENNE', 'BERDANDINO', 'OCTAVIANO', NULL, '2015-04-14', 11, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(194, '103503200021', 'JOMELLA MAE', 'CUEVAS', 'OPERIO', NULL, '2014-01-12', 11, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(195, '103503200022', 'QUEEN ISABELLE', 'GAONA', 'PINEDA', NULL, '2014-01-10', 11, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 12, 16, 122, 'active', '2026-09-29 12:58:57', '2026-09-29 12:58:57'),
(196, '136413200039', 'AVEY ARRIANA', 'VALLES', 'RAMISCAL', NULL, '2015-07-30', 11, 'Female', 'Tagalog', 'Kankanaey / Kankanaey Ibenguet / Kankanaey Iyaplay', 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:58', '2026-09-29 12:58:58'),
(197, '105614200148', 'ERICH JAZ', 'DELA CRUZ', 'RAMISCAL', NULL, '2015-06-17', 11, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'POBLACION I', 'PEÑARANDA', 'NUEVA ECIJA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:58', '2026-09-29 12:58:58'),
(198, '103503200024', 'JAYNE', 'FLORES', 'REGINIO', NULL, '2014-09-27', 11, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:58', '2026-09-29 12:58:58'),
(199, '103503200025', 'HEZKIAH ELINAIA', 'TOMAS', 'RELLORTA', NULL, '2014-09-11', 11, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:58', '2026-09-29 12:58:58'),
(200, '103503200027', 'PRINCESS', 'VALDEZ', 'SOBREPEÑA', NULL, '2015-11-02', 11, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:58', '2026-09-29 12:58:58'),
(201, '103503200028', 'ASHLEY ZIAH', 'ABRIAM', 'SOMERA', NULL, '2015-07-27', 11, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:58', '2026-09-29 12:58:58'),
(202, '103503240001', 'EXCY', 'PARAISO', 'ABALOS', NULL, '2018-10-16', 7, 'Male', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 5, 17, 123, 'active', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(203, '103503250001', 'AYDEN LEIGH', 'RAMOS', 'AGREGADO', NULL, '2020-07-19', 6, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 17, 123, 'active', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(204, '103503250006', 'PRINCE YHUHAN', 'MATIAS', 'CERA', NULL, '2020-06-23', 6, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 5, 17, 123, 'active', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(205, '103503250008', 'JOHN WILLIAM', 'FORTO', 'GALAPON', NULL, '2020-06-22', 6, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 17, 123, 'active', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(206, '103503250011', 'KENTH DAVE', 'GALINDEZ', 'GONGORA', NULL, '2020-07-12', 6, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 17, 123, 'active', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(207, '103503250012', 'NATHANIEL', 'GASPAR', 'ITLIONG', NULL, '2019-10-03', 6, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 17, 123, 'active', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(208, '103503250013', 'ROGEN DAVE', 'ANTING', 'JOSE', NULL, '2019-11-24', 6, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 5, 17, 123, 'active', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(209, '103503250016', 'MARK RAINIEL', 'FLORES', 'NUDO', NULL, '2020-05-10', 6, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 17, 123, 'active', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(210, '103503250018', 'JOHN JHAMES', 'MAPILI', 'RAMISCAL', NULL, '2020-05-21', 6, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 17, 123, 'active', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(211, '103503250019', 'SEAN XAVIER', 'MASIDDO', 'RAMOS', NULL, '2020-03-14', 6, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 17, 123, 'active', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(212, '103503250022', 'MARTYELLA LUCIA', 'GIRAO', 'ANCHETA', NULL, '2019-12-26', 6, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 17, 123, 'active', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(213, '103503250026', 'DANISHA LUREN', 'CERA', 'CABENIAN', NULL, '2019-12-03', 6, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 17, 123, 'active', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(214, '103503250030', 'LEANNE', 'LAPITAN', 'LARDIZABAL', NULL, '2019-11-13', 6, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 17, 123, 'active', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(215, '103503250034', 'CLARISSE JOY', 'DEL ROSARIO', 'RASPADO', NULL, '2020-10-15', 5, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 17, 123, 'active', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(216, '103503250036', 'HADIYA NATHALIE', 'ABRIAM', 'SOMERA', NULL, '2020-08-02', 6, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 5, 17, 123, 'active', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(217, '103503250038', 'VHIA ANGELIE', 'COLLADO', 'SOMERA', NULL, '2020-01-07', 6, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 5, 17, 123, 'active', '2026-09-30 12:43:57', '2026-09-30 12:43:57'),
(218, '103503240002', 'MARK KENNETH', 'RAMISCAL', 'BALAGAN', NULL, '2019-03-13', 7, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(219, '103501240003', 'JHIAN JHAIRUZ', 'MANAGUELOD', 'BENEDICTO', NULL, '2019-08-20', 7, 'Male', 'Ilocano', 'Ibanag / Ybanag / Iabanag', 'Christianity', NULL, NULL, NULL, NULL, NULL, 'RANG-AYAN', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD \nT/I DATE:2026-06-08', 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(220, '103503240004', 'EZEKIEL', 'TARIGA', 'BONITA', NULL, '2019-08-15', 7, 'Male', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(221, '103503240005', 'KEYFER', 'UBALDE', 'CARIAGA', NULL, '2018-12-25', 7, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(222, '103503240006', 'PRINCE KHIAN', 'MONONG', 'CORPUZ', NULL, '2019-08-02', 7, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(223, '103503240007', 'PRINCE ZACHARY', 'GUINAPON', 'GAGELONIA', NULL, '2018-11-22', 7, 'Male', 'Ilocano', 'Kalinga', 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(224, '103503240008', 'DANIEL MARC', 'CARLOS', 'IBAÑEZ', NULL, '2019-08-27', 7, 'Male', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(225, '103503230026', 'RAYMOND', 'JR TABADA', 'LAUD', NULL, '2018-10-19', 7, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(226, '103503240009', 'KING ZION', 'BENEDICTO', 'MAGCALAS', NULL, '2019-07-28', 7, 'Male', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(227, '103503240010', 'JOHN DENIEL', 'DELA CRUZ', 'PALAFOX', NULL, '2019-07-13', 7, 'Male', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(228, '103503240011', 'ANGELITO', 'III FRAGIO', 'RAMISCAL', NULL, '2018-12-01', 7, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(229, '103503240012', 'JHONDER DAVE', 'TABADA', 'SOBREPEÑA', NULL, '2019-06-29', 7, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(230, '103503240013', 'RICO', 'LAUD', 'SOMERA', NULL, '2018-12-10', 7, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(231, '103501240013', 'WILLIAM MIGUEL', 'RESPICIO', 'VALDEZ', NULL, '2019-04-05', 7, 'Male', 'Tagalog', 'Ibanag / Ybanag / Iabanag', 'Christianity', NULL, NULL, NULL, NULL, NULL, 'RANG-AYAN', 'MALLIG', 'ISABELA', 'Face-to-Face', 'T/I DATE:2026-06-08', 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(232, '400548240015', 'DYLAN NICOLAI', 'VICENTE', 'VILLAROMAN', NULL, '2018-11-06', 7, 'Male', 'English', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(233, '103503240014', 'THRIXZHIA FLEIGH', 'GAGELONIA', 'BALUGA', NULL, '2019-03-20', 7, 'Female', 'Ilocano', 'Kalinga', 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(234, '103503240015', 'NATHALIA MARIE', 'DOCTOR', 'CORPUZ', NULL, '2019-05-07', 7, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(235, '103503240017', 'JASMIN MARIE', 'RAMOS', 'ESTIGOY', NULL, '2019-06-18', 7, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(236, '103503240018', 'SHANELLE KRION', 'GAGELONIA', 'FERRER', NULL, '2018-12-17', 7, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(237, '103503230020', 'CRISHELA', 'DAGA', 'FRANCISCO', NULL, '2018-07-06', 8, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(238, '103503240019', 'JAMEELA AVERI', 'RAMISCAL', 'GUEVARRA', NULL, '2019-05-18', 7, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(239, '103503240020', 'PRINCESS JOY', 'TABADA', 'LAUD', NULL, '2018-11-21', 7, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(240, '103503240021', 'ZEMIRAH', 'ANCHETA', 'NATIVIDAD', NULL, '2019-07-22', 7, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 6, 18, 124, 'active', '2026-09-30 12:51:51', '2026-09-30 12:51:51'),
(241, '103503230002', 'CHESTENVER', 'LOTTO', 'CERA', NULL, '2017-12-25', 8, 'Male', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(242, '103186230007', 'MENARD JAY', 'SICAM', 'CREDO', NULL, '2018-07-17', 8, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(243, '103503230003', 'PRINCE CHANG-AY', '-', 'DAGA', NULL, '2018-03-04', 8, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(244, '103503230004', 'MIKAEL', 'GABAY', 'DUMAYAS', NULL, '2018-03-18', 8, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(245, '103503230005', 'NATHANIEL', '-', 'FAUSTINO', NULL, '2018-09-06', 7, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(246, '103503230006', 'ERL JHAY', 'DELA CRUZ', 'GAMBOA', NULL, '2018-03-20', 8, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(247, '103503230007', 'LHERVIN DALE', 'FLORES', 'GEBAÑA', NULL, '2017-09-17', 8, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(248, '103503220006', 'TRISTAN KURT', 'CATAMEN', 'GOMGOM-O', NULL, '2016-06-23', 10, 'Male', 'Ilocano', 'Kalinga', 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(249, '103503230008', 'KURT HENRY', 'GALINDEZ', 'GONGORA', NULL, '2018-01-29', 8, 'Male', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(250, '103503230009', 'MARK ANGELO RON', 'PINEDA', 'GUMIRAN', NULL, '2018-04-17', 8, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(251, '103503230010', 'HEZIKIEL EPHRAIM', 'TOMAS', 'RELLORTA', NULL, '2018-08-27', 8, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(252, '102639230010', 'PRINCE ZEDRICK', 'HERNAL', 'ROMERO', NULL, '2018-10-04', 7, 'Male', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(253, '103503230011', 'PRINCE HENRY', 'OLIGO', 'SOMERA', NULL, '2018-02-26', 8, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(254, '103503230027', 'JOLINA', 'PALOMA', 'ANTONIO', NULL, '2017-07-06', 9, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(255, '103503230012', 'PRINCESS JHAYBEL', 'GANATICE', 'BALINAGAY', NULL, '2018-08-23', 8, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(256, '103503230013', 'CADEN AICA', 'DELA CRUZ', 'BALINTAG', NULL, '2018-06-10', 8, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(257, '103503230014', 'SOFIA', 'DOLORIDO', 'BONITA', NULL, '2017-11-20', 8, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(258, '103503230015', 'GIA BIANCA', 'CABUSAO', 'BUENO', NULL, '2017-11-28', 8, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(259, '103503230016', 'ALTHEA JANINE', 'VIERNES', 'CAPANANG', NULL, '2017-10-24', 8, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(260, '103503230017', 'ALPHA GRACE', 'BALA', 'CORPUZ', NULL, '2017-12-28', 8, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(261, '103503230018', 'JAIRA', 'GIL', 'DE LEON', NULL, '2018-08-07', 8, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(262, '103503230019', 'QUIM AVISHA', 'DAMASCO', 'FERRER', NULL, '2018-04-03', 8, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(263, '103503220022', 'RHEALENE', 'FERRER', 'JOSE', NULL, '2017-09-14', 8, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(264, '103503230021', 'CRISANTA', 'GABUYA', 'JULIANO', NULL, '2017-12-25', 8, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(265, '103503230022', 'PRINCESS ARIANNA', 'CAPILI', 'MINA', NULL, '2018-08-01', 8, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(266, '103503230023', 'ELIZABETH', 'BERDANDINO', 'OCTAVIANO', NULL, '2018-04-22', 8, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(267, '103503220026', 'ANGELICA', 'GAONA', 'PINEDA', NULL, '2016-11-18', 9, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(268, '103503220028', 'MARY GRACE', 'VIERNES', 'RAMOS', NULL, '2017-05-03', 9, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(269, '103503230024', 'QUEEN JARIBEL', 'FERRER', 'RAMOS', NULL, '2018-04-20', 8, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(270, '103503230025', 'ELAISA JANE', 'ABRIAM', 'SOMERA', NULL, '2018-09-24', 7, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 8, 19, 125, 'active', '2026-09-30 12:53:26', '2026-09-30 12:53:26'),
(271, '103503220001', 'CLYDE LOWIS', 'PASTOR', 'BALILA', NULL, '2017-07-11', 9, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(272, '103503220002', 'TRISTAN JAY', 'EUGENIO', 'BUNAO', NULL, '2016-12-10', 9, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(273, '103503220003', 'JAMES KYRIE', 'COLOMA', 'DE GUZMAN', NULL, '2017-04-07', 9, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(274, '103503220004', 'RHODCH ALDRICH', 'DELOS SANTOS', 'GALAPON', NULL, '2017-08-01', 9, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(275, '103203220008', 'FRANZ KYLE', 'CASTIL', 'GAMBOA', NULL, '2017-07-05', 9, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'GURIBANG', 'DIFFUN', 'QUIRINO', 'Face-to-Face', 'LWD \nT/I DATE:2026-06-10', 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(276, '103503220005', 'CEZAR JADE', '-', 'GASPAR', NULL, '2016-11-20', 9, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(277, '103978220032', 'JULIUS NYGEL', 'BUENO', 'GOMBIO', NULL, '2017-07-09', 9, 'Male', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN FERNANDO', 'BAMBANG', 'NUEVA VIZCAYA', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(278, '103503220007', 'JOHN PHILIP', 'GAVINO', 'JOSE', NULL, '2016-07-11', 10, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(279, '103503220008', 'JHAM KHENDRIX', 'ADVENCULA', 'MARCOS', NULL, '2016-11-02', 9, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(280, '108077220116', 'JOHN CYRILL', 'BACCAY', 'MENDIORO', NULL, '2017-03-17', 9, 'Male', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SABANG', 'NAIC', 'CAVITE', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(281, '103503220009', 'SRI PHILIP', 'FAUSTINO', 'NALLAWARIGE', NULL, '2017-06-30', 9, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(282, '103503220010', 'VINCE ANDY', 'PILAR', 'OCTAVIANO', NULL, '2017-01-19', 9, 'Male', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(283, '103503220011', 'JHON KENNETH', 'GUZMAN', 'PINEDA', NULL, '2017-02-16', 9, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(284, '103503220013', 'ANGEL JOYCE', 'SISON', 'ALEJO', NULL, '2017-09-20', 8, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(285, '103503220014', 'CLOUDEN JANE', 'DELA CRUZ', 'BAUTISTA', NULL, '2017-03-22', 9, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(286, '103503220015', 'SCARLET', 'OCTAVIANO', 'BUENO', NULL, '2017-10-24', 8, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(287, '103503220016', 'KEARRA SOPHIA', 'LICUBEN', 'ERCILLA', NULL, '2016-12-29', 9, 'Female', 'Ilocano', 'Kalinga', 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(288, '103503220017', 'JULIANA MARIE', 'RAMOS', 'ESTIGOY', NULL, '2017-02-15', 9, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(289, '103503220018', 'ALEIGHSHA BRAYE', 'GUINAPON', 'GAGELONIA', NULL, '2016-12-29', 9, 'Female', 'Ilocano', 'Kalinga', 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(290, '103503220019', 'TIFFANY GWENAELLE', 'FORTO', 'GALAPON', NULL, '2017-09-22', 8, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(291, '103503220020', 'ARYANA', 'OLIGO', 'GAURAN', NULL, '2017-01-15', 9, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(292, '103503220021', 'RAJAH ALEXA', 'RAMISCAL', 'INERE', NULL, '2016-12-07', 9, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(293, '103503220023', 'JANICE', 'DUMAYAS', 'LOPEZ', NULL, '2016-12-19', 9, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(294, '103503220024', 'ISABELA CLARA', 'OCTAVIANO', 'OKATCH', NULL, '2017-02-11', 9, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(295, '103503220025', 'GIRLY', 'NACION', 'PALADAN', NULL, '2017-06-02', 9, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD \nCCT', 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(296, '103503220029', 'SOPHIA CLAIRE', 'DEL ROSARIO', 'RASPADO', NULL, '2017-07-08', 9, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(297, '136176220013', 'RHEA', 'DOMINGO', 'SOMERA', NULL, '2017-08-01', 9, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'NANENG', 'CITY OF TABUK (Capital)', 'KALINGA', 'Face-to-Face', 'LWD \nCCT', 8, 13, 20, 126, 'active', '2026-09-30 12:55:25', '2026-09-30 12:55:25'),
(298, '103503210001', 'JHON PAUL', '-', 'ALAMBRA', NULL, '2016-07-21', 10, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(299, '103503210002', 'DANIEL', 'COSTALES', 'AQUILO', NULL, '2016-04-04', 10, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(300, '103503210003', 'MIKE GEURON', 'ACOB', 'BALICAO', NULL, '2016-01-26', 10, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE NORTE I', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(301, '103503210004', 'ROMMELSON', 'JR MEJIA', 'BALICAO', NULL, '2016-02-26', 10, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(302, '103503210006', 'KING DENNIS', 'PASCUA', 'DELA CRUZ', NULL, '2016-03-07', 10, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(303, '135292210003', 'NYLE OXIE BRYANT', 'OPINION', 'DELA CRUZ', NULL, '2016-10-15', 9, 'Male', 'Iloko', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SANTA MARIA', 'FLORA', 'APAYAO', 'Face-to-Face', 'Pending TI', 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(304, '103503180016', 'FERNANDO JOSE', 'RAMOS', 'MAGNO', NULL, '2012-12-22', 13, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD \nCCT', 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(305, '103503180015', 'LUIS FERNANDO', 'RAMOS', 'MAGNO', NULL, '2012-12-22', 13, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(306, '103503210030', 'MIKE ANGELO', 'BARTOLOME', 'PARAISO', NULL, '2016-06-20', 10, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(307, '103503210007', 'HECTOR LIAM ACHILLES', 'SOLEDAD', 'PINEDA', NULL, '2016-10-10', 9, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(308, '400488210016', 'RHONELSON', 'CIMATO', 'SALES', NULL, '2016-05-22', 10, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SARANAY', 'ANGADANAN', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(309, '103503210008', 'FRANCIS KYLE', 'ANTING', 'SOMERA', NULL, '2015-08-30', 10, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD \nCCT', 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(310, '103503210009', 'RICKY', 'LAUD', 'SOMERA', NULL, '2016-03-16', 10, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(311, '103501210011', 'JANRY', 'SOBREPEÑA', 'VALDEZ', NULL, '2016-01-05', 10, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(312, '103503210010', 'JAIRA', 'GAURAN', 'ANQUILLANO', NULL, '2016-02-13', 10, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(313, '103503210011', 'ANGEL', 'DELA CRUZ', 'BAJACAN', NULL, '2016-08-10', 10, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(314, '103503210012', 'LUCKYZIAH', 'AULO', 'BALICAO', NULL, '2016-09-22', 9, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(315, '103503210013', 'EZRA', 'TARIGA', 'BONITA', NULL, '2016-01-05', 10, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(316, '103503210014', 'REAH MAE', 'DOLORIDO', 'BONITA', NULL, '2015-10-18', 10, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(317, '103503210015', 'PRINCESS', 'SALAZAR', 'CABUTUTAN', NULL, '2015-12-26', 10, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(318, '103634210019', 'IRIS NHOMARIE', 'RAMOS', 'CASTRO', NULL, '2016-07-27', 10, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'RANG-AYAN', 'ROXAS', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(319, '103503210016', 'RENALYN JOY', 'DELA CRUZ', 'CORPUZ', NULL, '2015-11-24', 10, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(320, '103503210017', 'SHIEKA HESSA', 'PINEDA', 'DAGA', NULL, '2015-09-08', 10, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(321, '103503210018', 'ABRIL MAYUMI', '-', 'DOMINGO', NULL, '2016-04-26', 10, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(322, '128923210080', 'PRISCELLA', 'DAGA', 'FRANCISCO', NULL, '2016-05-20', 10, 'Female', 'Cebuano / Sinugbuanong Binisay', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14');
INSERT INTO `students` (`id`, `lrn`, `first_name`, `middle_name`, `last_name`, `suffix`, `birth_date`, `age_as_of_june`, `gender`, `mother_tongue`, `ip_ethnic_group`, `religion`, `address`, `house_number`, `street`, `sitio`, `purok`, `barangay`, `city_municipality`, `province`, `learning_modality`, `remarks`, `school_year_id`, `grade_level_id`, `section_id`, `recorded_by`, `status`, `created_at`, `updated_at`) VALUES
(323, '103503210021', 'JENIPHER', 'DUMAYAS', 'LOPEZ', NULL, '2015-11-11', 10, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(324, '221009210001', 'JANINE', 'FLORES', 'MANIPON', NULL, '2016-06-04', 10, 'Female', 'Iloko', 'Kalinga', 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SIMIMBAAN', 'ROXAS', 'ISABELA', 'Face-to-Face', 'Pending TI', 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(325, '136524210397', 'VIA ANGELINE', 'FORTUNA', 'MEDRANO', NULL, '2016-09-19', 9, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(326, '103503210023', 'HANNAH KATE', 'PATTUGALAN', 'OCTAVIANO', NULL, '2016-02-15', 10, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(327, '103503210024', 'NICOLE', 'NACION', 'PALADAN', NULL, '2016-02-16', 10, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(328, '103496210021', 'MARRIANE JADE', 'ADRIOSULA', 'PULIDO', NULL, '2015-10-13', 10, 'Female', 'Filipino', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'LEONARDA', 'TUGUEGARAO CITY(Capital)', 'CAGAYAN', 'Face-to-Face', 'Pending TI', 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(329, '103503210027', 'ANGELICA QUIN', 'FRAGIO', 'RAMISCAL', NULL, '2016-03-29', 10, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(330, '103503210028', 'RHEMALYN', 'GAMBOA', 'SAMONTE', NULL, '2016-01-10', 10, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 11, 21, 127, 'active', '2026-09-30 12:56:14', '2026-09-30 12:56:14'),
(331, '103503260001', 'JEHORAM', 'CUARESMA', 'ANTOLIN', NULL, '2020-11-09', 5, 'Male', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(332, '103503260002', 'KING JAMEEL', 'AGURIN', 'DOMINGO', NULL, '2021-08-02', 5, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(333, '103503260003', 'CHRISTIAN', 'GALBANIO', 'GAGELONIA', NULL, '2021-10-05', 4, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(334, '103503260004', 'AYDEN EZEKIEL', 'TEJADA', 'GASPAR', NULL, '2021-08-28', 5, 'Male', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(335, '103503260005', 'RAMSEY RYU', 'BAGAMASPAD', 'IGUAN', NULL, '2021-04-28', 5, 'Male', 'Tagalog', 'Ayangan', 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE NORTE II', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(336, '103503260006', 'KAISER', 'JR ALAVA', 'RAMISCAL', NULL, '2021-05-15', 5, 'Male', 'English', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(337, '103503260007', 'PRINCE JHON', 'LEYSA', 'SANTOS', NULL, '2021-05-04', 5, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(338, '103503260008', 'CHRIS CARLSON', 'SANTIAGO', 'SOMERA', NULL, '2021-09-10', 4, 'Male', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(339, '103503260009', 'CARL ESTEVEN', 'BAUTISTA', 'TORRES', NULL, '2020-12-17', 5, 'Male', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(340, '103503260010', 'ANGEL MAY', '-', 'BALMORES', NULL, '2021-07-06', 5, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'VICTORIA', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(341, '103503260011', 'GLORIENNE ESTELLE', 'MALSI', 'BUSTAMANTE', NULL, '2021-06-05', 5, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(342, '103503260012', 'PRINCESS REYN', 'SAGUN', 'CASIMIRO', NULL, '2021-06-07', 5, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(343, '103503260013', 'BRIANNA', 'PARIO', 'CATUBIG', NULL, '2021-09-09', 4, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(344, '103503260014', 'SOPHIA LORIS', 'ASIS', 'DUQUE', NULL, '2020-11-23', 5, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(345, '103503260015', 'ZYRELLE JUZH', 'RAMISCAL', 'GABALDON', NULL, '2020-11-10', 5, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(346, '103503260016', 'PRECIOUS KIM', 'GAONA', 'PINEDA', NULL, '2021-01-27', 5, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(347, '103503260017', 'HARLHEN', 'BAGAMASPAD', 'RAFAEL', NULL, '2021-10-13', 4, 'Female', 'Tagalog', 'Tingguian / Itneg', 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE NORTE II', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(348, '103503260018', 'ERLYN JADE', 'DELA CRUZ', 'RAMISCAL', NULL, '2021-04-18', 5, 'Female', 'Tagalog', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(349, '103503260019', 'OLIVE', 'SOBREPEÑA', 'VALDEZ', NULL, '2020-11-25', 5, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', 'LWD', 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26'),
(350, '103503260020', 'SCARLET', 'DELA CRUZ', 'VILLAROMAN', NULL, '2021-07-29', 5, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 14, 22, 128, 'active', '2026-09-30 12:57:26', '2026-09-30 12:57:26');

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
(119, 'Teacher Two', 'teacher2@school.edu.ph', '$2y$10$.m5KtR/BU72SUnvWAi91k.mdgdvM8UPfRShh0EzxpEbdJYmvjpfAm', 'teacher', 'active', NULL, '2026-07-23', '0000-00-00'),
(120, 'April Berbon', 'april@gmail.com', '$2y$10$v0v1QI1cenFT2xRhXqWvOOswx77JVEMv1xStVaEFcALZEGL/FUfp2', 'teacher', 'active', NULL, '2026-07-23', '0000-00-00'),
(121, 'REMA BAGAMASPAD IGUAN', 'remaiguan@gmail.com', '$2y$10$upp5mcz9Hh.2KmTY2XlCFubQHsFCw/hPNJm7jN1/neo0vkImja7f2', 'teacher', 'active', NULL, '2026-09-29', '0000-00-00'),
(122, 'MARICEL MANUEL BESA', 'maricelbesa@gmail.com', '$2y$10$aO1HD3ElgbeoWCk/0IQadu3eTQQZ33xOFUCqKyTCi1G1GfdV2rChm', 'teacher', 'active', NULL, '2026-09-29', '0000-00-00'),
(123, 'CATHERINE VALDEZ MALLILLIN', 'cathericemallillin@gmail.com', '$2y$10$YJzSOSz2yibxpkH2RjecAOTcs1k6DYtcS7Wlh8z4BtWp4vcLbbwl.', 'teacher', 'active', NULL, '2026-09-30', '0000-00-00'),
(124, 'LOIDA SANTIAGO BALILA', 'loidabalila@gmail.com', '$2y$10$G1C3BQo53.q39qXTv4Ip1uVMmcKCaLApVehUl8/./FXovQRMCb.W6', 'teacher', 'active', NULL, '2026-09-30', '0000-00-00'),
(125, 'JENNIFER MILAN BESA', 'jenifferbesa@gmail.com', '$2y$10$rTHq1CR49hOOw.43jDciBeITQ5DMn4RzFOOCR7PFtlr4Uv4Ao6Tqq', 'teacher', 'active', NULL, '2026-09-30', '0000-00-00'),
(126, 'AMELITA IGLESIA POLICARPIO', 'amelitapolicarpio@gmail.com', '$2y$10$rYe7p6USij7uhUGrHIkP7uOExw4rsga5QYmhihB9H8SeWmFeH7Z7C', 'teacher', 'active', NULL, '2026-09-30', '0000-00-00'),
(127, 'LHENNIE JANE BAGAMASPAD RAFAEL', 'lhennierafael@gmail.com', '$2y$10$rnhD9Z8m7TnoHhCe/419Lu4J9I2LmqGbN6eYWQH9CJLScCwXVy0ty', 'teacher', 'active', NULL, '2026-09-30', '0000-00-00'),
(128, 'ANALYN PILAR OCTAVIANO', 'analynoctaviano@gmail.com', '$2y$10$QjXU0Kojvan1k/gsLVEhq.Ss4.apvC9ICbU..bPjablIgVZWskR8.', 'teacher', 'active', NULL, '2026-09-30', '0000-00-00');

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
-- Indexes for table `reading_levels`
--
ALTER TABLE `reading_levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `school_year_id` (`school_year_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- Indexes for table `school_settings`
--
ALTER TABLE `school_settings`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `achievements_profiles`
--
ALTER TABLE `achievements_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `at_risk_insights`
--
ALTER TABLE `at_risk_insights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `behavioral_profiles`
--
ALTER TABLE `behavioral_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `dashboard_ai_summaries`
--
ALTER TABLE `dashboard_ai_summaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `developmental_profiles`
--
ALTER TABLE `developmental_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `grade_levels`
--
ALTER TABLE `grade_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `health_profiles`
--
ALTER TABLE `health_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `parents_guardians`
--
ALTER TABLE `parents_guardians`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=239;

--
-- AUTO_INCREMENT for table `reading_levels`
--
ALTER TABLE `reading_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `school_year`
--
ALTER TABLE `school_year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `section_teacher_assignments`
--
ALTER TABLE `section_teacher_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=351;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

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
-- Constraints for table `reading_levels`
--
ALTER TABLE `reading_levels`
  ADD CONSTRAINT `reading_levels_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reading_levels_ibfk_2` FOREIGN KEY (`school_year_id`) REFERENCES `school_year` (`id`),
  ADD CONSTRAINT `reading_levels_ibfk_3` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`);

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
