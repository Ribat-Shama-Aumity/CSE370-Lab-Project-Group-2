<?php

session_start();

include "DBconnect.php";


// Student login করা আছে কিনা check
if (!isset($_SESSION["loggedIn"]) || $_SESSION["userType"] != "student") {

    header("Location: login.php");
    exit();

}


$std_id = $_SESSION["Std_ID"];

$message = "";


// ============================================================
// FORM SUBMIT
// ============================================================

if (isset($_POST["verify"])) {

    $university_id = $_POST["university_id"];
    $university_name = $_POST["university_name"];
    $university_email = $_POST["university_email"];


    // ========================================================
    // CHECK IF STUDENT ALREADY SUBMITTED
    // ========================================================

    $check = "SELECT Verification_Status
              FROM Verification_doc
              WHERE Std_ID = '$std_id'";

    $check_result = mysqli_query($conn, $check);


    if (mysqli_num_rows($check_result) > 0) {

        $verification = mysqli_fetch_assoc($check_result);

        $status = $verification["Verification_Status"];


        // Pending or Approved হলে আবার submit করতে পারবে না

        if ($status == "Pending" || $status == "Approved") {

            $message = "You have already submitted your verification.";

        }


        // Rejected হলে আবার submit করতে পারবে

        else if ($status == "Rejected") {

            // নিচের verification process চলবে

        }

    }


    // ========================================================
    // NEW VERIFICATION OR RE-SUBMISSION AFTER REJECTION
    // ========================================================

    if ($message == "") {


        // Uploaded files

        $student_id_card = $_FILES["student_id_card"];

        $passport = $_FILES["passport"];


        // Upload folder

        $upload_folder = "uploads/docs/";


        // Folder না থাকলে তৈরি করবে

        if (!is_dir($upload_folder)) {

            mkdir($upload_folder, 0777, true);

        }


        // File names

        $student_id_name =
            $std_id . "_student_id_" .
            basename($student_id_card["name"]);


        $passport_name =
            $std_id . "_passport_" .
            basename($passport["name"]);


        // Full paths

        $student_id_path =
            $upload_folder . $student_id_name;


        $passport_path =
            $upload_folder . $passport_name;


        // ====================================================
        // UPLOAD FILES
        // ====================================================

        if (
            move_uploaded_file(
                $student_id_card["tmp_name"],
                $student_id_path
            )
            &&
            move_uploaded_file(
                $passport["tmp_name"],
                $passport_path
            )
        ) {


            // =================================================
            // CHECK IF OLD REJECTED RECORD EXISTS
            // =================================================

            $check_old = "SELECT * FROM Verification_doc
                          WHERE Std_ID = '$std_id'";

            $old_result = mysqli_query($conn, $check_old);


            if (mysqli_num_rows($old_result) > 0) {

                // Rejected application আছে
                // তাই UPDATE করছি

                $sql = "UPDATE Verification_doc

                        SET Admin_ID = NULL,
                            University_ID = '$university_id',
                            University_Name = '$university_name',
                            University_Email = '$university_email',
                            Verification_Status = 'Pending',
                            Submitted_at = CURRENT_TIMESTAMP,
                            Reviewed_at = NULL

                        WHERE Std_ID = '$std_id'";


                mysqli_query($conn, $sql);


                // পুরোনো document records remove

                $delete_docs =
                    "DELETE FROM Verification_doc_FileURL
                     WHERE Std_ID = '$std_id'";

                mysqli_query($conn, $delete_docs);

            }


            else {

                // =================================================
                // NEW VERIFICATION
                // =================================================

                $sql = "INSERT INTO Verification_doc

                        (Std_ID, Admin_ID, DocType,
                         University_ID, University_Name,
                         University_Email,
                         Verification_Status)

                        VALUES

                        ('$std_id', NULL,
                         'Student Verification',
                         '$university_id',
                         '$university_name',
                         '$university_email',
                         'Pending')";


                mysqli_query($conn, $sql);

            }


            // =================================================
            // SAVE STUDENT ID CARD
            // =================================================

            $sql1 = "INSERT INTO Verification_doc_FileURL

                     (Std_ID, DocType, FileURL)

                     VALUES

                     ('$std_id',
                      'Student ID Card',
                      '$student_id_path')";


            mysqli_query($conn, $sql1);


            // =================================================
            // SAVE PASSPORT / NID
            // =================================================

            $sql2 = "INSERT INTO Verification_doc_FileURL

                     (Std_ID, DocType, FileURL)

                     VALUES

                     ('$std_id',
                      'Passport / NID',
                      '$passport_path')";


            mysqli_query($conn, $sql2);


            // =================================================
            // SAVE UNIVERSITY INFORMATION IN STUDENT TABLE
            // =================================================

            $sql3 = "UPDATE Student

                     SET University_ID = '$university_id',
                         University_Name = '$university_name',
                         University_Email = '$university_email',
                         is_Verified = 0

                     WHERE Std_ID = '$std_id'";


            mysqli_query($conn, $sql3);


            $message =
                "Verification submitted successfully! Please wait for admin approval.";

        }

        else {

            $message = "File upload failed.";

        }

    }

}

?>


<!DOCTYPE html>
<html>

<head>

    <title>Global Nest - Verify Account</title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family: Arial, sans-serif;

            background-color: #f5f5f5;

            min-height: 100vh;

        }


        /* HEADER */

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


        /* FORM CONTAINER */

        .container {

            width: 500px;

            max-width: 90%;

            margin: 40px auto;

            background-color: white;

            padding: 30px;

            border-radius: 12px;

            box-shadow: 0 3px 10px rgba(0,0,0,0.15);

        }


        h2 {

            text-align: center;

            color: #000080;

        }


        .description {

            text-align: center;

            color: #666;

            margin-bottom: 25px;

        }


        /* FORM */

        label {

            display: block;

            margin-top: 15px;

            margin-bottom: 6px;

            font-weight: bold;

        }


        input {

            width: 100%;

            padding: 10px;

            border: 1px solid #ccc;

            border-radius: 6px;

            font-size: 14px;

        }


        input:focus {

            outline: none;

            border-color: #000080;

        }


        /* BUTTON */

        .submit-button {

            width: 100%;

            margin-top: 25px;

            padding: 12px;

            background-color: #000080;

            color: white;

            border: none;

            border-radius: 6px;

            font-size: 16px;

            cursor: pointer;

        }


        .submit-button:hover {

            background-color: #000066;

        }


        /* MESSAGE */

        .message {

            text-align: center;

            margin-top: 20px;

            color: #000080;

            font-weight: bold;

        }

    </style>

</head>


<body>


<!-- HEADER -->

<div class="header">

    <h2>Global Nest</h2>

    <a href="dashboard.php">
        Back to Dashboard
    </a>

</div>



<!-- VERIFICATION FORM -->

<div class="container">


    <h2>Verify Your Account</h2>


    <p class="description">

        Please provide your university information and documents.

    </p>


    <form method="POST" enctype="multipart/form-data">


        <!-- UNIVERSITY STUDENT ID -->

        <label>
            University Student ID
        </label>

        <input
            type="text"
            name="university_id"
            placeholder="Enter your university student ID"
            required
        >


        <!-- UNIVERSITY NAME -->

        <label>
            University Name
        </label>

        <input
            type="text"
            name="university_name"
            placeholder="Enter your university name"
            required
        >


        <!-- UNIVERSITY EMAIL -->

        <label>
            University EDU Email
        </label>

        <input
            type="email"
            name="university_email"
            placeholder="example@university.edu"
            required
        >


        <!-- STUDENT ID CARD -->

        <label>
            Student ID Card
        </label>

        <input
            type="file"
            name="student_id_card"
            accept="image/*,.pdf"
            required
        >


        <!-- NID / PASSPORT -->

        <label>
            NID / Passport
        </label>

        <input
            type="file"
            name="passport"
            accept="image/*,.pdf"
            required
        >


        <!-- SUBMIT -->

        <input
            type="submit"
            name="verify"
            value="Submit for Verification"
            class="submit-button"
        >


    </form>



    <!-- MESSAGE -->

    <?php if ($message != "") { ?>

        <div class="message">

            <?php echo $message; ?>

        </div>

    <?php } ?>


</div>


</body>

</html>