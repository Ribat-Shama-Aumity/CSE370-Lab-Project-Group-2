USE CSE370_project;

-- ============================================================
-- 1. ONE ROOM PROVIDER ACCOUNT
--    Login  ->  Username: rahim_p    Password: provPass_1
-- ============================================================

INSERT INTO Room_Provider
(Username, First_name, Last_name, Email, Password, is_Verified, Phone)
VALUES
('rahim_p', 'Rahim', 'Uddin', 'rahim@globalnest.com',
 'provPass_1', 1, '01700000000');


-- ============================================================
-- 2. SIX LISTINGS FOR THAT PROVIDER
--    Provider_ID = 1 because he is the first provider.
--    Five are Approved so students can see them.
--    One stays Pending so the admin page still has
--    something to review.
-- ============================================================

INSERT INTO Listings
(Price, Currency, RoomType, Country, State, Neighbourhood,
 Clinic, Grocery, Campus, Legal_doc, Admin_ID, Provider_ID,
 Verification_Status)
VALUES

('12000.00', 'BDT', 'Single Room', 'Bangladesh', 'Dhaka',
 'Mohakhali', '0.80', '0.30', '0.50', '', 1, 1, 'Approved'),

('8000.00', 'BDT', 'Shared Room', 'Bangladesh', 'Dhaka',
 'Green Road', '1.20', '0.40', '2.10', '', 1, 1, 'Approved'),

('18000.00', 'BDT', 'Studio', 'Bangladesh', 'Dhaka',
 'Badda', '2.00', '0.50', '1.10', '', 1, 1, 'Approved'),

('620.00', 'GBP', 'Studio', 'United Kingdom', 'London',
 'Marylebone', '0.60', '0.20', '1.80', '', 2, 1, 'Approved'),

('400.00', 'CAD', 'Single Room', 'Canada', 'Ontario',
 'Downtown', '1.50', '0.70', '3.20', '', 2, 1, 'Approved'),

('300.00', 'CAD', 'Shared Room', 'Canada', 'Ontario',
 'Scarborough', '1.10', '0.60', '2.40', '', NULL, 1, 'Pending');