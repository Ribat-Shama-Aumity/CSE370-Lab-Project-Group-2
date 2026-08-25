<?php

session_start();


// Only Room Provider can access this page
if (
    !isset($_SESSION["loggedIn"]) ||
    $_SESSION["userType"] != "provider"
) {
    header("Location: provider_login.php");
    exit();
}


include "DBconnect.php";


$provider_id = $_SESSION["Provider_ID"];


// Delete listing
if (isset($_GET["delete"])) {

    $listing_id = $_GET["delete"];


    $sql = "DELETE FROM Listings
            WHERE ListingID = '$listing_id'
            AND Provider_ID = '$provider_id'";


    if (mysqli_query($conn, $sql)) {

        header("Location: provider_dashboard.php");
        exit();

    }

}


// Get provider's listings
$sql = "SELECT * FROM Listings
        WHERE Provider_ID = '$provider_id'
        ORDER BY ListingID DESC";


$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Provider Dashboard - Global Nest</title>


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


        /* MAIN CONTAINER */

        .container {

            width: 90%;

            max-width: 1100px;

            margin: 40px auto;

        }


        /* WELCOME */

        .welcome {

            background-color: white;

            padding: 25px;

            border-radius: 10px;

            margin-bottom: 25px;

            box-shadow:
                0 2px 8px rgba(0,0,0,0.10);

        }


        .welcome h1 {

            margin-top: 0;

            color: navy;

        }


        /* ADD BUTTON */

        .add-button {

            display: inline-block;

            background-color: navy;

            color: white;

            padding: 12px 20px;

            text-decoration: none;

            border-radius: 6px;

            margin-top: 10px;

        }


        .add-button:hover {

            background-color: #000066;

        }


        /* LISTINGS */

        .listings-title {

            margin-top: 30px;

            color: navy;

        }


        .listing-grid {

            display: grid;

            grid-template-columns:
                repeat(auto-fit, minmax(280px, 1fr));

            gap: 20px;

        }


        /* LISTING CARD */

        .listing-card {

            background-color: white;

            padding: 20px;

            border-radius: 10px;

            box-shadow:
                0 2px 8px rgba(0,0,0,0.10);

        }


        .listing-card h3 {

            color: navy;

            margin-top: 0;

        }


        .listing-info {

            margin: 8px 0;

            color: #555;

        }


        .price {

            font-size: 20px;

            font-weight: bold;

            color: navy;

            margin: 12px 0;

        }


        /* STATUS */

        .status {

            display: inline-block;

            padding: 6px 12px;

            border-radius: 15px;

            font-size: 13px;

            font-weight: bold;

        }


        .pending {

            background-color: #fff3cd;

            color: #856404;

        }


        .approved {

            background-color: #d4edda;

            color: #155724;

        }


        .rejected {

            background-color: #f8d7da;

            color: #721c24;

        }


        /* DELETE BUTTON */

        .delete-button {

            display: inline-block;

            margin-top: 15px;

            padding: 9px 15px;

            background-color: #d00000;

            color: white;

            text-decoration: none;

            border-radius: 5px;

        }


        .delete-button:hover {

            background-color: #a00000;

        }


        /* NO LISTING */

        .no-listing {

            background-color: white;

            padding: 30px;

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

        Global Nest

    </div>


    <div class="header-right">

        <span>

            Hi,
            <?php
            echo htmlspecialchars($_SESSION["provider_name"]);
            ?>

        </span>


        <a href="logout.php">
            Logout
        </a>

    </div>


</div>



<!-- MAIN -->

<div class="container">


    <!-- WELCOME -->

    <div class="welcome">

        


        <p>

            Welcome,
            <?php
            echo htmlspecialchars($_SESSION["provider_name"]);
            ?>

        </p>


        <a
            href="add_listing.php"
            class="add-button"
        >

            + Add New Listing

        </a>

    </div>



    <!-- MY LISTINGS -->

    <h2 class="listings-title">

        My Listings

    </h2>



    <?php if ($result && mysqli_num_rows($result) > 0) { ?>


        <div class="listing-grid">


            <?php while ($listing = mysqli_fetch_assoc($result)) { ?>


                <div class="listing-card">


                    <h3>

                        <?php
                        echo htmlspecialchars(
                            $listing["RoomType"]
                        );
                        ?>

                    </h3>


                    <div class="price">

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

                        <span
                            style="
                            font-size:14px;
                            color:#777;
                            "
                        >
                            / month
                        </span>

                    </div>


                    <div class="listing-info">

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


                    <div class="listing-info">

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


                    <div class="listing-info">

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


                    <div class="listing-info">

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


                    <!-- STATUS -->

                    <div style="margin-top:15px;">

                        <strong>
                            Status:
                        </strong>


                        <?php

                        $status =
                            $listing["Verification_Status"];


                        if ($status == "Pending") {

                            echo '<span class="status pending">
                                    Pending
                                  </span>';

                        }

                        else if ($status == "Approved") {

                            echo '<span class="status approved">
                                    Approved
                                  </span>';

                        }

                        else if ($status == "Rejected") {

                            echo '<span class="status rejected">
                                    Rejected
                                  </span>';

                        }

                        else {

                            echo '<span class="status pending">'
                                . htmlspecialchars($status)
                                . '</span>';

                        }

                        ?>

                    </div>



                    <!-- DELETE -->

                    <a
                        href="provider_dashboard.php?delete=<?php echo $listing["ListingID"]; ?>"
                        class="delete-button"
                        onclick="return confirm('Are you sure you want to delete this listing?');"
                    >

                        Delete

                    </a>


                </div>


            <?php } ?>


        </div>


    <?php } else { ?>


        <div class="no-listing">

            <h3>
                No Listings Yet
            </h3>

            <p>
                You have not added any room listings.
            </p>

            <a
                href="add_listing.php"
                class="add-button"
            >
                Add Your First Listing
            </a>

        </div>


    <?php } ?>


</div>


</body>

</html>