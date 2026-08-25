<?php

session_start();

include "DBconnect.php";


// Admin login করা আছে কিনা check
if (!isset($_SESSION["loggedIn"]) || $_SESSION["userType"] != "admin") {

    header("Location: login.php");
    exit();

}


// ============================================================
// COUNT STUDENTS
// ============================================================

$sql_students = "SELECT COUNT(*) AS total
                 FROM Student";

$result_students = mysqli_query($conn, $sql_students);

$row_students = mysqli_fetch_assoc($result_students);

$student_count = $row_students["total"];


// ============================================================
// COUNT APPROVED LISTINGS
// ============================================================

$sql_listings = "SELECT COUNT(*) AS total
                 FROM Listings
                 WHERE Verification_Status = 'Approved'";

$result_listings = mysqli_query($conn, $sql_listings);

$row_listings = mysqli_fetch_assoc($result_listings);

$listing_count = $row_listings["total"];


// ============================================================
// COUNT STUDENTS WAITING FOR VERIFICATION
// ============================================================

$sql_pending_students = "SELECT COUNT(*) AS total
                         FROM Verification_doc
                         WHERE Verification_Status = 'Pending'";

$result_pending_students =
    mysqli_query($conn, $sql_pending_students);

$row_pending_students =
    mysqli_fetch_assoc($result_pending_students);

$pending_student_count =
    $row_pending_students["total"];


// ============================================================
// COUNT LISTINGS WAITING FOR VERIFICATION
// ============================================================

$sql_pending_listings = "SELECT COUNT(*) AS total
                         FROM Listings
                         WHERE Verification_Status = 'Pending'";

$result_pending_listings =
    mysqli_query($conn, $sql_pending_listings);

$row_pending_listings =
    mysqli_fetch_assoc($result_pending_listings);

$pending_listing_count =
    $row_pending_listings["total"];

?>

<!DOCTYPE html>
<html>

<head>

    <title>Global Nest - Admin Dashboard</title>

    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family: Arial, sans-serif;

            background-color: #f5f5f5;

            color: #222;

        }


        /* ================= HEADER ================= */

        .header {

            background-color: #000080;

            color: white;

            padding: 20px 40px;

            display: flex;

            justify-content: space-between;

            align-items: center;

        }


        .header h2 {

            margin: 0;

        }


        .header-right {

            display: flex;

            align-items: center;

            gap: 20px;

        }


        .header a {

            color: white;

            text-decoration: none;

        }


        .header a:hover {

            text-decoration: underline;

        }


        /* ================= MAIN ================= */

        .container {

            width: 90%;

            max-width: 1100px;

            margin: 40px auto;

        }


        .welcome {

            margin-bottom: 30px;

        }


        .welcome h1 {

            color: #000080;

            margin-bottom: 5px;

        }


        .welcome p {

            color: #666;

        }


        /* ================= CARDS ================= */

        .cards {

            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 25px;

        }


        .card {

            background-color: white;

            padding: 30px;

            border-radius: 12px;

            box-shadow: 0 3px 10px rgba(0,0,0,0.12);

            text-align: center;

        }


        .card h2 {

            color: #000080;

            font-size: 32px;

            margin: 10px 0;

        }


        .card p {

            color: #666;

            font-size: 17px;

        }


        .card a {

            display: inline-block;

            margin-top: 15px;

            padding: 10px 20px;

            background-color: #000080;

            color: white;

            text-decoration: none;

            border-radius: 6px;

        }


        .card a:hover {

            background-color: #000066;

        }


        /* ================= VERIFICATION ================= */

        .verification {

            margin-top: 35px;

            background-color: white;

            padding: 25px;

            border-radius: 12px;

            box-shadow: 0 3px 10px rgba(0,0,0,0.12);

        }


        .verification h2 {

            color: #000080;

            margin-top: 0;

        }


        .verification-item {

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 20px 0;

            border-bottom: 1px solid #ddd;

        }


        .verification-item:last-child {

            border-bottom: none;

        }


        .verification-info h3 {

            margin: 0 0 5px 0;

        }


        .verification-info p {

            margin: 4px 0;

            color: #666;

        }


        .verify-button {

            background-color: #000080;

            color: white;

            padding: 10px 18px;

            text-decoration: none;

            border-radius: 6px;

        }


        .verify-button:hover {

            background-color: #000066;

        }


        /* ================= MOBILE ================= */

        @media (max-width: 700px) {

            .cards {

                grid-template-columns: 1fr;

            }


            .verification-item {

                flex-direction: column;

                align-items: flex-start;

                gap: 15px;

            }

        }

    </style>

</head>


<body>


<!-- ============================================================
     HEADER
============================================================ -->

<div class="header">

    <h2>Global Nest</h2>

    <div class="header-right">

        <span>
            Hi, Admin
        </span>
		
		<a href="faq_admin.php">
            Answer Questions
        </a>
		
        <a href="logout.php">
            Logout
        </a>

    </div>

</div>


<!-- ============================================================
     MAIN CONTENT
============================================================ -->

<div class="container">


    <div class="welcome">

        <h1>Admin Dashboard</h1>

        <p>
            Manage students, listings and verification requests.
        </p>

    </div>


    <!-- ========================================================
         MAIN CARDS
    ========================================================= -->

    <div class="cards">


        <!-- STUDENTS -->

        <div class="card">

            <p>Total Students</p>

            <h2>
                <?php echo $student_count; ?>
            </h2>

            <a href="students.php">
                View Students
            </a>

        </div>


        <!-- APPROVED LISTINGS -->

        <div class="card">

            <p>Approved Listings</p>

            <h2>
                <?php echo $listing_count; ?>
            </h2>

            <a href="listings.php">
                View Listings
            </a>

        </div>


    </div>


    <!-- ========================================================
         STUDENT VERIFICATION
    ========================================================= -->

    <div class="verification">

        <h2>
            Students Waiting for Verification
        </h2>


        <div class="verification-item">


            <div class="verification-info">

                <h3>
                    <?php echo $pending_student_count; ?>
                    Students
                </h3>

                <p>
                    Students who have submitted
                    their verification documents.
                </p>

            </div>


            <a
                href="student_verification.php"
                class="verify-button"
            >
                Check Students
            </a>


        </div>

    </div>


    <!-- ========================================================
         LISTING VERIFICATION
    ========================================================= -->

    <div class="verification">

        <h2>
            Listings Waiting for Verification
        </h2>


        <div class="verification-item">


            <div class="verification-info">

                <h3>
                    <?php echo $pending_listing_count; ?>
                    Listings
                </h3>

                <p>
                    Listings waiting for admin approval.
                </p>

            </div>


            <a
                href="listing_verification.php"
                class="verify-button"
            >
                Check Listings
            </a>


        </div>

    </div>


</div>


</body>

</html>