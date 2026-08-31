<?php

session_start();

include "DBconnect.php";

/* Only admin can view documents */
if (!isset($_SESSION["loggedIn"]) || $_SESSION["userType"] != "admin") {
    header("Location: login.php");
    exit();
}


/* Get student ID from URL */

if (!isset($_GET["std_id"])) {
    echo "Student ID not found.";
    exit();
}

$std_id = mysqli_real_escape_string($conn, $_GET["std_id"]);


/* Get verification document information */

$sql = "SELECT * FROM Verification_doc
        WHERE Std_ID = '$std_id'";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {

    echo "No verification documents found.";
    exit();

}

$document = mysqli_fetch_assoc($result);


/* Get uploaded files */

$sql2 = "SELECT * FROM Verification_doc_FileURL
         WHERE Std_ID = '$std_id'";

$result2 = mysqli_query($conn, $sql2);

?>

<!DOCTYPE html>
<html>

<head>

    <title>View Student Documents</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 40px;
        }

        .container {
            width: 600px;
            margin: auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        }

        h2 {
            color: #000080;
            text-align: center;
        }

        p {
            font-size: 16px;
        }

        .document {
            background-color: #f1f1f1;
            padding: 15px;
            margin-top: 15px;
            border-radius: 7px;
        }

        .document a {
            color: #000080;
            font-weight: bold;
            text-decoration: none;
        }

        .document a:hover {
            text-decoration: underline;
        }

        .back-button {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 18px;
            background-color: #000080;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

    </style>

</head>


<body>


<div class="container">

    <h2>Student Verification Documents</h2>


    <p>
        <strong>Student ID:</strong>
        <?php echo htmlspecialchars($std_id); ?>
    </p>


    <p>
        <strong>Document Type:</strong>
        <?php echo htmlspecialchars($document["DocType"]); ?>
    </p>


    <h3>Uploaded Files</h3>


    <?php if (mysqli_num_rows($result2) > 0) { ?>


        <?php while ($file = mysqli_fetch_assoc($result2)) { ?>

            <div class="document">

                <?php echo htmlspecialchars($file["FileURL"]); ?>

                <br><br>

                <a
                    href="<?php echo htmlspecialchars($file["FileURL"]); ?>"
                    target="_blank"
                >
                    Open Document
                </a>

            </div>

        <?php } ?>


    <?php } else { ?>

        <p>No uploaded files found.</p>

    <?php } ?>


    <a href="student_verification.php" class="back-button">
        Back
    </a>


</div>


</body>

</html>