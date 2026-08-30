<?php

session_start();

if (!isset($_SESSION["loggedIn"]) || $_SESSION["userType"] != "student") {
    header("Location: login.php");
    exit();
}

include "DBconnect.php";

$std_id = $_SESSION["Std_ID"];
$listing_id = $_GET["id"];


// Get room information
$sql = "SELECT Listings.*, Room_Provider.First_name, Room_Provider.Last_name
        FROM Listings
        JOIN Room_Provider
        ON Listings.Provider_ID = Room_Provider.Provider_ID
        WHERE Listings.ListingID = '$listing_id'
        AND Listings.Verification_Status = 'Approved'";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: dashboard.php");
    exit();
}

$room = mysqli_fetch_assoc($result);

$error = "";


// When Confirm Your Booking is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $password = $_POST["password"];
    $arrival_date = $_POST["arrival_date"];

    if (!isset($_POST["terms"])) {

        $error = "Please agree to the terms and conditions.";

    } elseif ($arrival_date < date("Y-m-d")) {

        $error = "Please select a valid arrival date.";

    } else {

        // Check student's password
        $student_sql = "SELECT * FROM student
                        WHERE Std_ID = '$std_id'";

        $student_result = mysqli_query($conn, $student_sql);

        $student = mysqli_fetch_assoc($student_result);


        if (!$student) {

            $error = "Student account not found.";

        } elseif ($password != $student["Password"]) {

            $error = "Incorrect password.";

        } else {

            // Check if student already booked this room
            $check_sql = "SELECT * FROM room_bookings
                          WHERE Std_ID = '$std_id'
                          AND ListingID = '$listing_id'
                          AND status = 'Booked'";

            $check_result = mysqli_query($conn, $check_sql);


            if (mysqli_num_rows($check_result) > 0) {

                $error = "You have already booked this room.";

            } else {

                $provider_id = $room["Provider_ID"];


                // Create booking
                $booking_sql = "INSERT INTO room_bookings
                                (ListingID, Std_ID, Provider_ID,
                                 arrival_date, status,
                                 terms_agreed, booked_at)

                                VALUES
                                ('$listing_id',
                                 '$std_id',
                                 '$provider_id',
                                 '$arrival_date',
                                 'Booked',
                                 1,
                                 NOW())";

                if (mysqli_query($conn, $booking_sql)) {

                    $booking_id = mysqli_insert_id($conn);


                    // Notify provider
                    $message = "A student booked your room. Arrival date: "
                             . $arrival_date;

                    $notification_sql =
                        "INSERT INTO notifications
                        (user_id, booking_id, message, type, is_read)

                        VALUES
                        ('$provider_id',
                         '$booking_id',
                         '$message',
                         'Room Booking',
                         0)";

                    mysqli_query($conn, $notification_sql);


                    // Go back to room page
                    header("Location: room.php?id=$listing_id&booked=1");
                    exit();

                } else {

                    $error = "Booking failed. Please try again.";

                }
            }
        }
    }
}

?>

<!DOCTYPE html>

<html>

<head>

<title>Global Nest - Confirm Booking</title>

<style>

body {
    margin: 0;
    font-family: Arial;
    background: #f5f5f5;
}

.header {
    background: navy;
    color: white;
    padding: 20px 40px;
}

.container {
    width: 80%;
    max-width: 800px;
    margin: 40px auto;
}

.card {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 8px #ccc;
}

h1 {
    color: navy;
}

label {
    display: block;
    margin-top: 20px;
    font-weight: bold;
}

input[type="password"],
input[type="date"] {
    width: 100%;
    padding: 12px;
    margin-top: 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
}

.terms {
    background: #f8f8f8;
    border: 1px solid #ddd;
    padding: 20px;
    margin-top: 8px;
    line-height: 1.6;
}

.error {
    background: #ffdede;
    color: red;
    padding: 12px;
    margin-bottom: 15px;
}

.button {
    width: 100%;
    margin-top: 25px;
    padding: 14px;
    background: navy;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
}

.button:hover {
    background: #000066;
}

.back {
    color: navy;
    text-decoration: none;
}

</style>

</head>


<body>


<div class="header">

    <strong>Global Nest</strong>

</div>


<div class="container">

<a class="back"
   href="room.php?id=<?php echo $listing_id; ?>">
   ← Back to Room
</a>


<div class="card">

<h1>Confirm Your Booking</h1>


<p>

<strong>
<?php echo htmlspecialchars($room["RoomType"]); ?>
</strong>

<br>

<?php echo htmlspecialchars($room["Neighbourhood"]); ?>,
<?php echo htmlspecialchars($room["State"]); ?>,
<?php echo htmlspecialchars($room["Country"]); ?>

</p>


<p style="color:navy;font-size:22px;font-weight:bold;">

<?php echo number_format($room["Price"], 2); ?>

<?php echo htmlspecialchars($room["Currency"]); ?>

<span style="font-size:14px;color:#777;">
    / month
</span>

</p>


<?php if ($error != "") { ?>

<div class="error">

<?php echo $error; ?>

</div>

<?php } ?>


<form method="POST">


<label>
Re-enter Your Password
</label>

<input
    type="password"
    name="password"
    required
>


<label>
Date You Will Arrive
</label>

<input
    type="date"
    name="arrival_date"
    min="<?php echo date('Y-m-d'); ?>"
    required
>


<label>
Booking Terms & Conditions
</label>


<div class="terms">

<ul>

<li>
You can cancel the booking within 3 days.
</li>

<li>
After 3 days, the booking cannot be cancelled.
</li>

<li>
A 25% advance/booking commitment applies.
</li>

<li>
Students must maintain good manners and respectful behaviour.
</li>

<li>
Students must follow the accommodation rules.
</li>

<li>
Students must provide correct information.
</li>

</ul>

</div>


<p>

<input
    type="checkbox"
    name="terms"
    required
>

I have read and agree to all the terms and conditions.

</p>


<button
    type="submit"
    class="button"
>

Confirm Your Booking

</button>


</form>


</div>

</div>

</body>

</html>