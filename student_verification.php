<?php

session_start();

include "DBconnect.php";


// Admin login করা আছে কিনা check
if (!isset($_SESSION["loggedIn"]) || $_SESSION["userType"] != "admin") {

    header("Location: login.php");
    exit();

}


// Logged-in admin ID
$admin_id = $_SESSION["Admin_ID"];


// ============================================================
// APPROVE STUDENT
// ============================================================

if (isset($_GET["approve"])) {

    $std_id = $_GET["approve"];


    // Verification approve করা
    $sql = "UPDATE Verification_doc

            SET Verification_Status = 'Approved',
                Admin_ID = '$admin_id',
                Reviewed_at = CURRENT_TIMESTAMP

            WHERE Std_ID = '$std_id'";


    mysqli_query($conn, $sql);


    // Student-কে verified করা
    $sql2 = "UPDATE Student

             SET is_Verified = 1

             WHERE Std_ID = '$std_id'";


    mysqli_query($conn, $sql2);


    header("Location: student_verification.php");
    exit();

}


// ============================================================
// REJECT STUDENT
// ============================================================

if (isset($_GET["reject"])) {

    $std_id = $_GET["reject"];


    // Verification reject করা
    $sql = "UPDATE Verification_doc

            SET Verification_Status = 'Rejected',
                Admin_ID = '$admin_id',
                Reviewed_at = CURRENT_TIMESTAMP

            WHERE Std_ID = '$std_id'";


    mysqli_query($conn, $sql);


    // Student verified থাকবে না
    $sql2 = "UPDATE Student

             SET is_Verified = 0

             WHERE Std_ID = '$std_id'";


    mysqli_query($conn, $sql2);


    header("Location: student_verification.php");
    exit();

}


// ============================================================
// GET PENDING STUDENTS
// ============================================================

$sql = "SELECT

            Student.Std_ID,
            Student.Username,
            Student.First_name,
            Student.Last_name,
            Student.Email,

            Verification_doc.University_ID,
            Verification_doc.University_Name,
            Verification_doc.University_Email,
            Verification_doc.Submitted_at

        FROM Student

        JOIN Verification_doc

        ON Student.Std_ID = Verification_doc.Std_ID

        WHERE Verification_doc.Verification_Status = 'Pending'

        ORDER BY Verification_doc.Submitted_at ASC";


$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Student Verification - Global Nest</title>


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


        .top {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 25px;

        }


        .top h1 {

            color: #000080;

            margin: 0;

        }


        .back-button {

            background-color: #000080;

            color: white;

            padding: 10px 18px;

            text-decoration: none;

            border-radius: 6px;

        }


        .back-button:hover {

            background-color: #000066;

        }


        /* ================= STUDENT CARD ================= */

        .student-card {

            background-color: white;

            padding: 25px;

            margin-bottom: 25px;

            border-radius: 10px;

            box-shadow: 0 3px 10px rgba(0,0,0,0.12);

        }


        .student-card h2 {

            color: #000080;

            margin-top: 0;

        }


        .student-info {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 10px 30px;

            margin-top: 15px;

        }


        .student-info p {

            margin: 5px 0;

        }


        .student-info strong {

            color: #333;

        }


        /* ================= BUTTONS ================= */

        .buttons {

            margin-top: 25px;

            display: flex;

            gap: 10px;

            flex-wrap: wrap;

        }


        .button {

            padding: 10px 18px;

            border: none;

            border-radius: 6px;

            text-decoration: none;

            color: white;

            cursor: pointer;

            font-size: 14px;

        }


        .view-button {

            background-color: #555;

        }


        .approve-button {

            background-color: green;

        }


        .reject-button {

            background-color: #cc0000;

        }


        .view-button:hover {

            background-color: #333;

        }


        .approve-button:hover {

            background-color: #006600;

        }


        .reject-button:hover {

            background-color: #990000;

        }


        /* ================= DOCUMENTS ================= */

        .documents {

            margin-top: 20px;

            padding: 15px;

            background-color: #f5f5f5;

            border-radius: 6px;

        }


        .documents h3 {

            margin-top: 0;

            color: #000080;

        }


        .documents a {

            display: block;

            margin: 8px 0;

            color: #000080;

        }


        /* ================= EMPTY ================= */

        .empty {

            background-color: white;

            padding: 40px;

            text-align: center;

            border-radius: 10px;

            color: #666;

        }


        /* ================= MOBILE ================= */

        @media (max-width: 700px) {

            .student-info {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>


<body>


<!-- ============================================================
     HEADER
============================================================ -->

<div class="header">

    <h2>Global Nest - Admin</h2>

    <a href="logout.php">
        Logout
    </a>

</div>


<!-- ============================================================
     MAIN
============================================================ -->

<div class="container">


    <div class="top">

        <h1>
            Students Waiting for Verification
        </h1>


        <a
            href="admin_dashboard.php"
            class="back-button"
        >
            Back to Dashboard
        </a>

    </div>


    <?php if (mysqli_num_rows($result) > 0) { ?>


        <?php while ($student = mysqli_fetch_assoc($result)) { ?>


            <div class="student-card">


                <h2>

                    <?php

                    echo htmlspecialchars(
                        $student["First_name"] . " " .
                        $student["Last_name"]
                    );

                    ?>

                </h2>


                <div class="student-info">


                    <p>

                        <strong>Student ID:</strong>

                        <?php

                        echo htmlspecialchars(
                            $student["Std_ID"]
                        );

                        ?>

                    </p>


                    <p>

                        <strong>Username:</strong>

                        <?php

                        echo htmlspecialchars(
                            $student["Username"]
                        );

                        ?>

                    </p>


                    <p>

                        <strong>Email:</strong>

                        <?php

                        echo htmlspecialchars(
                            $student["Email"]
                        );

                        ?>

                    </p>


                    <p>

                        <strong>University Student ID:</strong>

                        <?php

                        echo htmlspecialchars(
                            $student["University_ID"]
                        );

                        ?>

                    </p>


                    <p>

                        <strong>University:</strong>

                        <?php

                        echo htmlspecialchars(
                            $student["University_Name"]
                        );

                        ?>

                    </p>


                    <p>

                        <strong>University Email:</strong>

                        <?php

                        echo htmlspecialchars(
                            $student["University_Email"]
                        );

                        ?>

                    </p>


                </div>


                <!-- =================================================
                     BUTTONS
                ================================================= -->

                <div class="buttons">


                    <a
                        href="view_documents.php?std_id=<?php echo $student["Std_ID"]; ?>"
                        class="button view-button"
                    >
                        View Documents
                    </a>


                    <a
                        href="student_verification.php?approve=<?php echo $student["Std_ID"]; ?>"
                        class="button approve-button"
                    >
                        Approve
                    </a>


                    <a
                        href="student_verification.php?reject=<?php echo $student["Std_ID"]; ?>"
                        class="button reject-button"
                    >
                        Reject
                    </a>


                </div>


            </div>


        <?php } ?>


    <?php } else { ?>


        <div class="empty">

            <h2>No Students Waiting</h2>

            <p>
                There are currently no pending student verification requests.
            </p>

        </div>


    <?php } ?>


</div>


</body>

</html>