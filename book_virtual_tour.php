
<?php 
session_start(); 
 
if (!isset($_SESSION["loggedIn"]) || $_SESSION["userType"] != "student") { 
    header("Location: login.php"); 
    exit(); 
} 
 
include "DBconnect.php"; 
 
$std_id = $_SESSION["Std_ID"]; 
$listing_id = isset($_POST["listing_id"]) ? $_POST["listing_id"] : ""; 
$tour_date = isset($_POST["tour_date"]) ? $_POST["tour_date"] : ""; 
$tour_time = isset($_POST["tour_time"]) ? $_POST["tour_time"] : ""; 
 
if ($listing_id == "" || !is_numeric($listing_id) || $tour_date == "" || $tour_time == "") { 
    header("Location: dashboard.php"); 
    exit(); 
} 
 
$safe_id = mysqli_real_escape_string($conn, $listing_id); 
 
// Get the approved room and provider.
// Virtual tour does NOT require a room booking.
$room_sql = "SELECT * FROM Listings 
             WHERE ListingID='$safe_id' 
             AND Verification_Status='Approved' 
             LIMIT 1"; 
 
$room_result = mysqli_query($conn, $room_sql); 
 
if (!$room_result || mysqli_num_rows($room_result) == 0) { 
    header("Location: dashboard.php"); 
    exit(); 
} 
 
$room = mysqli_fetch_assoc($room_result); 
$provider_id = $room["Provider_ID"]; 
 
// Do not allow a tour date in the past.
if ($tour_date < date("Y-m-d")) { 
    header("Location: room.php?id=" . $safe_id . "&tour_error=date"); 
    exit(); 
} 
 
// Do not allow two active requests for the same room/date/time.
$check_stmt = mysqli_prepare( 
    $conn, 
    "SELECT id FROM virtual_tour_bookings 
     WHERE listing_id=? AND tour_date=? AND tour_time=? 
     AND status IN ('Pending','Confirmed') 
     LIMIT 1" 
); 
 
if ($check_stmt) { 
    mysqli_stmt_bind_param($check_stmt, "iss", $listing_id, $tour_date, $tour_time); 
    mysqli_stmt_execute($check_stmt); 
    $check_result = mysqli_stmt_get_result($check_stmt); 
    $already = $check_result && mysqli_num_rows($check_result) > 0; 
    mysqli_stmt_close($check_stmt); 
 
    if ($already) { 
        header("Location: room.php?id=" . $safe_id . "&tour_error=taken"); 
        exit(); 
    } 
} 
 
// Create the virtual tour request.
$stmt = mysqli_prepare( 
    $conn, 
    "INSERT INTO virtual_tour_bookings 
     (listing_id, student_id, provider_id, tour_date, tour_time, status, created_at) 
     VALUES (?, ?, ?, ?, ?, 'Pending', NOW())" 
); 
 
if ($stmt) { 
    mysqli_stmt_bind_param(
        $stmt, 
        "iiiss", 
        $listing_id, 
        $std_id, 
        $provider_id, 
        $tour_date, 
        $tour_time
    ); 
 
    if (mysqli_stmt_execute($stmt)) { 
        $tour_id = mysqli_insert_id($conn); 
        mysqli_stmt_close($stmt); 
 
        // Notify the provider about the virtual tour request.
        $message = "New virtual tour request for " . $room["RoomType"] . " on " 
                 . date("d M Y", strtotime($tour_date)) . " at " 
                 . date("h:i A", strtotime($tour_time)) . "."; 
 
        $notice_stmt = mysqli_prepare( 
            $conn, 
            "INSERT INTO notifications 
             (user_id, booking_id, message, type, is_read, created_at) 
             VALUES (?, ?, ?, 'Virtual Tour Request', 0, NOW())" 
        ); 
 
        if ($notice_stmt) { 
            mysqli_stmt_bind_param(
                $notice_stmt, 
                "iis", 
                $provider_id, 
                $tour_id, 
                $message
            ); 
            mysqli_stmt_execute($notice_stmt); 
            mysqli_stmt_close($notice_stmt); 
        } 
    } else { 
        mysqli_stmt_close($stmt); 
    } 
} 
 
header("Location: room.php?id=" . $safe_id . "&tour_booked=1"); 
exit(); 
?>

