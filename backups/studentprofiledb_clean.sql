-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2026 at 02:39 PM
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
  `school_head` varchar(150) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=451;

--
-- AUTO_INCREMENT for table `behavioral_profiles`
--
ALTER TABLE `behavioral_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `dashboard_ai_summaries`
--
ALTER TABLE `dashboard_ai_summaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `developmental_profiles`
--
ALTER TABLE `developmental_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `grade_levels`
--
ALTER TABLE `grade_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `health_profiles`
--
ALTER TABLE `health_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `parents_guardians`
--
ALTER TABLE `parents_guardians`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `section_teacher_assignments`
--
ALTER TABLE `section_teacher_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

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
