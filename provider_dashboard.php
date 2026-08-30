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

/*
|--------------------------------------------------------------------------
| Handle Virtual Tour confirmation / rejection
|--------------------------------------------------------------------------
*/
if (isset($_GET["tour_action"]) && isset($_GET["tour_id"])) {

    $tour_id = (int) $_GET["tour_id"];
    $tour_action = $_GET["tour_action"];

    if ($tour_action == "confirm") {
        $new_status = "Confirmed";
    } elseif ($tour_action == "reject") {
        $new_status = "Rejected";
    } else {
        $new_status = "";
    }

    if ($new_status != "") {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE virtual_tour_bookings
             SET status = ?
             WHERE id = ? AND provider_id = ?"
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "sii",
                $new_status,
                $tour_id,
                $provider_id
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);

                // Notify the student after the provider confirms/rejects the tour.
                $tour_info_sql = "SELECT student_id, tour_date, tour_time
                                  FROM virtual_tour_bookings
                                  WHERE id = '$tour_id'
                                  AND provider_id = '$provider_id'
                                  LIMIT 1";
                $tour_info_result = mysqli_query($conn, $tour_info_sql);

                if ($tour_info_result && mysqli_num_rows($tour_info_result) > 0) {
                    $tour_info = mysqli_fetch_assoc($tour_info_result);
                    $student_id = $tour_info["student_id"];

                    if ($new_status == "Confirmed") {
                        $student_message = "Your virtual tour has been confirmed for "
                                         . date("d M Y", strtotime($tour_info["tour_date"]))
                                         . " at "
                                         . date("h:i A", strtotime($tour_info["tour_time"])) . ".";
                    } else {
                        $student_message = "Your virtual tour request for "
                                         . date("d M Y", strtotime($tour_info["tour_date"]))
                                         . " at "
                                         . date("h:i A", strtotime($tour_info["tour_time"]))
                                         . " was rejected by the provider.";
                    }

                    $student_notice_stmt = mysqli_prepare(
                        $conn,
                        "INSERT INTO notifications
                         (user_id, booking_id, message, type, is_read, created_at)
                         VALUES (?, ?, ?, 'Virtual Tour Update', 0, NOW())"
                    );

                    if ($student_notice_stmt) {
                        mysqli_stmt_bind_param(
                            $student_notice_stmt,
                            "iis",
                            $student_id,
                            $tour_id,
                            $student_message
                        );
                        mysqli_stmt_execute($student_notice_stmt);
                        mysqli_stmt_close($student_notice_stmt);
                    }
                }
            } else {
                mysqli_stmt_close($stmt);
            }
        }

        header("Location: provider_dashboard.php");
        exit();
    }
}


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

/*
|--------------------------------------------------------------------------
| Notifications
|--------------------------------------------------------------------------
| Requires:
| notifications(id, user_id, booking_id, message, type, is_read, created_at)
|
| If your notification table uses different column names, change this query.
|--------------------------------------------------------------------------
*/
$notifications = [];
$notification_count = 0;

$notification_sql = "SELECT *
                     FROM notifications n   
                     WHERE n.user_id = '$provider_id'
                     ORDER BY n.created_at DESC
                     LIMIT 10";

$notification_result = mysqli_query($conn, $notification_sql);

if ($notification_result) {
    while ($notification = mysqli_fetch_assoc($notification_result)) {
        $notifications[] = $notification;

        if (isset($notification["is_read"]) && $notification["is_read"] == 0) {
            $notification_count++;
        }
    }
}

/*
|--------------------------------------------------------------------------
| Pending virtual tour requests
|--------------------------------------------------------------------------
*/
$pending_tours = [];

$tour_sql = "SELECT
                v.id,
                v.listing_id,
                v.student_id,
                v.tour_date,
                v.tour_time,
                v.status,
                l.RoomType,
                l.Neighbourhood,
                l.State,
                l.Country
             FROM virtual_tour_bookings v
             LEFT JOIN Listings l
                ON v.listing_id = l.ListingID
            LEFT JOIN Student s
                ON v.student_id = s.Std_ID
             WHERE v.provider_id = '$provider_id'
             AND v.status = 'Pending'
             ORDER BY v.tour_date ASC, v.tour_time ASC";

$tour_result = mysqli_query($conn, $tour_sql);

if ($tour_result) {
    while ($tour = mysqli_fetch_assoc($tour_result)) {
        $pending_tours[] = $tour;
    }
}

$pending_tour_count = count($pending_tours);

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

         /* NOTIFICATION BUTTON */

        .notification-wrapper {
            position: relative;
        }

        .notification-button {
            border: none;
            background: transparent;
            color: white;
            cursor: pointer;
            font-size: 22px;
            position: relative;
            padding: 4px 8px;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -3px;
            min-width: 19px;
            height: 19px;
            padding: 2px 5px;
            border-radius: 20px;
            background: #e00000;
            color: white;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
        }

        /* NOTIFICATION PANEL */

        .notification-panel {
            display: none;
            position: absolute;
            top: 42px;
            right: 65px;
            width: 390px;
            max-height: 620px;
            overflow-y: auto;
            background: white;
            color: #222;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.20);
            z-index: 1000;
        }

        .notification-panel.show {
            display: block;
        }

        .notification-header {
            padding: 18px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-header h3 {
            margin: 0;
            color: navy;
        }

        .notification-item {
            padding: 17px 20px;
            border-bottom: 1px solid #eee;
        }

        .notification-item.unread {
            background-color: #f5f7ff;
        }

        .notification-title {
            color: navy;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .notification-message {
            color: #555;
            line-height: 1.5;
            font-size: 14px;
        }

        .notification-time {
            color: #888;
            font-size: 12px;
            margin-top: 8px;
        }

        .notification-actions {
            margin-top: 12px;
            display: flex;
            gap: 8px;
        }

        .notification-student {
             margin-top: 8px;
             font-size: 14px;
             color: #555;
        }

         .notification-student a {
             color: navy;
             font-weight: bold;
             text-decoration: none;
        }

         .notification-student a:hover {
             text-decoration: underline;
        }

      

        .confirm-button,
        .reject-button {
            border: none;
            padding: 8px 13px;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
        }

        .confirm-button {
            background: #16833b;
        }

        .reject-button {
            background: #d00000;
        }

        .view-all-button {
            display: block;
            text-align: center;
            padding: 14px;
            color: navy;
            font-weight: bold;
            text-decoration: none;
        }

        .view-all-button:hover {
            background: #f5f5f5;
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
 
         /* PENDING REQUESTS */

        .request-box {
            background-color: white;
            padding: 22px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
            border-left: 5px solid #f0ad00;
        }

        .request-box h2 {
            color: navy;
            margin-top: 0;
        }

        .request-count {
            display: inline-block;
            background: #fff3cd;
            color: #856404;
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: bold;
        }

        .tour-request {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            background: #fff;
        }

        .tour-request h3 {
            color: navy;
            margin-top: 0;
        }

        .tour-request p {
            margin: 7px 0;
            color: #555;
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

         @media (max-width: 700px) {

            .header {
                padding: 16px 20px;
            }

            .header-right {
                gap: 10px;
            }

            .notification-panel {
                right: -20px;
                width: min(390px, 92vw);
            }

            .container {
                width: 94%;
            }
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
        

        <!-- NOTIFICATIONS -->

        <div class="notification-wrapper">

            <button
                type="button"
                class="notification-button"
                onclick="toggleNotifications()"
                title="Notifications"
            >
                🔔

                <?php if ($notification_count > 0) { ?>
                    <span class="notification-badge">
                        <?php echo $notification_count; ?>
                    </span>
                <?php } ?>

            </button>

            <div id="notificationPanel" class="notification-panel">

                <div class="notification-header">
                    <h3>Notifications</h3>

                    <span>
                        <?php echo $notification_count; ?> new
                    </span>
                </div>

                <?php if (count($notifications) > 0) { ?>

                    <?php foreach ($notifications as $notification) { ?>

                        <div
                            class="notification-item <?php
                                echo (
                                    isset($notification["is_read"]) &&
                                    $notification["is_read"] == 0
                                )
                                ? "unread"
                                : "";
                            ?>"
                        >

                            <div class="notification-title">
                                <?php
                                echo htmlspecialchars(
                                    $notification["type"] ?? "Notification"
                                );
                                ?>
                            </div>

                            <?php if (!empty($notification["student_id"])) { ?>

  <?php
    $student_id = (int)$notification["student_id"];

    $student_sql = "SELECT Std_ID, Name
                    FROM Student
                    WHERE Std_ID = '$student_id'
                    LIMIT 1";

    $student_result = mysqli_query($conn, $student_sql);

    if ($student_result && mysqli_num_rows($student_result) > 0) {
        $student = mysqli_fetch_assoc($student_result);
    }
    ?>

    <?php if (isset($student)) { ?>

        <div class="notification-student">
            <strong>Student:</strong>

            <a href="student_profile.php?id=<?php echo $student_id; ?>">
                <?php echo htmlspecialchars($student["Name"]); ?>
            </a>
        </div>

    <?php } ?>

<?php } ?>

                            <div class="notification-message">
                                <?php
                                echo htmlspecialchars(
                                    $notification["message"]
                                );
                                ?>
                            </div>

                            <?php if (!empty($notification["created_at"])) { ?>

                                <div class="notification-time">
                                    <?php
                                    echo htmlspecialchars(
                                        $notification["created_at"]
                                    );
                                    ?>
                                </div>

                            <?php } ?>

                        </div>

                    <?php } ?>

                <?php } else { ?>

                    <div class="notification-item">
                        <div class="notification-message">
                            No new notifications.
                        </div>
                    </div>

                <?php } ?>

                <a href="notifications.php" class="view-all-button">
                    View All Notifications
                </a>

            </div>

        </div>

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

    <!-- PENDING VIRTUAL TOUR REQUESTS -->

    <?php if ($pending_tour_count > 0) { ?>

        <div class="request-box">

            <h2>
                Virtual Tour Requests
            </h2>

            <span class="request-count">
                <?php echo $pending_tour_count; ?> Pending
            </span>

            <?php foreach ($pending_tours as $tour) { ?>

                <div class="tour-request">

                    <h3>
                        New Virtual Tour Request
                    </h3>
                    
                     

                    <p>
                        <strong>Room:</strong>
                        <?php
                        echo htmlspecialchars($tour["RoomType"]);
                        ?>
                    </p>

                    <p>
                        <strong>Location:</strong>
                        <?php
                        echo htmlspecialchars($tour["Neighbourhood"]);
                        ?>,
                        <?php
                        echo htmlspecialchars($tour["State"]);
                        ?>,
                        <?php
                        echo htmlspecialchars($tour["Country"]);
                        ?>
                    </p>

                    <p>
                        <strong>Date:</strong>
                        <?php
                        echo htmlspecialchars($tour["tour_date"]);
                        ?>
                    </p>

                    <p>
                        <strong>Time:</strong>
                        <?php
                        echo htmlspecialchars(
                            date(
                                "h:i A",
                                strtotime($tour["tour_time"])
                            )
                        );
                        ?>
                    </p>

                    <div class="notification-actions">

                        <a
                            href="provider_dashboard.php?tour_action=confirm&tour_id=<?php echo $tour["id"]; ?>"
                            class="confirm-button"
                            onclick="return confirm('Confirm this virtual tour?');"
                        >
                            Confirm
                        </a>

                        <a
                            href="provider_dashboard.php?tour_action=reject&tour_id=<?php echo $tour["id"]; ?>"
                            class="reject-button"
                            onclick="return confirm('Reject this virtual tour request?');"
                        >
                            Reject
                        </a>

                    </div>

                </div>

            <?php } ?>

        </div>

    <?php } ?>

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

<script>

function toggleNotifications() {

    const panel =
        document.getElementById("notificationPanel");

    panel.classList.toggle("show");

}


/*
 * Close notification panel when clicking outside it.
 */
document.addEventListener("click", function(event) {

    const wrapper =
        document.querySelector(".notification-wrapper");

    const panel =
        document.getElementById("notificationPanel");

    if (
        wrapper &&
        !wrapper.contains(event.target)
    ) {
        panel.classList.remove("show");
    }

});

</script>


</body>

</html>