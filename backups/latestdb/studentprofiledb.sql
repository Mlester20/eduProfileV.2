-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2026 at 03:02 PM
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
(12, 122, 'teacher', 'Importing students from Excel', 'Students', NULL, NULL, 'MARICEL MANUEL BESA Imported 33 student(s) via Excel', '::1', 'success', '2026-09-29 12:58:58');

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
(12, 'Grade 6', '2026-09-29 12:58:05', '2026-09-29 12:58:05');

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
(89, 201, 122, 'SOMERA, GERIEL VICTORIO', '', '', 'ABRIAM,ELIZABETH,PAOLO', '', '', '', '', '', '2026-09-29 12:58:58', '2026-09-29 12:58:58');

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
(16, 12, 'Emerald Green', 122, '2026-09-29 12:58:33', '2026-09-29 12:58:33');

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
(18, 16, 122, 8, '2026-09-29 12:58:33', '2026-09-29 12:58:33');

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
(201, '103503200028', 'ASHLEY ZIAH', 'ABRIAM', 'SOMERA', NULL, '2015-07-27', 11, 'Female', 'Ilocano', NULL, 'Christianity', NULL, NULL, NULL, NULL, NULL, 'SAN JOSE SUR', 'MALLIG', 'ISABELA', 'Face-to-Face', NULL, 8, 12, 16, 122, 'active', '2026-09-29 12:58:58', '2026-09-29 12:58:58');

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
(122, 'MARICEL MANUEL BESA', 'maricelbesa@gmail.com', '$2y$10$aO1HD3ElgbeoWCk/0IQadu3eTQQZ33xOFUCqKyTCi1G1GfdV2rChm', 'teacher', 'active', NULL, '2026-09-29', '0000-00-00');

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
-- Indexes for table `dashboard_ai_summaries`
--
ALTER TABLE `dashboard_ai_summaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `school_year_unique` (`school_year_id`);

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
-- AUTO_INCREMENT for table `at_risk_insights`
--
ALTER TABLE `at_risk_insights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `dashboard_ai_summaries`
--
ALTER TABLE `dashboard_ai_summaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `grade_levels`
--
ALTER TABLE `grade_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `health_profiles`
--
ALTER TABLE `health_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `parents_guardians`
--
ALTER TABLE `parents_guardians`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `section_teacher_assignments`
--
ALTER TABLE `section_teacher_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

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
