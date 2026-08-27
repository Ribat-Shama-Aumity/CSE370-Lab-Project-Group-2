<?php

session_start();

/* Only admin can access */
if (!isset($_SESSION["loggedIn"]) || $_SESSION["userType"] != "admin") {
    header("Location: login.php");
    exit();
}

include "DBconnect.php";


/* Get all listings */

$sql = "SELECT * FROM Listings
        ORDER BY ListingID DESC";

$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>All Listings - Global Nest</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f5f5f5;
        }

        .header {
            background-color: #000080;
            color: white;
            padding: 20px 40px;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
        }

        .header a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        .container {
            width: 90%;
            margin: 40px auto;
        }

        h1 {
            color: #000080;
        }

        .listing {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;

            border-radius: 10px;

            box-shadow:
                0 2px 8px rgba(0,0,0,0.12);
        }

        .listing h2 {
            color: #000080;
            margin-top: 0;
        }

        .info {
            margin: 8px 0;
        }

        .pending {
            color: #c58b00;
            font-weight: bold;
        }

        .approved {
            color: green;
            font-weight: bold;
        }

        .rejected {
            color: red;
            font-weight: bold;
        }

        .document {
            margin-top: 15px;
        }

        .document a {
            color: #000080;
            font-weight: bold;
        }

        .back-button {
            display: inline-block;

            background-color: #000080;
            color: white;

            padding: 10px 18px;

            text-decoration: none;

            border-radius: 6px;

            margin-bottom: 20px;
        }

        .no-listings {
            background-color: white;
            padding: 40px;
            text-align: center;
            border-radius: 10px;
            color: #666;
        }

    </style>

</head>


<body>


<!-- HEADER -->

<div class="header">

    <div class="logo">
        Global Nest - Admin
    </div>

    <div>

        <a href="admin_dashboard.php">
            Dashboard
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>

</div>


<!-- MAIN -->

<div class="container">

    <h1>
        All Listings
    </h1>


    <a href="admin_dashboard.php" class="back-button">
        Back to Dashboard
    </a>


    <?php if ($result && mysqli_num_rows($result) > 0) { ?>


        <?php while ($listing = mysqli_fetch_assoc($result)) { ?>


            <div class="listing">


                <h2>

                    <?php
                    echo htmlspecialchars($listing["RoomType"]);
                    ?>

                </h2>


                <div class="info">

                    <strong>
                        Price:
                    </strong>

                    <?php
                    echo htmlspecialchars($listing["Price"]);
                    ?>

                    <?php
                    echo htmlspecialchars($listing["Currency"]);
                    ?>

                    / month

                </div>


                <div class="info">

                    <strong>
                        Location:
                    </strong>

                    <?php
                    echo htmlspecialchars($listing["Neighbourhood"]);
                    ?>,

                    <?php
                    echo htmlspecialchars($listing["State"]);
                    ?>,

                    <?php
                    echo htmlspecialchars($listing["Country"]);
                    ?>

                </div>


                <div class="info">

                    <strong>
                        Clinic:
                    </strong>

                    <?php
                    echo htmlspecialchars($listing["Clinic"]);
                    ?>
                    km

                </div>


                <div class="info">

                    <strong>
                        Grocery:
                    </strong>

                    <?php
                    echo htmlspecialchars($listing["Grocery"]);
                    ?>
                    km

                </div>


                <div class="info">

                    <strong>
                        Campus:
                    </strong>

                    <?php
                    echo htmlspecialchars($listing["Campus"]);
                    ?>
                    km

                </div>


                <!-- STATUS -->

                <div class="info">

                    <strong>
                        Status:
                    </strong>


                    <?php

                    if ($listing["Verification_Status"] == "Pending") {

                        echo '<span class="pending">
                                Pending
                              </span>';

                    }

                    else if ($listing["Verification_Status"] == "Approved") {

                        echo '<span class="approved">
                                Approved
                              </span>';

                    }

                    else if ($listing["Verification_Status"] == "Rejected") {

                        echo '<span class="rejected">
                                Rejected
                              </span>';

                    }

                    ?>

                </div>


                <!-- LEGAL DOCUMENT -->

                <?php if (!empty($listing["Legal_doc"])) { ?>

                    <div class="document">

                        <strong>
                            Legal Document:
                        </strong>

                        <a
                            href="<?php echo htmlspecialchars($listing["Legal_doc"]); ?>"
                            target="_blank"
                        >
                            View Document
                        </a>

                    </div>

                <?php } ?>


            </div>


        <?php } ?>


    <?php } else { ?>


        <div class="no-listings">

            No listings found.

        </div>


    <?php } ?>


</div>


</body>

</html>