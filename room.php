<?php

session_start();


// Only a logged-in student can open a room page
if (!isset($_SESSION["loggedIn"]) || $_SESSION["userType"] != "student") {

    header("Location: login.php");
    exit();

}


include "DBconnect.php";


$std_id = $_SESSION["Std_ID"];


// ============================================================
// PART 1 : WHICH ROOM ARE WE SHOWING
// ------------------------------------------------------------
// The id arrives in the address bar, like
//     room.php?id=8
// ============================================================

$listing_id = "";

if (isset($_GET["id"])) {
    $listing_id = $_GET["id"];
}


// If somebody types a silly id, send them back

if ($listing_id == "" || !is_numeric($listing_id)) {

    header("Location: dashboard.php");
    exit();

}


$safe_id = mysqli_real_escape_string($conn, $listing_id);


// ============================================================
// PART 2 : FAVOURITE BUTTON
// ------------------------------------------------------------
// The Bookmark table already exists and its primary key is
// (Std_ID, ListingID), so the same student cannot save the
// same room twice.
// ============================================================

if (isset($_GET["fav"])) {

    $fav = $_GET["fav"];


    if ($fav == "add") {

        $sql = "INSERT INTO bookmarks (Std_ID, ListingID)
                VALUES ('$std_id', '$safe_id')";

        mysqli_query($conn, $sql);

    }

    else if ($fav == "remove") {

        $sql = "DELETE FROM bookmarks
                WHERE Std_ID = '$std_id'
                AND ListingID = '$safe_id'";

        mysqli_query($conn, $sql);

    }


    // Go back to a clean address, so refreshing the page
    // does not try to add the bookmark all over again

    header("Location: room.php?id=" . $safe_id);
    exit();

}


// ============================================================
// PART 3 : GET THE ROOM
// ------------------------------------------------------------
// JOIN brings the provider's name in from Room_Provider.
// A plain JOIN is fine here because Provider_ID can never
// be NULL - every listing must have an owner.
//
// We also check Verification_Status, so a student cannot
// open a Pending room by typing its id by hand.
// ============================================================

$sql = "SELECT

            Listings.*,

            Room_Provider.First_name,
            Room_Provider.Last_name

        FROM Listings

        JOIN Room_Provider

        ON Listings.Provider_ID = Room_Provider.Provider_ID

        WHERE Listings.ListingID = '$safe_id'

        AND Listings.Verification_Status = 'Approved'";


$result = mysqli_query($conn, $sql);


// Room not found, or not approved yet

if (mysqli_num_rows($result) == 0) {

    header("Location: dashboard.php");
    exit();

}


$room = mysqli_fetch_assoc($result);


// ============================================================
// PART 4 : IS THIS ROOM ALREADY A FAVOURITE
// ------------------------------------------------------------
// We only need to know YES or NO, so we count the rows.
// ============================================================

$fav_sql = "SELECT * FROM bookmarks
            WHERE Std_ID = '$std_id'
            AND ListingID = '$safe_id'";

$fav_result = mysqli_query($conn, $fav_sql);


$is_favourite = 0;

if (mysqli_num_rows($fav_result) > 0) {
    $is_favourite = 1;
}


// ============================================================
// PART 5 : GET THE PHOTOS
// ------------------------------------------------------------
// We put them in a simple array, because we need to show
// the first one big and the next two small.
// ============================================================

$photo_sql = "SELECT * FROM Listing_Photo
              WHERE ListingID = '$safe_id'
              ORDER BY PhotoID ASC";

$photo_result = mysqli_query($conn, $photo_sql);


$photos = array();

while ($row = mysqli_fetch_assoc($photo_result)) {
    $photos[] = $row["PhotoURL"];
}

$photo_count = count($photos);


// ============================================================
// PART 6 : GET THE UTILITIES
// ============================================================

$utility_sql = "SELECT * FROM Listing_Utility
                WHERE ListingID = '$safe_id'
                ORDER BY UtilityName ASC";

$utility_result = mysqli_query($conn, $utility_sql);

$utility_count = mysqli_num_rows($utility_result);

// ============================================================
// PART 7 : ROOM BOOKING STATUS
// ============================================================
// A student can book this room once. Cancellation is allowed
// for 3 days after the booking was made.
// ============================================================

$booking = null;
$booking_exists = false;
$cancel_allowed = false;

$booking_sql = "SELECT * FROM room_bookings
                WHERE Std_ID = '$std_id'
                AND ListingID = '$safe_id'
                AND status IN ('Booked', 'Confirmed')
                ORDER BY id DESC
                LIMIT 1";

$booking_result = mysqli_query($conn, $booking_sql);

if ($booking_result && mysqli_num_rows($booking_result) > 0) {
    $booking = mysqli_fetch_assoc($booking_result);
    $booking_exists = true;

    if (!empty($booking["booked_at"])) {
        $booking_time = strtotime($booking["booked_at"]);
        $cancel_until = strtotime("+3 days", $booking_time);
        $cancel_allowed = time() <= $cancel_until;
    }
}

// ============================================================
// PART 8 : STUDENT NOTIFICATIONS
// ============================================================
$notification_count = 0;
$notification_sql = "SELECT COUNT(*) AS unread_count
                     FROM notifications
                     WHERE user_id = '$std_id'
                     AND is_read = 0";
$notification_result = mysqli_query($conn, $notification_sql);
if ($notification_result) {
    $notification_row = mysqli_fetch_assoc($notification_result);
    $notification_count = (int)$notification_row["unread_count"];
}


?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Global Nest - Room Details</title>

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

    .navigation a {
        color: white;
        text-decoration: none;
        font-size: 16px;
        margin-left: 25px;
    }
    

    .navigation {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .navigation a:hover {
        text-decoration: underline;
    }

       .notification-link {
        position: relative;
        display: inline-flex;
        align-items: center;
        color: white !important;
        text-decoration: none !important;
        font-size: 20px !important;
        margin-left: 0 !important;
    }

    .notification-badge {
        position: absolute;
        top: -9px;
        right: -10px;
        min-width: 18px;
        height: 18px;
        padding: 2px 5px;
        border-radius: 20px;
        background: #e00000;
        color: white;
        font-size: 10px;
        font-weight: bold;
        text-align: center;
    }


    /* ================= MAIN ================= */

    .container {
        width: 92%;
        max-width: 1000px;
        margin: 30px auto 60px auto;
    }


    /* ================= BACK LINK ================= */

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 10px;

        color: #000080;
        font-size: 17px;

        text-decoration: none;

        margin-bottom: 20px;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .back-link svg {
        width: 20px;
        height: 20px;
    }


    /* ================= PHOTOS ================= */
    /* one big photo on the left, two small on the right */

    .photo-area {
        display: flex;
        gap: 12px;

        height: 380px;

        margin-bottom: 25px;
    }

    .photo-big {
        flex: 2;

        border-radius: 10px;
        overflow: hidden;
    }

    .photo-side {
        flex: 1;

        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .photo-small {
        flex: 1;

        border-radius: 10px;
        overflow: hidden;
    }

    .photo-area img {
        width: 100%;
        height: 100%;

        object-fit: cover;

        display: block;
    }


    /* grey box shown when there is no photo yet */

    .photo-empty {
        width: 100%;
        height: 100%;

        background-color: #e4e4ea;

        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 10px;

        color: #8a8a96;
        font-size: 15px;
    }

    .photo-empty svg {
        width: 46px;
        height: 46px;
    }

    .photo-empty.small svg {
        width: 28px;
        height: 28px;
    }


    /* ================= DISTANCES ================= */
    /* sits horizontally right under the photos */

    .distance-row {
        display: flex;
        gap: 12px;

        margin-bottom: 25px;
    }

    .distance-item {
        flex: 1;

        background-color: white;

        padding: 16px 20px;

        border-radius: 10px;

        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.10);

        display: flex;
        align-items: center;
        gap: 12px;
    }

    .distance-item svg {
        width: 22px;
        height: 22px;

        color: #000080;

        flex-shrink: 0;
    }

    .distance-label {
        color: #777;
        font-size: 13px;
    }

    .distance-value {
        font-size: 16px;
        font-weight: bold;
    }


    /* ================= ROOM INFO ================= */

    .info-card {
        background-color: white;

        padding: 25px 28px;
        margin-bottom: 25px;

        border-radius: 10px;

        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);

        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
    }

    .info-left h1 {
        color: #000080;
        font-size: 27px;

        margin: 0 0 8px 0;
    }

    .info-location {
        color: #666;
        font-size: 15px;

        margin-bottom: 6px;
    }

    .info-provider {
        color: #888;
        font-size: 13px;
    }

    .info-right {
        text-align: right;
        flex-shrink: 0;
    }

    .info-price {
        color: #000080;
        font-size: 25px;
        font-weight: bold;
    }

    .info-price span {
        font-size: 14px;
        font-weight: normal;
        color: #777;
    }


    /* ================= FAVOURITE BUTTON ================= */

    .fav-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        margin-top: 14px;
        padding: 10px 20px;

        border: 1px solid #000080;
        border-radius: 20px;

        background-color: white;
        color: #000080;

        font-size: 14px;

        text-decoration: none;
    }

    .fav-button:hover {
        background-color: #f0f0f7;
    }

    .fav-button svg {
        width: 17px;
        height: 17px;
    }


    /* when the room is already saved, fill the heart in */

    .fav-button.saved {
        background-color: #000080;
        color: white;
    }

    .fav-button.saved:hover {
        background-color: #000066;
    }


    /* ================= UTILITIES ================= */

    .utility-card {
        background-color: white;

        padding: 25px 28px;

        border-radius: 10px;

        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .utility-card h2 {
        color: #000080;
        font-size: 20px;

        margin: 0 0 5px 0;
    }

    .utility-note {
        color: #666;
        font-size: 14px;

        margin: 0 0 22px 0;
    }

    .utility-grid {
        display: grid;

        /* auto-fit means "fit as many as you can per row".
           With 5 utilities this gives 3 on top, 2 below,
           and it keeps working if more are added later. */

        grid-template-columns:
            repeat(auto-fit, minmax(180px, 1fr));

        gap: 18px;
    }

    .utility-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .utility-item svg {
        width: 24px;
        height: 24px;

        color: #000080;

        flex-shrink: 0;
    }

    .utility-name {
        font-size: 15px;
        font-weight: bold;
    }

    .utility-amount {
        color: #666;
        font-size: 14px;
    }

    .utility-total {
        margin-top: 22px;
        padding-top: 18px;

        border-top: 1px solid #ddd;

        font-size: 15px;
    }

    .utility-total strong {
        color: #000080;
        font-size: 17px;
    }

    .utility-empty {
        color: #888;
        font-size: 15px;
    }

     /* ================= BOOKING ================= */

    .action-card {
        background-color: white;
        padding: 25px 28px;
        margin-bottom: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .action-card h2 {
        color: #000080;
        font-size: 20px;
        margin: 0 0 6px 0;
    }

    .action-note {
        color: #666;
        font-size: 14px;
        margin: 0 0 18px 0;
    }

    .booking-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .primary-action,
    .secondary-action,
    .danger-action,
    .tour-action {
        display: inline-block;
        padding: 11px 20px;
        border-radius: 7px;
        text-decoration: none;
        font-size: 14px;
        border: none;
        cursor: pointer;
    }

    .primary-action {
        background-color: #000080;
        color: white;
    }

    .primary-action:hover {
        background-color: #000066;
    }

    .secondary-action {
        background-color: #e8f5ec;
        color: #147333;
        border: 1px solid #b8dfc3;
    }

    .danger-action {
        background-color: white;
        color: #c00000;
        border: 1px solid #c00000;
    }

    .danger-action:hover {
        background-color: #fff1f1;
    }

    .booked-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 14px;
        border-radius: 18px;
        background: #d4edda;
        color: #155724;
        font-weight: bold;
        font-size: 14px;
    }

    .cancel-note {
        margin-top: 13px;
        color: #777;
        font-size: 13px;
    }

    /* ================= VIRTUAL TOUR ================= */

    .tour-card {
        background-color: white;
        padding: 25px 28px;
        margin-bottom: 25px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .tour-card h2 {
        color: #000080;
        font-size: 20px;
        margin: 0 0 5px 0;
    }

    .tour-card p {
        color: #666;
        font-size: 14px;
        margin: 0 0 18px 0;
    }

    .tour-form {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 14px;
        align-items: end;
    }

    .tour-field label {
        display: block;
        color: #555;
        font-size: 13px;
        margin-bottom: 6px;
        font-weight: bold;
    }

    .tour-field input {
        width: 100%;
        padding: 11px 12px;
        border: 1px solid #ccc;
        border-radius: 7px;
        font-size: 14px;
    }

    .tour-action {
        background-color: #000080;
        color: white;
    }

    .tour-action:hover {
        background-color: #000066;
    }

    .tour-info {
        margin-top: 15px;
        padding: 12px 14px;
        border-radius: 7px;
        background: #f5f7ff;
        color: #555;
        font-size: 13px;
    }

    .booking-alert {
        margin-top: 15px;
        padding: 12px 14px;
        border-radius: 7px;
        background: #fff3cd;
        color: #856404;
        font-size: 13px;
    }


    /* ================= MOBILE ================= */

    @media (max-width: 800px) {

        .photo-area {
            flex-direction: column;
            height: auto;
        }

        .photo-big {
            height: 240px;
        }

        .photo-side {
            flex-direction: row;
            height: 110px;
        }

        .distance-row {
            flex-direction: column;
        }

        .utility-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .info-card {
            flex-direction: column;
        }

        .info-right {
            text-align: left;
        }

    }

</style>

</head>


<body>


<!-- ================= HEADER ================= -->

<div class="header">

    <div class="logo">

        <strong>Global Nest</strong>
        - Student Room Matcher

    </div>


    <div class="navigation">

        <a href="dashboard.php">Dashboard</a>

        <a href="faq.php">FAQ</a>

         <a href="notifications.php" class="notification-link" title="Notifications">
            🔔
            <?php if ($notification_count > 0) { ?>
                <span class="notification-badge"><?php echo $notification_count; ?></span>
            <?php } ?>
        </a>

        <a href="logout.php">Logout</a>

    </div>

</div>



<div class="container">
    <?php if (isset($_GET["booked"])) { ?>
        <div style="background:#d4edda;color:#155724;padding:14px 18px;border-radius:8px;margin-bottom:18px;">
            ✓ Booking confirmed successfully. The provider has been notified.
        </div>
    <?php } ?>

    <?php if (isset($_GET["cancelled"])) { ?>
        <div style="background:#fff3cd;color:#856404;padding:14px 18px;border-radius:8px;margin-bottom:18px;">
            Booking cancelled successfully.
        </div>
    <?php } ?>

    <?php if (isset($_GET["tour_booked"])) { ?>
        <div style="background:#d4edda;color:#155724;padding:14px 18px;border-radius:8px;margin-bottom:18px;">
            ✓ Virtual tour request sent. Waiting for provider confirmation.
        </div>
    <?php } ?>

    <?php if (isset($_GET["tour_error"])) { ?>
        <div style="background:#f8d7da;color:#721c24;padding:14px 18px;border-radius:8px;margin-bottom:18px;">
            <?php
            $tour_error = $_GET["tour_error"];
            if ($tour_error == "taken") echo "That virtual tour time is already booked. Please choose another time.";
            elseif ($tour_error == "bookfirst") echo "Please book the room before requesting a virtual tour.";
            else echo "The virtual tour request could not be submitted.";
            ?>
        </div>
    <?php } ?>

    <!-- ================= BACK LINK ================= -->

    <a href="dashboard.php" class="back-link">

        <!-- left arrow icon -->
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12 H5"/>
            <path d="M12 19 l-7 -7 l7 -7"/>
        </svg>

        See all listings

    </a>



    <!-- ================= PHOTOS ================= -->
    <!-- If the provider has not uploaded photos yet,
         we show a plain grey box instead. -->

    <div class="photo-area">


        <!-- BIG PHOTO ON THE LEFT -->

        <div class="photo-big">

            <?php if ($photo_count > 0) { ?>

                <img src="<?php echo htmlspecialchars($photos[0]); ?>"
                     alt="Room photo">

            <?php } else { ?>

                <div class="photo-empty">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="1.6" stroke-linecap="round"
                         stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="14" rx="2"/>
                        <circle cx="8.5" cy="10" r="1.5"/>
                        <path d="M21 16 l-5 -5 L5 19"/>
                    </svg>

                    No photos yet

                </div>

            <?php } ?>

        </div>


        <!-- TWO SMALL PHOTOS ON THE RIGHT -->

        <div class="photo-side">


            <div class="photo-small">

                <?php if ($photo_count > 1) { ?>

                    <img src="<?php echo htmlspecialchars($photos[1]); ?>"
                         alt="Room photo">

                <?php } else { ?>

                    <div class="photo-empty small">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.6" stroke-linecap="round"
                             stroke-linejoin="round">
                            <rect x="3" y="5" width="18" height="14" rx="2"/>
                            <circle cx="8.5" cy="10" r="1.5"/>
                            <path d="M21 16 l-5 -5 L5 19"/>
                        </svg>

                    </div>

                <?php } ?>

            </div>


            <div class="photo-small">

                <?php if ($photo_count > 2) { ?>

                    <img src="<?php echo htmlspecialchars($photos[2]); ?>"
                         alt="Room photo">

                <?php } else { ?>

                    <div class="photo-empty small">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="1.6" stroke-linecap="round"
                             stroke-linejoin="round">
                            <rect x="3" y="5" width="18" height="14" rx="2"/>
                            <circle cx="8.5" cy="10" r="1.5"/>
                            <path d="M21 16 l-5 -5 L5 19"/>
                        </svg>

                    </div>

                <?php } ?>

            </div>


        </div>


    </div>



    <!-- ================= DISTANCES ================= -->

    <div class="distance-row">


        <!-- CLINIC -->

        <div class="distance-item">

            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="6" width="18" height="14" rx="2"/>
                <path d="M12 10 v6"/>
                <path d="M9 13 h6"/>
            </svg>

            <div>
                <div class="distance-label">Clinic</div>
                <div class="distance-value">
                    <?php echo $room["Clinic"]; ?> km
                </div>
            </div>

        </div>


        <!-- GROCERY -->

        <div class="distance-item">

            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 7 h16 l-1.5 11 h-13 z"/>
                <path d="M9 7 V5 a3 3 0 0 1 6 0 v2"/>
            </svg>

            <div>
                <div class="distance-label">Grocery</div>
                <div class="distance-value">
                    <?php echo $room["Grocery"]; ?> km
                </div>
            </div>

        </div>


        <!-- CAMPUS -->

        <div class="distance-item">

            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 5 L22 10 L12 15 L2 10 Z"/>
                <path d="M6 12 v5 c0 1 3 2 6 2 s6 -1 6 -2 v-5"/>
            </svg>

            <div>
                <div class="distance-label">Campus</div>
                <div class="distance-value">
                    <?php echo $room["Campus"]; ?> km
                </div>
            </div>

        </div>


    </div>



    <!-- ================= ROOM INFO ================= -->

    <div class="info-card">


        <div class="info-left">


            <h1>
                <?php echo htmlspecialchars($room["RoomType"]); ?>
            </h1>


            <div class="info-location">

                <?php echo htmlspecialchars($room["Neighbourhood"]); ?>,

                <?php echo htmlspecialchars($room["State"]); ?>,

                <?php echo htmlspecialchars($room["Country"]); ?>

            </div>


            <div class="info-provider">

                Listed by

                <?php

                echo htmlspecialchars(
                    $room["First_name"] . " " .
                    $room["Last_name"]
                );

                ?>

            </div>


            <!-- ============================================
                 FAVOURITE BUTTON
                 --------------------------------------------
                 It is a plain link. The address tells the
                 page whether to add or remove the bookmark.
            ============================================ -->

            <?php if ($is_favourite == 1) { ?>

                <a class="fav-button saved"
                   href="room.php?id=<?php echo $safe_id; ?>&fav=remove">

                    <!-- filled heart -->
                    <svg viewBox="0 0 24 24" fill="currentColor"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20 C12 20 3 14 3 8.5 A4.5 4.5 0 0 1 12 6
                                 A4.5 4.5 0 0 1 21 8.5 C21 14 12 20 12 20 Z"/>
                    </svg>

                    Saved

                </a>

            <?php } else { ?>

                <a class="fav-button"
                   href="room.php?id=<?php echo $safe_id; ?>&fav=add">

                    <!-- empty heart -->
                    <svg viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20 C12 20 3 14 3 8.5 A4.5 4.5 0 0 1 12 6
                                 A4.5 4.5 0 0 1 21 8.5 C21 14 12 20 12 20 Z"/>
                    </svg>

                    Add to Favourites

                </a>

            <?php } ?>


        </div>


        <div class="info-right">

            <div class="info-price">

                <?php echo number_format($room["Price"], 2); ?>

                <?php echo htmlspecialchars($room["Currency"]); ?>

                <span>/ month</span>

            </div>

        </div>


    </div>

     <!-- ================= ROOM BOOKING ================= -->

    <div class="action-card">

        <h2>Room Booking</h2>

        <p class="action-note">
            Reserve this room by confirming your booking details and agreeing to the booking terms.
        </p>

        <?php if (!$booking_exists) { ?>

            <a
                href="booking.php?id=<?php echo $safe_id; ?>"
                class="primary-action"
            >
                Want to Book
            </a>

        <?php } else { ?>

            <div class="booking-buttons">

                <span class="booked-status">
                    ✓ Booked
                </span>

                <?php if ($cancel_allowed) { ?>

                    <a
                        href="cancel_booking.php?id=<?php echo $safe_id; ?>"
                        class="danger-action"
                        onclick="return confirm('Are you sure you want to cancel this booking?');"
                    >
                        Cancel Booking
                    </a>

                <?php } ?>

            </div>

            <?php if ($cancel_allowed && !empty($booking["booked_at"])) { ?>
                <div class="cancel-note">
                    Cancellation is available until
                    <?php echo date("d M Y, h:i A", strtotime($booking["booked_at"] . " +3 days")); ?>.
                </div>
            <?php } else { ?>
                <div class="cancel-note">
                    The 3-day cancellation period has ended. This booking can no longer be cancelled.
                </div>
            <?php } ?>

        <?php } ?>

    </div>


    <!-- ================= VIRTUAL TOUR ================= -->

    <div class="tour-card">

        <h2>Virtual Tour</h2>

        <p>
            Choose a date and time to request a virtual tour of this room.
            The provider will confirm your selected slot.
        </p>

        <?php if ($booking_exists) { ?>

            <form class="tour-form" action="book_virtual_tour.php" method="POST">

                <input type="hidden" name="listing_id" value="<?php echo $safe_id; ?>">

                <div class="tour-field">
                    <label for="tour_date">Date</label>
                    <input
                        type="date"
                        id="tour_date"
                        name="tour_date"
                        min="<?php echo date('Y-m-d'); ?>"
                        required
                    >
                </div>

                <div class="tour-field">
                    <label for="tour_time">Time</label>
                    <input
                        type="time"
                        id="tour_time"
                        name="tour_time"
                        required
                    >
                </div>

                <button type="submit" class="tour-action">
                    Book Virtual Tour
                </button>

            </form>

            <div class="tour-info">
                After submitting, the request will appear in the provider's notifications as <strong>Pending</strong>.
            </div>

        <?php } else { ?>

            <div class="booking-alert">
                Please book the room first to request a virtual tour.
            </div>

        <?php } ?>

    </div>

    <!-- ================= UTILITIES ================= -->

    <div class="utility-card">


        <h2>Utilities</h2>

        <p class="utility-note">
            Monthly utility costs for this room.
        </p>


        <?php if ($utility_count > 0) { ?>


            <div class="utility-grid">


                <?php

                // we add the amounts up while we print them

                $total = 0;

                while ($utility = mysqli_fetch_assoc($utility_result)) {

                    $name = $utility["UtilityName"];

                    $total = $total + $utility["Amount"];

                ?>


                <div class="utility-item">


                    <!-- a different icon for each utility name -->

                    <?php if ($name == "Electricity") { ?>

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M13 2 L4 14 h6 l-1 8 l9 -12 h-6 z"/>
                        </svg>

                    <?php } else if ($name == "Wifi") { ?>

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M2 8.5 a15 15 0 0 1 20 0"/>
                            <path d="M5.5 12 a10 10 0 0 1 13 0"/>
                            <path d="M9 15.5 a5 5 0 0 1 6 0"/>
                            <circle cx="12" cy="19" r="1"/>
                        </svg>

                    <?php } else if ($name == "Gas") { ?>

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M12 3 c4 5 5 7 5 10 a5 5 0 0 1 -10 0
                                     c0 -3 1 -5 5 -10 z"/>
                        </svg>

                    <?php } else if ($name == "Heating") { ?>

                        <!-- radiator icon -->
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M6 6 v9"/>
                            <path d="M10 6 v9"/>
                            <path d="M14 6 v9"/>
                            <path d="M18 6 v9"/>
                            <path d="M4 18 h16"/>
                        </svg>

                    <?php } else { ?>

                        <!-- Water, and anything added later -->
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M12 3 s5 5.5 5 9 a5 5 0 0 1 -10 0
                                     c0 -3.5 5 -9 5 -9 z"/>
                        </svg>

                    <?php } ?>


                    <div>

                        <div class="utility-name">
                            <?php echo htmlspecialchars($name); ?>
                        </div>

                        <div class="utility-amount">

                            <?php echo number_format($utility["Amount"], 2); ?>

                            <?php echo htmlspecialchars($room["Currency"]); ?>

                            / month

                        </div>

                    </div>


                </div>


                <?php } ?>


            </div>


            <div class="utility-total">

                Total utilities:

                <strong>

                    <?php echo number_format($total, 2); ?>

                    <?php echo htmlspecialchars($room["Currency"]); ?>

                </strong>

                / month

            </div>


        <?php } else { ?>


            <p class="utility-empty">
                The provider has not added utility costs for this room yet.
            </p>


        <?php } ?>


    </div>


</div>


</body>

</html>