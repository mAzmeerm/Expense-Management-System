-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2026 at 07:18 AM
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
-- Database: `expensemanagementdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `budget`
--

CREATE TABLE `budget` (
  `BudgetID` int(11) NOT NULL,
  `DepartmentID` int(11) DEFAULT NULL,
  `Year` year(4) DEFAULT NULL,
  `AllocatedAmount` decimal(15,2) DEFAULT NULL,
  `SpentAmount` decimal(15,2) DEFAULT 0.00,
  `RemainAmount` decimal(15,2) GENERATED ALWAYS AS (`AllocatedAmount` - `SpentAmount`) STORED,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=eucjpms COLLATE=eucjpms_bin;

--
-- Dumping data for table `budget`
--

INSERT INTO `budget` (`BudgetID`, `DepartmentID`, `Year`, `AllocatedAmount`, `SpentAmount`, `Description`) VALUES
(2, 2, '2026', 30000.00, 29570.00, 'Sales department operational budget'),
(3, 3, '2026', 120000.00, 49910.00, 'Inventory and stock purchasing budget'),
(4, 4, '2026', 60000.00, 23960.00, 'Marketing campaigns and advertising'),
(6, 1, '2027', 70000.00, 70000.00, 'Management department annual operational and administrative budget'),
(11, 3, '2027', 20000.00, 10000.00, 'samdos'),
(12, 1, '2026', 2000.00, 0.00, 'yest');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `DepartmentID` int(11) NOT NULL,
  `DepartmentName` varchar(100) NOT NULL,
  `Status` varchar(20) DEFAULT 'In Use'
) ENGINE=InnoDB DEFAULT CHARSET=eucjpms COLLATE=eucjpms_bin;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`DepartmentID`, `DepartmentName`, `Status`) VALUES
(1, 'Management', 'In Use'),
(2, 'Sales', 'In Use'),
(3, 'Inventory', 'In Use'),
(4, 'Marketing', 'In Use'),
(5, 'Customer Service', 'In Use'),
(7, 'Kesatria', 'In Use');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `EmployeeID` int(11) NOT NULL,
  `DepartmentID` int(11) DEFAULT NULL,
  `Name` varchar(150) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` varchar(50) DEFAULT NULL,
  `PhoneNum` varchar(20) DEFAULT NULL,
  `Status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=eucjpms COLLATE=eucjpms_bin;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`EmployeeID`, `DepartmentID`, `Name`, `Email`, `Password`, `Role`, `PhoneNum`, `Status`) VALUES
(1, 3, 'Azmeer mantap coy', 'azmeer@sportzone.com', '$2y$10$e3jqXN.cO8Xh1Fsio/Jpku11z2MU/pCSh6oqtoHZMNkf6DxiPl3Nq', 'Admin', '0123456781', 'Active'),
(2, 1, 'Noorasyraf', 'noorasyraf@sportzone.com', '$2y$10$TaXOMFFnHBa9OlIvGHxMNe61E.gjNps.eAh0r7UYk2FX/.68TswW.', 'Admin', '0123456782', 'Active'),
(3, 1, 'Aiman Hakim', 'aimanhakim@sportzone.com', 'aiman123', 'Admin', '0123456783', 'Active'),
(4, 2, 'Faris', 'faris@sportzone.com', 'faris123', 'Staff', '0123456789', 'Inactive'),
(5, 2, 'Aisyah', 'aisyah@sportzone.com', '$2y$10$PaNjVxkEjZa3y01GRbOfg.TvUHS27ddNuizhTC71BD3P3rPQes8Cm', 'Staff', '0123456785', 'Active'),
(6, 3, 'Hakimi', 'hakimi@sportzone.com', 'hakimi123', 'Staff', '0123456786', 'Active'),
(7, 3, 'Sarah s', 'sarah@sportzone.com', '$2y$10$vuTXJ/YqIqOjw7c2Rcmqzu23RZF3CvsOhlJqHN8VJrVaaKr99pVgC', 'Staff', '0123456787', 'Active'),
(8, 4, 'Daniel', 'daniel@sportzone.com', 'daniel123', 'Staff', '0123456788', 'Active'),
(9, 5, 'Amirul', 'amirul@sportzone.com', 'amirul123', 'Staff', '0123456789', 'Active'),
(10, 5, 'Iqbal', 'iqbal@sportzone.com', 'iqbal123', 'Staff', '0123456790', 'Active'),
(19, 2, 'kamal', 'kamal@sportzone.com', '$2y$10$jphkI/UQldII8OWErF68AO1hkuwuQcOhLQjsarNjIAuyz3VDPHyr6', 'Staff', '01123670299', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `expensecategory`
--

CREATE TABLE `expensecategory` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=eucjpms COLLATE=eucjpms_bin;

--
-- Dumping data for table `expensecategory`
--

INSERT INTO `expensecategory` (`CategoryID`, `CategoryName`) VALUES
(1, 'Travel'),
(3, 'Sports Equipment'),
(4, 'Marketing'),
(5, 'Utilities');

-- --------------------------------------------------------

--
-- Table structure for table `expenseclaim`
--

CREATE TABLE `expenseclaim` (
  `ClaimID` int(11) NOT NULL,
  `EmployeeID` int(11) DEFAULT NULL,
  `CategoryID` int(11) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Amount` decimal(15,2) NOT NULL,
  `ClaimDate` date DEFAULT curdate(),
  `Status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=eucjpms COLLATE=eucjpms_bin;

--
-- Dumping data for table `expenseclaim`
--

INSERT INTO `expenseclaim` (`ClaimID`, `EmployeeID`, `CategoryID`, `Description`, `Amount`, `ClaimDate`, `Status`) VALUES
(1, 1, 1, 'Travel for supplier meeting', 550.00, '2026-05-01', 'Approved'),
(2, 2, 4, 'Facebook advertisement campaign', 1200.00, '2026-05-02', 'Approved'),
(3, 3, NULL, 'Office printer and paper purchase', 450.00, '2026-05-03', 'Rejected'),
(4, 4, 3, 'Purchase of football stock', 3500.00, '2026-05-04', 'Approved'),
(5, 5, 3, 'Badminton shuttlecock supplies', 900.00, '2026-05-05', 'Approved'),
(6, 6, NULL, 'Warehouse storage equipment', 750.00, '2026-05-06', 'Approved'),
(7, 7, 5, 'Electricity bill payment', 680.00, '2026-05-07', 'Approved'),
(8, 8, 4, 'Instagram sports promotion', 1300.00, '2026-05-08', 'Rejected'),
(9, 9, NULL, 'Customer service desk supplies', 250.00, '2026-05-09', 'Approved'),
(10, 10, 1, 'Delivery transportation expenses', 420.00, '2026-05-10', 'Approved'),
(11, 4, 3, 'Purchase of basketballs for new stock', 1850.00, '2026-05-12', 'Rejected'),
(12, 5, 3, 'Purchase of futsal jerseys for resale', 2400.00, '2026-05-13', 'Approved'),
(13, 6, NULL, 'New barcode scanner for inventory', 650.00, '2026-05-14', 'Approved'),
(14, 7, 5, 'Monthly water and electricity payment', 890.00, '2026-05-15', 'Approved'),
(15, 8, 4, 'TikTok advertisement for sports shoe promotion', 1750.00, '2026-05-16', 'Rejected'),
(16, 9, 1, 'Transportation cost for supplier delivery meeting', 320.00, '2026-05-17', 'Approved'),
(17, 4, 3, 'Purchase of basketballs for new stock', 1850.00, '2026-05-12', 'Rejected'),
(18, 6, NULL, 'New barcode scanner for inventory', 650.00, '2026-05-14', 'Approved'),
(19, 10, 4, 'Social media promotion for sports bag sale', 1200.00, '2026-05-18', 'Approved'),
(20, 5, 3, 'Purchase of badminton grip stock', 780.00, '2026-05-19', 'Rejected'),
(21, 7, 5, 'Snacks and drinks for warehouse staff', 210.00, '2026-05-20', 'Approved'),
(22, 9, 1, 'Travel expenses for supplier stock inspection', 540.00, '2026-05-21', 'Approved'),
(23, 2, 4, 'Instagram ads for running shoes promotion', 1500.00, '2026-05-22', 'Approved'),
(24, 3, 3, 'Restock of footballs for warehouse', 3200.00, '2026-05-23', 'Rejected'),
(25, 4, NULL, 'Purchase of office stationery for sales team', 420.00, '2026-05-24', 'Approved'),
(26, 8, 5, 'Electricity bill for retail store branch', 980.00, '2026-05-25', 'Rejected'),
(27, 10, 1, 'Fuel cost for product delivery to outlet', 610.00, '2026-05-26', 'Approved'),
(28, 3, 3, 'Bulk purchase of imported sports equipment for new branch opening', 250000.00, '2026-05-27', 'Approved'),
(29, 1, 4, 'National sports expo sponsorship and promotional booth setup', 500000.00, '2026-05-28', 'Rejected'),
(30, 2, 3, 'Restock of volleyball and net equipment', 2750.00, '2026-05-29', 'Approved'),
(31, 4, 1, 'Transportation expenses for supplier warehouse visit', 430.00, '2026-05-30', 'Approved'),
(32, 5, 4, 'Facebook ads for fitness accessories campaign', 1650.00, '2026-05-31', 'Approved'),
(33, 6, NULL, 'Purchase of storage shelves for inventory room', 980.00, '2026-06-01', 'Approved'),
(34, 7, 5, 'Refreshments for monthly staff meeting', 260.00, '2026-06-02', 'Rejected'),
(35, 8, 3, 'New stock of tennis rackets and grips', 5400.00, '2026-06-03', 'Approved'),
(36, 9, NULL, 'Replacement of office chairs for customer service area', 1200.00, '2026-06-04', 'Approved'),
(37, 1, 4, 'Shopee promotional campaign for sports watches', 2100.00, '2026-06-05', 'Approved'),
(38, 2, 3, 'Purchase of gym resistance bands stock', 1350.00, '2026-06-06', 'Approved'),
(39, 3, 1, 'Travel expenses for supplier negotiation meeting', 620.00, '2026-06-07', 'Rejected'),
(40, 4, NULL, 'Office printer toner replacement', 390.00, '2026-06-08', 'Approved'),
(41, 5, 5, 'Warehouse staff meal allowance', 480.00, '2026-06-09', 'Approved'),
(42, 6, 3, 'Restock of sports water bottles', 2750.00, '2026-06-10', 'Approved'),
(43, 7, 4, 'Google ads for football boots promotion', 1850.00, '2026-06-11', 'Approved'),
(44, 8, NULL, 'Packaging supplies for online customer orders', 560.00, '2026-06-12', 'Approved'),
(45, 9, 1, 'Fuel expenses for outlet stock delivery', 340.00, '2026-06-13', 'Rejected'),
(46, 10, 3, 'Purchase of yoga mats for new inventory', 3200.00, '2026-06-14', 'Approved'),
(47, 1, 4, 'TIKTOK', 200.00, '2026-06-13', 'Rejected'),
(48, 1, 4, 'shoppee', 3000.00, '2026-06-13', 'Rejected'),
(49, 1, 4, 'tktk', 20000.00, '2026-06-13', 'Rejected'),
(50, 1, 4, 'TIKTOK', 2000.00, '2026-06-13', 'Approved'),
(51, 1, 3, 'Group Project CSC264 ', 15300.00, '2026-06-14', 'Approved'),
(52, 1, 4, 'micropro 1 ', 19.00, '2026-06-14', 'Rejected'),
(53, 4, 4, 'Cleint', 200.00, '2026-06-15', 'Approved'),
(54, 4, 5, 'TIKTOK', 20000.00, '2026-06-15', 'Rejected'),
(55, 1, 4, 'TETSTT', 1000.00, '2026-06-15', 'Approved'),
(56, 1, 4, 'micropro 1 ', 500.00, '2026-06-15', 'Rejected'),
(57, 1, 4, 'aamdo', 2000.00, '2026-06-15', 'Approved'),
(58, 1, 4, 'test123', 30.00, '2026-06-15', 'Approved'),
(59, 1, 4, 'barued', 40.00, '2026-06-15', 'Approved'),
(60, 1, 4, '2027', 10000.00, '2027-06-15', 'Approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budget`
--
ALTER TABLE `budget`
  ADD PRIMARY KEY (`BudgetID`),
  ADD KEY `DepartmentID` (`DepartmentID`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`DepartmentID`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`EmployeeID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `DepartmentID` (`DepartmentID`);

--
-- Indexes for table `expensecategory`
--
ALTER TABLE `expensecategory`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexes for table `expenseclaim`
--
ALTER TABLE `expenseclaim`
  ADD PRIMARY KEY (`ClaimID`),
  ADD KEY `EmployeeID` (`EmployeeID`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budget`
--
ALTER TABLE `budget`
  MODIFY `BudgetID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `expensecategory`
--
ALTER TABLE `expensecategory`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `expenseclaim`
--
ALTER TABLE `expenseclaim`
  MODIFY `ClaimID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budget`
--
ALTER TABLE `budget`
  ADD CONSTRAINT `budget_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`) ON DELETE CASCADE;

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`) ON DELETE SET NULL;

--
-- Constraints for table `expenseclaim`
--
ALTER TABLE `expenseclaim`
  ADD CONSTRAINT `expenseclaim_ibfk_1` FOREIGN KEY (`EmployeeID`) REFERENCES `employee` (`EmployeeID`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenseclaim_ibfk_2` FOREIGN KEY (`CategoryID`) REFERENCES `expensecategory` (`CategoryID`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
