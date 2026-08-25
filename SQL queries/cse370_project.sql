-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 19, 2026 at 08:05 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

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
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_ID`, `Email`, `Password`) VALUES
(1, 'admin@globalnest.com', 'adminPass_123'),
(2, 'rakib.admin@globalnest.com', 'adminPass_456');

-- --------------------------------------------------------

--
-- Table structure for table `bookmark`
--

CREATE TABLE `bookmark` (
  `Std_ID` int(11) NOT NULL,
  `ListingID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `bookmark`
--

INSERT INTO `bookmark` (`Std_ID`, `ListingID`) VALUES
(23101001, 3),
(23101001, 5),
(23101002, 1),
(23101003, 1),
(23101003, 5),
(23101005, 2);

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `Std_ID` int(11) NOT NULL,
  `ListingID` int(11) NOT NULL,
  `SlotTime` datetime DEFAULT NULL,
  `VirtualTour` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`Std_ID`, `ListingID`, `SlotTime`, `VirtualTour`) VALUES
(23101001, 3, '2026-09-03 17:00:00', 1),
(23101002, 1, '2026-09-01 15:00:00', 1),
(23101003, 1, '2026-09-02 11:30:00', 0),
(23101005, 4, '2026-09-04 10:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `FAQ_ID` int(11) NOT NULL,
  `Question` text NOT NULL,
  `Answer` text DEFAULT NULL,
  `Std_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `RoomType` varchar(50) DEFAULT NULL,
  `Country` varchar(50) DEFAULT NULL,
  `State` varchar(50) DEFAULT NULL,
  `Neighbourhood` varchar(100) DEFAULT NULL,
  `Clinic` decimal(5,2) DEFAULT NULL,
  `Grocery` decimal(5,2) DEFAULT NULL,
  `Campus` decimal(5,2) DEFAULT NULL,
  `Legal_doc` varchar(255) DEFAULT NULL,
  `Admin_ID` int(11) DEFAULT NULL,
  `Std_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`ListingID`, `Price`, `RoomType`, `Country`, `State`, `Neighbourhood`, `Clinic`, `Grocery`, `Campus`, `Legal_doc`, `Admin_ID`, `Std_ID`) VALUES
(1, '250.00', 'Single Room', 'Bangladesh', 'Dhaka', 'Mohakhali', '0.80', '0.30', '0.50', 'legal/deed_1001.pdf', 1, 23101001),
(2, '180.00', 'Shared Room', 'Bangladesh', 'Dhaka', 'Green Road', '1.20', '0.40', '2.10', 'legal/deed_1002.pdf', 1, 23101002),
(3, '620.00', 'Studio', 'United Kingdom', 'London', 'Marylebone', '0.60', '0.20', '1.80', 'legal/deed_1003.pdf', 2, 23101004),
(4, '400.00', 'Single Room', 'Canada', 'Ontario', 'Downtown', '1.50', '0.70', '3.20', 'legal/deed_1004.pdf', NULL, 23101003),
(5, '300.00', 'Shared Room', 'Bangladesh', 'Dhaka', 'Badda', '2.00', '0.50', '1.10', 'legal/deed_1005.pdf', 2, 23101005);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `Reviewer_ID` int(11) NOT NULL,
  `Reviewee_ID` int(11) NOT NULL,
  `Rating` int(11) DEFAULT NULL,
  `Comment` text DEFAULT NULL
) ;

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
  `SleepSchedule` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`Std_ID`, `First_name`, `Last_name`, `Email`, `Password`, `is_Verified`, `Nationality`, `CookingHabit`, `SleepSchedule`) VALUES
(23101001, 'Ribat', 'Shama', 'ribat@g.bracu.ac.bd', 'stPass_1001', 1, 'Bangladeshi', 'Cooks daily', 'Early bird'),
(23101002, 'Nabila', 'Rahman', 'nabila@g.bracu.ac.bd', 'stPass_1002', 1, 'Bangladeshi', 'Cooks weekends', 'Night owl'),
(23101003, 'Arjun', 'Mehta', 'arjun@g.bracu.ac.bd', 'stPass_1003', 0, 'Indian', 'Rarely cooks', 'Night owl'),
(23101004, 'Sofia', 'Lindqvist', 'sofia@g.bracu.ac.bd', 'stPass_1004', 1, 'Swedish', 'Cooks daily', 'Early bird'),
(23101005, 'Kenji', 'Tanaka', 'kenji@g.bracu.ac.bd', 'stPass_1005', 0, 'Japanese', 'Cooks weekends', 'Flexible');

-- --------------------------------------------------------

--
-- Table structure for table `utility_expense`
--

CREATE TABLE `utility_expense` (
  `Std_ID` int(11) NOT NULL,
  `ExpenseName` varchar(50) NOT NULL,
  `Amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `utility_expense`
--

INSERT INTO `utility_expense` (`Std_ID`, `ExpenseName`, `Amount`) VALUES
(23101001, 'Electricity', '45.50'),
(23101001, 'Gas', '15.75'),
(23101001, 'WiFi', '20.00'),
(23101002, 'Electricity', '38.20'),
(23101002, 'WiFi', '20.00'),
(23101003, 'Electricity', '52.00'),
(23101004, 'Heating', '88.40'),
(23101005, 'WiFi', '18.00');

-- --------------------------------------------------------

--
-- Table structure for table `verification_doc`
--

CREATE TABLE `verification_doc` (
  `Std_ID` int(11) NOT NULL,
  `Admin_ID` int(11) DEFAULT NULL,
  `DocType` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `verification_doc`
--

INSERT INTO `verification_doc` (`Std_ID`, `Admin_ID`, `DocType`) VALUES
(23101001, 1, 'Student ID Card'),
(23101002, 1, 'Passport'),
(23101003, 2, 'Student ID Card'),
(23101004, 2, 'Enrollment Letter');

-- --------------------------------------------------------

--
-- Table structure for table `verification_doc_fileurl`
--

CREATE TABLE `verification_doc_fileurl` (
  `Std_ID` int(11) NOT NULL,
  `FileURL` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `verification_doc_fileurl`
--

INSERT INTO `verification_doc_fileurl` (`Std_ID`, `FileURL`) VALUES
(23101001, 'uploads/docs/23101001_id_back.jpg'),
(23101001, 'uploads/docs/23101001_id_front.jpg'),
(23101002, 'uploads/docs/23101002_passport.pdf'),
(23101003, 'uploads/docs/23101003_id.jpg'),
(23101004, 'uploads/docs/23101004_enrollment.pdf');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_ID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Password` (`Password`);

--
-- Indexes for table `bookmark`
--
ALTER TABLE `bookmark`
  ADD PRIMARY KEY (`Std_ID`,`ListingID`),
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
  ADD KEY `Std_ID` (`Std_ID`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`Reviewer_ID`,`Reviewee_ID`),
  ADD KEY `Reviewee_ID` (`Reviewee_ID`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`Std_ID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `Password` (`Password`);

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
  ADD PRIMARY KEY (`Std_ID`,`FileURL`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `Admin_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `FAQ_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `listings`
--
ALTER TABLE `listings`
  MODIFY `ListingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `Std_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23101006;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookmark`
--
ALTER TABLE `bookmark`
  ADD CONSTRAINT `bookmark_ibfk_1` FOREIGN KEY (`Std_ID`) REFERENCES `student` (`Std_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookmark_ibfk_2` FOREIGN KEY (`ListingID`) REFERENCES `listings` (`ListingID`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`Admin_ID`) REFERENCES `admin` (`Admin_ID`) ON DELETE SET NULL,
  ADD CONSTRAINT `listings_ibfk_2` FOREIGN KEY (`Std_ID`) REFERENCES `student` (`Std_ID`) ON DELETE CASCADE;

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

USE CSE370_project;


-- ============================================================
-- ADD USERNAME TO STUDENT TABLE
-- ============================================================

ALTER TABLE Student
ADD COLUMN Username VARCHAR(50) NULL UNIQUE;


-- Add usernames to existing students

UPDATE Student
SET Username = 'ribat_s'
WHERE Email = 'ribat@g.bracu.ac.bd';

UPDATE Student
SET Username = 'nabila_r'
WHERE Email = 'nabila@g.bracu.ac.bd';

UPDATE Student
SET Username = 'arjun_m'
WHERE Email = 'arjun@g.bracu.ac.bd';

UPDATE Student
SET Username = 'sofia_l'
WHERE Email = 'sofia@g.bracu.ac.bd';

UPDATE Student
SET Username = 'kenji_t'
WHERE Email = 'kenji@g.bracu.ac.bd';


-- Make Username required

ALTER TABLE Student
MODIFY Username VARCHAR(50) NOT NULL;



-- ============================================================
-- ADD USERNAME TO ADMIN TABLE
-- ============================================================

ALTER TABLE Admin
ADD COLUMN Username VARCHAR(50) NULL UNIQUE;


-- Add usernames to existing admins

UPDATE Admin
SET Username = 'superadmin'
WHERE Email = 'admin@globalnest.com';

UPDATE Admin
SET Username = 'rakib_admin'
WHERE Email = 'rakib.admin@globalnest.com';


-- Make Username required

ALTER TABLE Admin
MODIFY Username VARCHAR(50) NOT NULL;

USE CSE370_project;


-- ============================================================
-- 1. ADD UNIVERSITY INFORMATION TO STUDENT
-- ============================================================

ALTER TABLE Student
ADD COLUMN University_ID VARCHAR(50) NULL,
ADD COLUMN University_Name VARCHAR(150) NULL,
ADD COLUMN University_Email VARCHAR(100) NULL;


-- ============================================================
-- 2. REMOVE OLD VERIFICATION DOCUMENT FILE TABLE
-- ============================================================

DROP TABLE IF EXISTS Verification_doc_FileURL;


-- ============================================================
-- 3. UPDATE VERIFICATION_DOC TABLE
-- ============================================================

ALTER TABLE Verification_doc
ADD COLUMN University_ID VARCHAR(50) NULL,
ADD COLUMN University_Name VARCHAR(150) NULL,
ADD COLUMN University_Email VARCHAR(100) NULL,
ADD COLUMN Verification_Status VARCHAR(20) NOT NULL DEFAULT 'Pending',
ADD COLUMN Submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN Reviewed_at DATETIME NULL;


-- ============================================================
-- 4. CREATE DOCUMENT FILE TABLE AGAIN
-- ============================================================

CREATE TABLE Verification_doc_FileURL (

    Std_ID INT NOT NULL,

    DocType VARCHAR(50) NOT NULL,

    FileURL VARCHAR(255) NOT NULL,

    PRIMARY KEY (Std_ID, DocType),

    FOREIGN KEY (Std_ID)
        REFERENCES Verification_doc (Std_ID)
        ON DELETE CASCADE

) ENGINE=InnoDB;


-- ============================================================
-- 5. RE-INSERT EXISTING SAMPLE DOCUMENTS
-- ============================================================

INSERT INTO Verification_doc_FileURL
(Std_ID, DocType, FileURL)
VALUES

(23101001, 'Student ID Card',
 'uploads/docs/23101001_id_front.jpg'),

(23101002, 'Passport',
 'uploads/docs/23101002_passport.pdf'),

(23101003, 'Student ID Card',
 'uploads/docs/23101003_id.jpg'),

(23101004, 'Enrollment Letter',
 'uploads/docs/23101004_enrollment.pdf');


-- ============================================================
-- 6. ADD BACK SIDE OF STUDENT ID CARD AS ANOTHER DOCUMENT
-- ============================================================

INSERT INTO Verification_doc_FileURL
(Std_ID, DocType, FileURL)
VALUES

(23101001, 'Student ID Card Back',
 'uploads/docs/23101001_id_back.jpg');

USE CSE370_project;


-- 1. সব student-কে unverified করে দাও

UPDATE Student
SET is_Verified = 0;


-- 2. Student table-এর পুরোনো university information মুছে দাও

UPDATE Student
SET University_ID = NULL,
    University_Name = NULL,
    University_Email = NULL;


-- 3. পুরোনো verification document files মুছে দাও

DELETE FROM Verification_doc_FileURL;


-- 4. পুরোনো verification applications মুছে দাও

DELETE FROM Verification_doc;


USE CSE370_project;

ALTER TABLE Listings
ADD COLUMN Verification_Status VARCHAR(20) NOT NULL DEFAULT 'Pending';


CREATE TABLE Room_Provider (
    Provider_ID INT NOT NULL AUTO_INCREMENT,
    Username VARCHAR(50) NOT NULL UNIQUE,
    First_name VARCHAR(50) NOT NULL,
    Last_name VARCHAR(50),
    Email VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    is_Verified TINYINT(1) NOT NULL DEFAULT 0,
    Phone VARCHAR(20),
    PRIMARY KEY (Provider_ID)
) ENGINE=InnoDB;


DELETE FROM Listings;
ALTER TABLE Listings
DROP FOREIGN KEY Listings_ibfk_2;
ALTER TABLE Listings
DROP COLUMN Std_ID;
ALTER TABLE Listings
ADD COLUMN Provider_ID INT NOT NULL AFTER Admin_ID;
ALTER TABLE Listings
ADD CONSTRAINT fk_listing_provider
FOREIGN KEY (Provider_ID)
REFERENCES Room_Provider(Provider_ID)
ON DELETE CASCADE;

ALTER TABLE Listings
ADD COLUMN Currency VARCHAR(10) NOT NULL AFTER Price;