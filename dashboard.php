<?php

session_start();

/* Check if student is logged in */
if (!isset($_SESSION["loggedIn"]) || $_SESSION["userType"] != "student") {
    header("Location: login.php");
    exit();
}

/* Connect to database */
include "DBconnect.php";

/* ===== FILTER FEATURE ===== */
include "filter.php";

/* Get all listings */
$sql = "SELECT * FROM Listings " . $filter . " ORDER BY ListingID DESC";

$result = mysqli_query($conn, $sql);

$roomCount = mysqli_num_rows($result);

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Global Nest - Available Rooms</title>

<style>

    * {
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        background-color: #f5f5f5;
        color: #222;
    }

    /* HEADER */

    .header {
        width: 100%;
        height: 75px;
        background-color: #000080;

        display: flex;
        justify-content: space-between;
        align-items: center;

        padding: 0 40px;
    }

    .logo {
        color: white;
        font-size: 24px;
    }

    .navigation {
        display: flex;
        align-items: center;
    }

    .navigation span,
    .navigation a {
        color: white;
        text-decoration: none;
        font-size: 16px;
        margin-left: 25px;
    }

    .navigation a:hover {
        text-decoration: underline;
    }


    /* HERO */

    .home-hero {
        width: 100%;
        padding: 60px 40px;

        text-align: center;

        background: linear-gradient(
            135deg,
            #000080,
            #000066
        );
    }

    .home-hero h1 {
        color: white;
        font-size: 38px;
        margin: 0 0 12px 0;
    }

    .home-hero p {
        color: white;
        font-size: 18px;
    }

    .room-count {
        font-weight: bold;
    }


    /* ROOM GRID */

    .room-grid {
        display: grid;

        grid-template-columns:
        repeat(auto-fill, minmax(280px, 1fr));

        gap: 25px;

        padding: 35px 40px 50px 40px;
    }


    /* ROOM CARD */

    .room-card {
        background-color: white;

        border-radius: 10px;

        padding: 20px;

        box-shadow:
        0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .room-card:hover {
        transform: translateY(-4px);
    }

    .room-card h3 {
        margin-top: 0;

        color: #000080;

        font-size: 20px;
    }

    .room-location {
        color: #666;

        font-size: 14px;

        margin-bottom: 10px;
    }

    .room-price {
        font-size: 20px;

        font-weight: bold;

        color: #000080;

        margin-bottom: 10px;
    }

    .room-info {
        font-size: 14px;

        margin-bottom: 8px;
    }

    .interest-button {
        width: 100%;

        height: 42px;

        background-color: #000080;

        color: white;

        border: none;

        border-radius: 6px;

        font-size: 15px;

        cursor: pointer;
    }

    .interest-button:hover {
        background-color: #000066;
    }


    /* NO ROOMS */

    .no-rooms {
        padding: 60px;

        text-align: center;

        color: #666;

        font-size: 18px;
    }

    .verified-badge {
        display: inline-flex;
        justify-content: center;
        align-items: center;

        width: 22px;
        height: 22px;

        background-color: #1877f2;
        color: white;

        border-radius: 50%;

        font-size: 14px;
        font-weight: bold;

        margin-left: 8px;

        vertical-align: middle;
    }


</style>

</head>


<body>


<!-- HEADER -->

<div class="header">

    <div class="logo">

        <strong>Global Nest</strong>
        - Student Room Matcher

    </div>


    <div class="navigation">

        <span>
            Hi,
            <?php echo $_SESSION["full_name"]; ?>
        </span>

        <a href="verify.php">
            Verify Account
        </a>
		
		<a href="faq.php">
            FAQ
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>



</div>



<!-- HERO -->

<div class="home-hero">

    <h1>

        Welcome back,
        <?php echo htmlspecialchars($_SESSION["full_name"]); ?>

        <?php if ($_SESSION["is_Verified"] == 1) { ?>

            <span class="verified-badge">✓</span>

        <?php } ?>

    </h1>


    <p>

        We found

        <span class="room-count">
            <?php echo $roomCount; ?>
        </span>

        available listings.

    </p>

</div>

<!-- ===== FILTER FEATURE ===== -->
<?php include "filter_box.php"; ?>



<!-- ROOM GRID -->

<?php if ($roomCount > 0) { ?>


<div class="<?php echo $view_class; ?>">


    <?php while ($room = mysqli_fetch_assoc($result)) { ?>


    <div class="room-card">


        <h3>

            <?php echo $room["RoomType"]; ?>

        </h3>


        <div class="room-location">

            <?php echo $room["Neighbourhood"]; ?>,

            <?php echo $room["State"]; ?>,

            <?php echo $room["Country"]; ?>

        </div>


        <div class="room-price">

            <?php echo number_format($room["Price"], 2); ?>
            <?php echo htmlspecialchars($room["Currency"]); ?>

            <span style="font-size:13px;">
                / month
            </span>

        </div>


        <div class="room-info">

            <strong>Clinic:</strong>

            <?php echo $room["Clinic"]; ?> km

        </div>


        <div class="room-info">

            <strong>Grocery:</strong>

            <?php echo $room["Grocery"]; ?> km

        </div>


        <div class="room-info">

            <strong>Campus:</strong>

            <?php echo $room["Campus"]; ?> km

        </div>


        <button
            class="interest-button"
            type="button">

            I'm Interested

        </button>


    </div>


    <?php } ?>


</div>


<?php } else { ?>


<div class="no-rooms">

    No rooms available right now.

</div>


<?php } ?>


</body>

</html>