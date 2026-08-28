USE CSE370_project;


-- ============================================================
-- 1. UTILITIES THAT BELONG TO A ROOM
-- ------------------------------------------------------------
-- This is NOT the same as utility_expense.
--   utility_expense  = what a STUDENT paid
--   Listing_Utility  = what a ROOM costs per month
-- Both tables stay in the database.
--
-- The primary key uses TWO columns together, so one room
-- cannot have two rows for 'Electricity'.
-- ============================================================

CREATE TABLE Listing_Utility (

    ListingID INT NOT NULL,

    UtilityName VARCHAR(50) NOT NULL,

    Amount DECIMAL(10,2) NOT NULL,

    PRIMARY KEY (ListingID, UtilityName),

    FOREIGN KEY (ListingID)
        REFERENCES Listings (ListingID)
        ON DELETE CASCADE

) ENGINE=InnoDB;


-- ============================================================
-- 2. PHOTOS THAT BELONG TO A ROOM
-- ------------------------------------------------------------
-- One room can have many photos, so they live in their own
-- table instead of being columns on Listings.
--
-- PhotoID is AUTO_INCREMENT so every photo has its own
-- number, which makes it easy to delete a single photo later.
-- ============================================================

CREATE TABLE Listing_Photo (

    PhotoID INT NOT NULL AUTO_INCREMENT,

    ListingID INT NOT NULL,

    PhotoURL VARCHAR(255) NOT NULL,

    PRIMARY KEY (PhotoID),

    FOREIGN KEY (ListingID)
        REFERENCES Listings (ListingID)
        ON DELETE CASCADE

) ENGINE=InnoDB;


-- ============================================================
-- 3. SAMPLE UTILITIES FOR THE LISTINGS YOU ALREADY HAVE
-- ------------------------------------------------------------
-- INSERT ... SELECT means "do not type the values by hand,
-- take them from another query instead".
--
-- We do it this way because we do NOT know the ListingID
-- numbers any more. The SELECT finds them for us.
--
-- We group by Currency so the amounts make sense:
-- 1200 is normal for BDT but not for GBP.
-- ============================================================


-- ----- BANGLADESH LISTINGS (BDT) -----

INSERT INTO Listing_Utility (ListingID, UtilityName, Amount)
SELECT ListingID, 'Electricity', 1200.00
FROM Listings WHERE Currency = 'BDT';

INSERT INTO Listing_Utility (ListingID, UtilityName, Amount)
SELECT ListingID, 'Wifi', 800.00
FROM Listings WHERE Currency = 'BDT';

INSERT INTO Listing_Utility (ListingID, UtilityName, Amount)
SELECT ListingID, 'Gas', 500.00
FROM Listings WHERE Currency = 'BDT';

INSERT INTO Listing_Utility (ListingID, UtilityName, Amount)
SELECT ListingID, 'Water', 300.00
FROM Listings WHERE Currency = 'BDT';

INSERT INTO Listing_Utility (ListingID, UtilityName, Amount)
SELECT ListingID, 'Heating', 200.00
FROM Listings WHERE Currency = 'BDT';




-- ----- UK LISTINGS (GBP) -----

INSERT INTO Listing_Utility (ListingID, UtilityName, Amount)
SELECT ListingID, 'Electricity', 45.00
FROM Listings WHERE Currency = 'GBP';

INSERT INTO Listing_Utility (ListingID, UtilityName, Amount)
SELECT ListingID, 'Wifi', 30.00
FROM Listings WHERE Currency = 'GBP';

INSERT INTO Listing_Utility (ListingID, UtilityName, Amount)
SELECT ListingID, 'Gas', 25.00
FROM Listings WHERE Currency = 'GBP';

INSERT INTO Listing_Utility (ListingID, UtilityName, Amount)
SELECT ListingID, 'Water', 15.00
FROM Listings WHERE Currency = 'GBP';

INSERT INTO Listing_Utility (ListingID, UtilityName, Amount)
SELECT ListingID, 'Heating', 10.00
FROM Listings WHERE Currency = 'GBP';


-- ----- CANADA LISTINGS (CAD) -----

INSERT INTO Listing_Utility (ListingID, UtilityName, Amount)
SELECT ListingID, 'Electricity', 60.00
FROM Listings WHERE Currency = 'CAD';

INSERT INTO Listing_Utility (ListingID, UtilityName, Amount)
SELECT ListingID, 'Wifi', 40.00
FROM Listings WHERE Currency = 'CAD';

INSERT INTO Listing_Utility (ListingID, UtilityName, Amount)
SELECT ListingID, 'Gas', 35.00
FROM Listings WHERE Currency = 'CAD';

INSERT INTO Listing_Utility (ListingID, UtilityName, Amount)
SELECT ListingID, 'Water', 20.00
FROM Listings WHERE Currency = 'CAD';

INSERT INTO Listing_Utility (ListingID, UtilityName, Amount)
SELECT ListingID, 'Heating', 15.00
FROM Listings WHERE Currency = 'CAD';