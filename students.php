<?php

session_start();

include "DBconnect.php";


// Admin login করা আছে কিনা check
if (!isset($_SESSION["loggedIn"]) || $_SESSION["userType"] != "admin") {

    header("Location: login.php");
    exit();

}


// সব student বের করা
$sql = "SELECT * FROM Student ORDER BY Std_ID";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Global Nest - Students</title>

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

            width: 95%;

            max-width: 1200px;

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


        /* ================= TABLE ================= */

        .table-container {

            background-color: white;

            padding: 20px;

            border-radius: 10px;

            box-shadow: 0 3px 10px rgba(0,0,0,0.12);

            overflow-x: auto;

        }


        table {

            width: 100%;

            border-collapse: collapse;

        }


        th {

            background-color: #000080;

            color: white;

            padding: 14px;

            text-align: left;

        }


        td {

            padding: 13px;

            border-bottom: 1px solid #ddd;

        }


        tr:hover {

            background-color: #f5f5f5;

        }


        /* ================= STATUS ================= */

        .verified {

            color: green;

            font-weight: bold;

        }


        .not-verified {

            color: #d97706;

            font-weight: bold;

        }

    </style>

</head>


<body>


<!-- ================= HEADER ================= -->

<div class="header">

    <h2>Global Nest - Admin</h2>

    <a href="logout.php">Logout</a>

</div>


<!-- ================= MAIN ================= -->

<div class="container">


    <div class="top">

        <h1>All Students</h1>

        <a href="admin_dashboard.php" class="back-button">
            Back to Dashboard
        </a>

    </div>


    <div class="table-container">

        <table>

            <tr>

                <th>Student ID</th>

                <th>Username</th>

                <th>Name</th>

                <th>Email</th>

                <th>University</th>

                <th>Status</th>

            </tr>


            <?php

            if (mysqli_num_rows($result) > 0) {

                while ($student = mysqli_fetch_assoc($result)) {

            ?>


            <tr>

                <!-- Student ID -->

                <td>
                    <?php echo htmlspecialchars($student["Std_ID"]); ?>
                </td>


                <!-- Username -->

                <td>
                    <?php echo htmlspecialchars($student["Username"]); ?>
                </td>


                <!-- Name -->

                <td>

                    <?php

                    echo htmlspecialchars(
                        $student["First_name"] . " " .
                        $student["Last_name"]
                    );

                    ?>

                </td>


                <!-- Email -->

                <td>
                    <?php echo htmlspecialchars($student["Email"]); ?>
                </td>


                <!-- University -->

                <td>

                    <?php

                    if ($student["University_Name"] != NULL) {

                        echo htmlspecialchars(
                            $student["University_Name"]
                        );

                    } else {

                        echo "Not provided";

                    }

                    ?>

                </td>


                <!-- Verification Status -->

                <td>

                    <?php

                    if ($student["is_Verified"] == 1) {

                        echo '<span class="verified">Verified</span>';

                    } else {

                        echo '<span class="not-verified">Not Verified</span>';

                    }

                    ?>

                </td>


            </tr>


            <?php

                }

            } else {

            ?>


            <tr>

                <td colspan="6" style="text-align:center;">

                    No students found.

                </td>

            </tr>


            <?php

            }

            ?>

        </table>

    </div>


</div>


</body>

</html>