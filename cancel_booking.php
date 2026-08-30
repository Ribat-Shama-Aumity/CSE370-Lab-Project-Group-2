<?php
session_start();

if (!isset($_SESSION["loggedIn"]) || $_SESSION["userType"] != "student") {
    header("Location: login.php");
    exit();
}

include "DBconnect.php";

$std_id = $_SESSION["Std_ID"];
$listing_id = isset($_GET["id"]) ? $_GET["id"] : "";

if ($listing_id == "" || !is_numeric($listing_id)) {
    header("Location: dashboard.php");
    exit();
}

$safe_id = mysqli_real_escape_string($conn, $listing_id);

$sql = "SELECT * FROM room_bookings
        WHERE Std_ID = '$std_id'
        AND ListingID = '$safe_id'
        AND status IN ('Booked', 'Confirmed')
        ORDER BY id DESC LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $booking = mysqli_fetch_assoc($result);

    if (!empty($booking["booked_at"]) && time() <= strtotime($booking["booked_at"] . " +3 days")) {
        $booking_id = (int)$booking["id"];

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE room_bookings SET status='Cancelled', cancelled_at=NOW()
             WHERE id=? AND Std_ID=?"
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "is", $booking_id, $std_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
}

header("Location: room.php?id=" . $safe_id . "&cancelled=1");
exit();
?>
