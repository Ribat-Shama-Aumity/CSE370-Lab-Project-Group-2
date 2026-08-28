<?php

session_start();


// Only admin can access this page
if (
    !isset($_SESSION["loggedIn"]) ||
    $_SESSION["userType"] != "admin"
) {

    header("Location: login.php");
    exit();

}


include "DBconnect.php";


$message = "";


// ==========================================
// APPROVE LISTING
// ==========================================

if (isset($_GET["approve"])) {

    $listing_id = $_GET["approve"];


    $sql = "UPDATE Listings
            SET Verification_Status = 'Approved'
            WHERE ListingID = '$listing_id'";


    if (mysqli_query($conn, $sql)) {

        $message = "Listing approved successfully!";

    }

}


// ==========================================
// REJECT LISTING
// ==========================================

if (isset($_GET["reject"])) {

    $listing_id = $_GET["reject"];


    $sql = "UPDATE Listings
            SET Verification_Status = 'Rejected'
            WHERE ListingID = '$listing_id'";


    if (mysqli_query($conn, $sql)) {

        $message = "Listing rejected.";

    }

}


// ==========================================
// GET PENDING LISTINGS
// ==========================================

$sql = "SELECT Listings.*, 
               Room_Provider.First_name,
               Room_Provider.Last_name,
               Room_Provider.Email,
               Room_Provider.Username
        FROM Listings
        JOIN Room_Provider
        ON Listings.Provider_ID = Room_Provider.Provider_ID
        WHERE Listings.Verification_Status = 'Pending'
        ORDER BY Listings.ListingID DESC";


$result = mysqli_query($conn, $sql);

?>


<!DOCTYPE html>
<html>

<head>

    <title>Listing Verification - Global Nest</title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family: Arial, sans-serif;

            background-color: #f5f5f5;

        }


        /* HEADER */

        .header {

            background-color: navy;

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


        .header a:hover {

            text-decoration: underline;

        }


        /* CONTAINER */

        .container {

            width: 90%;

            max-width: 1100px;

            margin: 40px auto;

        }


        h1 {

            color: navy;

        }


        /* MESSAGE */

        .message {

            background-color: #d4edda;

            color: #155724;

            padding: 12px;

            border-radius: 6px;

            margin-bottom: 20px;

        }


        /* LISTING CARD */

        .listing-card {

            background-color: white;

            padding: 25px;

            margin-bottom: 25px;

            border-radius: 10px;

            box-shadow:
                0 2px 8px rgba(0,0,0,0.12);

        }


        .listing-card h2 {

            color: navy;

            margin-top: 0;

        }


        .info {

            margin: 8px 0;

            color: #444;

        }


        .provider {

            background-color: #f0f0f0;

            padding: 15px;

            border-radius: 7px;

            margin-top: 15px;

        }


        .document {

            margin-top: 20px;

        }


        .document a {

            color: navy;

            font-weight: bold;

        }


        /* BUTTONS */

        .approve-button {

            display: inline-block;

            margin-top: 20px;

            padding: 10px 18px;

            background-color: green;

            color: white;

            text-decoration: none;

            border-radius: 5px;

            margin-right: 10px;

        }


        .approve-button:hover {

            background-color: darkgreen;

        }


        .reject-button {

            display: inline-block;

            margin-top: 20px;

            padding: 10px 18px;

            background-color: #d00000;

            color: white;

            text-decoration: none;

            border-radius: 5px;

        }


        .reject-button:hover {

            background-color: #a00000;

        }


        .no-listing {

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
        Listing Verification
    </h1>


    <p>
        Review room listings submitted by providers.
    </p>



    <?php if ($message != "") { ?>

        <div class="message">

            <?php echo $message; ?>

        </div>

    <?php } ?>



    <?php if ($result && mysqli_num_rows($result) > 0) { ?>


        <?php while ($listing = mysqli_fetch_assoc($result)) { ?>


            <div class="listing-card">


                <h2>

                    <?php
                    echo htmlspecialchars(
                        $listing["RoomType"]
                    );
                    ?>

                </h2>


                <!-- PRICE -->

                <div class="info">

                    <strong>
                        Price:
                    </strong>

                    <?php
                    echo htmlspecialchars(
                        $listing["Price"]
                    );
                    ?>

                    <?php
                    echo htmlspecialchars(
                        $listing["Currency"]
                    );
                    ?>

                    / month

                </div>



                <!-- LOCATION -->

                <div class="info">

                    <strong>
                        Location:
                    </strong>

                    <?php
                    echo htmlspecialchars(
                        $listing["Neighbourhood"]
                    );
                    ?>,

                    <?php
                    echo htmlspecialchars(
                        $listing["State"]
                    );
                    ?>,

                    <?php
                    echo htmlspecialchars(
                        $listing["Country"]
                    );
                    ?>

                </div>



                <!-- DISTANCES -->

                <div class="info">

                    <strong>
                        Clinic:
                    </strong>

                    <?php
                    echo htmlspecialchars(
                        $listing["Clinic"]
                    );
                    ?>

                    km

                </div>


                <div class="info">

                    <strong>
                        Grocery:
                    </strong>

                    <?php
                    echo htmlspecialchars(
                        $listing["Grocery"]
                    );
                    ?>

                    km

                </div>


                <div class="info">

                    <strong>
                        Campus:
                    </strong>

                    <?php
                    echo htmlspecialchars(
                        $listing["Campus"]
                    );
                    ?>

                    km

                </div>


                                <!-- ====================================================
                     PHOTOS AND UTILITIES
                     ----------------------------------------------------
                     These two queries run once for every listing card,
                     because each listing has its own photos and its own
                     utility amounts.
                ==================================================== -->

                <?php

                $this_id = $listing["ListingID"];


                // ----- photos of this listing -----

                $photo_sql = "SELECT * FROM Listing_Photo
                              WHERE ListingID = '$this_id'
                              ORDER BY PhotoID ASC";

                $photo_result = mysqli_query($conn, $photo_sql);

                ?>


                <div class="info" style="margin-top:15px;">

                    <strong>Room Photos:</strong>

                </div>


                <?php if (mysqli_num_rows($photo_result) > 0) { ?>

                    <div style="display:flex; gap:10px; flex-wrap:wrap;
                                margin-top:8px;">

                        <?php while ($photo = mysqli_fetch_assoc($photo_result)) { ?>

                            <a href="<?php echo htmlspecialchars($photo["PhotoURL"]); ?>"
                               target="_blank">

                                <img
                                    src="<?php echo htmlspecialchars($photo["PhotoURL"]); ?>"
                                    style="width:130px; height:95px;
                                           object-fit:cover; border-radius:7px;"
                                    alt="Room photo">

                            </a>

                        <?php } ?>

                    </div>

                <?php } else { ?>

                    <div class="info" style="color:#888;">
                        No photos uploaded.
                    </div>

                <?php } ?>


                <?php

                // ----- utilities of this listing -----

                $utility_sql = "SELECT * FROM Listing_Utility
                                WHERE ListingID = '$this_id'
                                ORDER BY UtilityName ASC";

                $utility_result = mysqli_query($conn, $utility_sql);

                ?>


                <div class="info" style="margin-top:15px;">

                    <strong>Utilities:</strong>


                    <?php if (mysqli_num_rows($utility_result) > 0) { ?>

                        <?php while ($utility = mysqli_fetch_assoc($utility_result)) { ?>

                            <?php
                            echo htmlspecialchars($utility["UtilityName"]);
                            ?>

                            <?php
                            echo htmlspecialchars($utility["Amount"]);
                            ?>

                            <?php
                            echo htmlspecialchars($listing["Currency"]);
                            ?>

                            &nbsp;&nbsp;

                        <?php } ?>

                    <?php } else { ?>

                        <span style="color:#888;">
                            None added.
                        </span>

                    <?php } ?>

                </div>



                <!-- PROVIDER -->

                <div class="provider">

                    <strong>
                        Provider Information
                    </strong>


                    <div class="info">

                        Name:

                        <?php
                        echo htmlspecialchars(
                            $listing["First_name"]
                        );
                        ?>

                        <?php
                        echo htmlspecialchars(
                            $listing["Last_name"]
                        );
                        ?>

                    </div>


                    <div class="info">

                        Username:

                        <?php
                        echo htmlspecialchars(
                            $listing["Username"]
                        );
                        ?>

                    </div>


                    <div class="info">

                        Email:

                        <?php
                        echo htmlspecialchars(
                            $listing["Email"]
                        );
                        ?>

                    </div>

                </div>



                <!-- LEGAL DOCUMENT -->

                <div class="document">

                    <strong>
                        Legal Document:
                    </strong>


                    <?php if ($listing["Legal_doc"] != "") { ?>

                        <br><br>

                        <a
                            href="<?php
                            echo htmlspecialchars(
                                $listing["Legal_doc"]
                            );
                            ?>"
                            target="_blank"
                        >
                            View Legal Document
                        </a>

                    <?php } else { ?>

                        <p>
                            No document uploaded.
                        </p>

                    <?php } ?>

                </div>



                <!-- BUTTONS -->

                <a
                    href="listing_verification.php?approve=<?php
                    echo $listing["ListingID"];
                    ?>"
                    class="approve-button"
                    onclick="return confirm('Approve this listing?');"
                >

                    Approve

                </a>


                <a
                    href="listing_verification.php?reject=<?php
                    echo $listing["ListingID"];
                    ?>"
                    class="reject-button"
                    onclick="return confirm('Reject this listing?');"
                >

                    Reject

                </a>


            </div>


        <?php } ?>


    <?php } else { ?>


        <div class="no-listing">

            <h2>
                No Pending Listings
            </h2>

            <p>
                There are currently no listings waiting for verification.
            </p>

        </div>


    <?php } ?>


</div>


</body>

</html>