-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 31, 2026 at 11:26 AM
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
-- Database: `cse370_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_ID` int(11) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Username` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_ID`, `Email`, `Password`, `Username`) VALUES
(1, 'admin@globalnest.com', 'adminPass_123', 'superadmin'),
(2, 'rakib.admin@globalnest.com', 'adminPass_456', 'rakib_admin');

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE `bookmarks` (
  `BookmarkID` int(11) NOT NULL,
  `Std_ID` int(11) NOT NULL,
  `ListingID` int(11) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookmarks`
--

INSERT INTO `bookmarks` (`BookmarkID`, `Std_ID`, `ListingID`, `CreatedAt`) VALUES
(27, 23101004, 8, '2026-08-30 19:23:30');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `Std_ID` int(11) NOT NULL,
  `ListingID` int(11) NOT NULL,
  `SlotTime` datetime DEFAULT NULL,
  `VirtualTour` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `FAQ_ID` int(11) NOT NULL,
  `Question` text NOT NULL,
  `Answer` text DEFAULT NULL,
  `Std_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`FAQ_ID`, `Question`, `Answer`, `Std_ID`) VALUES
(1, 'How do I get my student account verified?', 'Upload your student ID or passport from your profile page. An admin reviews it within 48 hours.', 23101001),
(2, 'Is there a fee for booking a room viewing?', 'No. Booking a viewing slot is completely free, whether in person or as a virtual tour.', 23101002),
(3, 'Can I bookmark a listing before I am verified?', 'Yes, bookmarking is open to all students. Booking a viewing requires verification.', 23101003),
(4, 'How are utility expenses split between housemates?', 'Each student logs their own share under Utility Expenses. The site does not process payments.', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE `listings` (
  `ListingID` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Currency` varchar(10) NOT NULL,
  `RoomType` varchar(50) DEFAULT NULL,
  `Country` varchar(50) DEFAULT NULL,
  `State` varchar(50) DEFAULT NULL,
  `Neighbourhood` varchar(100) DEFAULT NULL,
  `Clinic` decimal(5,2) DEFAULT NULL,
  `Grocery` decimal(5,2) DEFAULT NULL,
  `Campus` decimal(5,2) DEFAULT NULL,
  `Legal_doc` varchar(255) DEFAULT NULL,
  `Admin_ID` int(11) DEFAULT NULL,
  `Provider_ID` int(11) NOT NULL,
  `Verification_Status` varchar(20) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`ListingID`, `Price`, `Currency`, `RoomType`, `Country`, `State`, `Neighbourhood`, `Clinic`, `Grocery`, `Campus`, `Legal_doc`, `Admin_ID`, `Provider_ID`, `Verification_Status`) VALUES
(6, 12000.00, 'BDT', 'Single Room', 'Bangladesh', 'Dhaka', 'Mohakhali', 0.80, 0.30, 0.50, '', 1, 1, 'Approved'),
(7, 8000.00, 'BDT', 'Shared Room', 'Bangladesh', 'Dhaka', 'Green Road', 1.20, 0.40, 2.10, '', 1, 1, 'Approved'),
(8, 18000.00, 'BDT', 'Studio', 'Bangladesh', 'Dhaka', 'Badda', 2.00, 0.50, 1.10, '', 1, 1, 'Approved'),
(9, 620.00, 'GBP', 'Studio', 'United Kingdom', 'London', 'Marylebone', 0.60, 0.20, 1.80, '', 2, 1, 'Approved'),
(10, 400.00, 'CAD', 'Single Room', 'Canada', 'Ontario', 'Downtown', 1.50, 0.70, 3.20, '', 2, 1, 'Approved'),
(11, 300.00, 'CAD', 'Shared Room', 'Canada', 'Ontario', 'Scarborough', 1.10, 0.60, 2.40, '', NULL, 1, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `listing_photo`
--

CREATE TABLE `listing_photo` (
  `PhotoID` int(11) NOT NULL,
  `ListingID` int(11) NOT NULL,
  `PhotoURL` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `listing_utility`
--

CREATE TABLE `listing_utility` (
  `ListingID` int(11) NOT NULL,
  `UtilityName` varchar(50) NOT NULL,
  `Amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_read` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `booking_id`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 1, 1, 'A student booked your room. Arrival date: 2026-09-01', 'Room Booking', 0, '2026-08-30 23:47:38'),
(2, 1, 1, 'New virtual tour request for Studio on 04 Sep 2026 at 07:54 PM.', 'Virtual Tour Request', 0, '2026-08-30 23:48:08');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `Reviewer_ID` int(11) NOT NULL,
  `Reviewee_ID` int(11) NOT NULL,
  `Rating` int(11) DEFAULT NULL,
  `Comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`Reviewer_ID`, `Reviewee_ID`, `Rating`, `Comment`) VALUES
(23101001, 23101002, 5, 'Quiet, considerate, always pays utilities on time.'),
(23101002, 23101001, 5, 'Very tidy and respectful housemate. Would live with again.'),
(23101003, 23101001, 4, 'Friendly, though the kitchen gets busy in the evenings.'),
(23101005, 23101004, 3, 'Nice person but keeps very different hours from me.');

-- --------------------------------------------------------

--
-- Table structure for table `room_bookings`
--

CREATE TABLE `room_bookings` (
  `id` int(11) NOT NULL,
  `ListingID` int(11) NOT NULL,
  `Std_ID` int(11) NOT NULL,
  `Provider_ID` int(11) NOT NULL,
  `arrival_date` date NOT NULL,
  `status` varchar(20) DEFAULT 'Booked',
  `terms_agreed` int(11) DEFAULT 0,
  `booked_at` datetime DEFAULT current_timestamp(),
  `cancelled_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_bookings`
--

INSERT INTO `room_bookings` (`id`, `ListingID`, `Std_ID`, `Provider_ID`, `arrival_date`, `status`, `terms_agreed`, `booked_at`, `cancelled_at`) VALUES
(1, 8, 23101004, 1, '2026-09-01', 'Booked', 1, '2026-08-30 23:47:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `room_provider`
--

CREATE TABLE `room_provider` (
  `Provider_ID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `First_name` varchar(50) NOT NULL,
  `Last_name` varchar(50) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `is_Verified` tinyint(1) NOT NULL DEFAULT 0,
  `Phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_provider`
--

INSERT INTO `room_provider` (`Provider_ID`, `Username`, `First_name`, `Last_name`, `Email`, `Password`, `is_Verified`, `Phone`) VALUES
(1, 'rahim_p', 'Rahim', 'Uddin', 'rahim@globalnest.com', 'provPass_1', 1, '01700000000');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `Std_ID` int(11) NOT NULL,
  `First_name` varchar(50) NOT NULL,
  `Last_name` varchar(50) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `is_Verified` tinyint(1) NOT NULL DEFAULT 0,
  `Nationality` varchar(50) DEFAULT NULL,
  `CookingHabit` varchar(50) DEFAULT NULL,
  `SleepSchedule` varchar(50) DEFAULT NULL,
  `Username` varchar(50) NOT NULL,
  `University_ID` varchar(50) DEFAULT NULL,
  `University_Name` varchar(150) DEFAULT NULL,
  `University_Email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`Std_ID`, `First_name`, `Last_name`, `Email`, `Password`, `is_Verified`, `Nationality`, `CookingHabit`, `SleepSchedule`, `Username`, `University_ID`, `University_Name`, `University_Email`) VALUES
(23101001, 'Ribat', 'Shama', 'ribat@g.bracu.ac.bd', 'stPass_1001', 0, 'Bangladeshi', 'Cooks daily', 'Early bird', 'ribat_s', NULL, NULL, NULL),
(23101002, 'Nabila', 'Rahman', 'nabila@g.bracu.ac.bd', 'stPass_1002', 0, 'Bangladeshi', 'Cooks weekends', 'Night owl', 'nabila_r', NULL, NULL, NULL),
(23101003, 'Arjun', 'Mehta', 'arjun@g.bracu.ac.bd', 'stPass_1003', 0, 'Indian', 'Rarely cooks', 'Night owl', 'arjun_m', NULL, NULL, NULL),
(23101004, 'Sofia', 'Lindqvist', 'sofia@g.bracu.ac.bd', 'stPass_1004', 0, 'Swedish', 'Cooks daily', 'Early bird', 'sofia_l', NULL, NULL, NULL),
(23101005, 'Kenji', 'Tanaka', 'kenji@g.bracu.ac.bd', 'stPass_1005', 0, 'Japanese', 'Cooks weekends', 'Flexible', 'kenji_t', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `utility_expense`
--

CREATE TABLE `utility_expense` (
  `Std_ID` int(11) NOT NULL,
  `ExpenseName` varchar(50) NOT NULL,
  `Amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utility_expense`
--

INSERT INTO `utility_expense` (`Std_ID`, `ExpenseName`, `Amount`) VALUES
(23101001, 'Electricity', 45.50),
(23101001, 'Gas', 15.75),
(23101001, 'WiFi', 20.00),
(23101002, 'Electricity', 38.20),
(23101002, 'WiFi', 20.00),
(23101003, 'Electricity', 52.00),
(23101004, 'Heating', 88.40),
(23101005, 'WiFi', 18.00);

-- --------------------------------------------------------

--
-- Table structure for table `verification_doc`
--

CREATE TABLE `verification_doc` (
  `Std_ID` int(11) NOT NULL,
  `Admin_ID` int(11) DEFAULT NULL,
  `DocType` varchar(50) DEFAULT NULL,
  `University_ID` varchar(50) DEFAULT NULL,
  `University_Name` varchar(150) DEFAULT NULL,
  `University_Email` varchar(100) DEFAULT NULL,
  `Verification_Status` varchar(20) NOT NULL DEFAULT 'Pending',
  `Submitted_at` datetime DEFAULT current_timestamp(),
  `Reviewed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `verification_doc_fileurl`
--

CREATE TABLE `verification_doc_fileurl` (
  `Std_ID` int(11) NOT NULL,
  `DocType` varchar(50) NOT NULL,
  `FileURL` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `virtual_tour_bookings`
--

CREATE TABLE `virtual_tour_bookings` (
  `id` int(11) NOT NULL,
  `listing_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `tour_date` date NOT NULL,
  `tour_time` time NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `virtual_tour_bookings`
--

INSERT INTO `virtual_tour_bookings` (`id`, `listing_id`, `student_id`, `provider_id`, `tour_date`, `tour_time`, `status`, `created_at`) VALUES
(1, 8, 23101004, 1, '2026-09-04', '19:54:00', 'Pending', '2026-08-30 23:48:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_ID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Password` (`Password`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD PRIMARY KEY (`BookmarkID`),
  ADD UNIQUE KEY `Std_ID` (`Std_ID`,`ListingID`),
  ADD KEY `ListingID` (`ListingID`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`Std_ID`,`ListingID`),
  ADD KEY `ListingID` (`ListingID`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`FAQ_ID`),
  ADD KEY `Std_ID` (`Std_ID`);

--
-- Indexes for table `listings`
--
ALTER TABLE `listings`
  ADD PRIMARY KEY (`ListingID`),
  ADD KEY `Admin_ID` (`Admin_ID`),
  ADD KEY `fk_listing_provider` (`Provider_ID`);

--
-- Indexes for table `listing_photo`
--
ALTER TABLE `listing_photo`
  ADD PRIMARY KEY (`PhotoID`),
  ADD KEY `ListingID` (`ListingID`);

--
-- Indexes for table `listing_utility`
--
ALTER TABLE `listing_utility`
  ADD PRIMARY KEY (`ListingID`,`UtilityName`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`Reviewer_ID`,`Reviewee_ID`),
  ADD KEY `Reviewee_ID` (`Reviewee_ID`);

--
-- Indexes for table `room_bookings`
--
ALTER TABLE `room_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_provider`
--
ALTER TABLE `room_provider`
  ADD PRIMARY KEY (`Provider_ID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`Std_ID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Password` (`Password`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `utility_expense`
--
ALTER TABLE `utility_expense`
  ADD PRIMARY KEY (`Std_ID`,`ExpenseName`);

--
-- Indexes for table `verification_doc`
--
ALTER TABLE `verification_doc`
  ADD PRIMARY KEY (`Std_ID`),
  ADD KEY `Admin_ID` (`Admin_ID`);

--
-- Indexes for table `verification_doc_fileurl`
--
ALTER TABLE `verification_doc_fileurl`
  ADD PRIMARY KEY (`Std_ID`,`DocType`);

--
-- Indexes for table `virtual_tour_bookings`
--
ALTER TABLE `virtual_tour_bookings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `Admin_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bookmarks`
--
ALTER TABLE `bookmarks`
  MODIFY `BookmarkID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `FAQ_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `ListingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `listing_photo`
--
ALTER TABLE `listing_photo`
  MODIFY `PhotoID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `room_bookings`
--
ALTER TABLE `room_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `room_provider`
--
ALTER TABLE `room_provider`
  MODIFY `Provider_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `Std_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23101006;

--
-- AUTO_INCREMENT for table `virtual_tour_bookings`
--
ALTER TABLE `virtual_tour_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookmarks`
--
ALTER TABLE `bookmarks`
  ADD CONSTRAINT `bookmarks_ibfk_1` FOREIGN KEY (`ListingID`) REFERENCES `listings` (`ListingID`) ON DELETE CASCADE;

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`Std_ID`) REFERENCES `student` (`Std_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`ListingID`) REFERENCES `listings` (`ListingID`) ON DELETE CASCADE;

--
-- Constraints for table `faq`
--
ALTER TABLE `faq`
  ADD CONSTRAINT `faq_ibfk_1` FOREIGN KEY (`Std_ID`) REFERENCES `student` (`Std_ID`) ON DELETE SET NULL;

--
-- Constraints for table `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `fk_listing_provider` FOREIGN KEY (`Provider_ID`) REFERENCES `room_provider` (`Provider_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`Admin_ID`) REFERENCES `admin` (`Admin_ID`) ON DELETE SET NULL;

--
-- Constraints for table `listing_photo`
--
ALTER TABLE `listing_photo`
  ADD CONSTRAINT `listing_photo_ibfk_1` FOREIGN KEY (`ListingID`) REFERENCES `listings` (`ListingID`) ON DELETE CASCADE;

--
-- Constraints for table `listing_utility`
--
ALTER TABLE `listing_utility`
  ADD CONSTRAINT `listing_utility_ibfk_1` FOREIGN KEY (`ListingID`) REFERENCES `listings` (`ListingID`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`Reviewer_ID`) REFERENCES `student` (`Std_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`Reviewee_ID`) REFERENCES `student` (`Std_ID`) ON DELETE CASCADE;

--
-- Constraints for table `utility_expense`
--
ALTER TABLE `utility_expense`
  ADD CONSTRAINT `utility_expense_ibfk_1` FOREIGN KEY (`Std_ID`) REFERENCES `student` (`Std_ID`) ON DELETE CASCADE;

--
-- Constraints for table `verification_doc`
--
ALTER TABLE `verification_doc`
  ADD CONSTRAINT `verification_doc_ibfk_1` FOREIGN KEY (`Std_ID`) REFERENCES `student` (`Std_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `verification_doc_ibfk_2` FOREIGN KEY (`Admin_ID`) REFERENCES `admin` (`Admin_ID`) ON DELETE SET NULL;

--
-- Constraints for table `verification_doc_fileurl`
--
ALTER TABLE `verification_doc_fileurl`
  ADD CONSTRAINT `verification_doc_fileurl_ibfk_1` FOREIGN KEY (`Std_ID`) REFERENCES `verification_doc` (`Std_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
