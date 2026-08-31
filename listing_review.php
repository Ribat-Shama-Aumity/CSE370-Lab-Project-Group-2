<?php

// ============================================================
// ROOM REVIEW FEATURE - PART 1 : THE PHP WORK
// ------------------------------------------------------------
// Included inside room.php, at the END of its PHP block.
//
// It uses three things room.php already worked out:
//     $conn      the database connection
//     $std_id    the logged in student
//     $safe_id   the listing being shown
//
// It does 5 jobs:
//   1. check if this student is allowed to review
//   2. save a new review, or update their old one
//   3. work out the star average
//   4. get the list of reviews
//   5. get this student's own review, to fill the form
// ============================================================


// ------------------------------------------------------------
// JOB 1 : IS THIS STUDENT ALLOWED TO REVIEW?
// ------------------------------------------------------------
// Only a student who has booked this room may review it.
// We only need YES or NO, so we count the rows.

$review_allowed = 0;

$booked_sql = "SELECT * FROM room_bookings
               WHERE Std_ID = '$std_id'
               AND ListingID = '$safe_id'";

$booked_result = mysqli_query($conn, $booked_sql);

if ($booked_result && mysqli_num_rows($booked_result) > 0) {

    $review_allowed = 1;
}


// ------------------------------------------------------------
// JOB 2 : SAVE THE REVIEW
// ------------------------------------------------------------
// We check $review_allowed again here. The form is hidden for
// students who did not book, but somebody could still send the
// form by hand, so the server must check too.
//
// Never trust the browser. Always check on the server.

if (isset($_POST["save_review"]) && $review_allowed == 1) {


    $rating = $_POST["rating"];

    $comment = mysqli_real_escape_string($conn, $_POST["comment"]);


    // make sure the rating really is a number from 1 to 5

    if (!is_numeric($rating) || $rating < 1 || $rating > 5) {

        $rating = 5;
    }


    // Has this student already reviewed this room?
    // The primary key is (Std_ID, ListingID), so a second
    // INSERT would fail. We UPDATE instead.

    $old_sql = "SELECT * FROM listing_review
                WHERE Std_ID = '$std_id'
                AND ListingID = '$safe_id'";

    $old_result = mysqli_query($conn, $old_sql);


    if (mysqli_num_rows($old_result) > 0) {

        // they are editing a review they already wrote

        $save_sql = "UPDATE listing_review

                     SET Rating = '$rating',
                         Comment = '$comment'

                     WHERE Std_ID = '$std_id'
                     AND ListingID = '$safe_id'";

    } else {

        // this is their first review of this room

        $save_sql = "INSERT INTO listing_review
                     (Std_ID, ListingID, Rating, Comment)
                     VALUES
                     ('$std_id', '$safe_id', '$rating', '$comment')";
    }


    mysqli_query($conn, $save_sql);


    // Go back to a clean address, so refreshing the page does
    // not send the same review a second time.

    header("Location: room.php?id=" . $safe_id . "&reviewed=1");
    exit();

}


// ------------------------------------------------------------
// JOB 3 : THE STAR AVERAGE
// ------------------------------------------------------------
// AVG() adds every rating and divides by how many there are.
// COUNT(*) counts the rows.
// Both are called AGGREGATE functions, because they squeeze
// many rows down into one answer.
//
// When a room has no reviews at all, AVG() gives back NULL,
// so we turn that into 0 ourselves.

$avg_sql = "SELECT

                AVG(Rating) AS average_rating,
                COUNT(*) AS total_reviews

            FROM listing_review

            WHERE ListingID = '$safe_id'";

$avg_result = mysqli_query($conn, $avg_sql);

$avg_row = mysqli_fetch_assoc($avg_result);


$review_count = $avg_row["total_reviews"];

$average_rating = 0;

if ($review_count > 0) {

    $average_rating = $avg_row["average_rating"];
}


// ------------------------------------------------------------
// JOB 4 : THE LIST OF REVIEWS
// ------------------------------------------------------------
// A plain JOIN is correct here, NOT a LEFT JOIN.
//
// In faq.php we needed LEFT JOIN because Std_ID was allowed
// to be NULL. Here Std_ID is NOT NULL and has a foreign key,
// so every review must belong to a real student. There is
// never a missing match to protect.

$list_sql = "SELECT

                 listing_review.Rating,
                 listing_review.Comment,
                 listing_review.Created_at,

                 student.First_name,
                 student.Last_name

             FROM listing_review

             JOIN student

             ON listing_review.Std_ID = student.Std_ID

             WHERE listing_review.ListingID = '$safe_id'

             ORDER BY listing_review.Created_at DESC";

$list_result = mysqli_query($conn, $list_sql);


// ------------------------------------------------------------
// JOB 5 : THIS STUDENT'S OWN REVIEW
// ------------------------------------------------------------
// If they already wrote one, we put it back into the form so
// they can see and change it instead of starting again.

$my_rating = 5;

$my_comment = "";

$has_my_review = 0;


$mine_sql = "SELECT * FROM listing_review
             WHERE Std_ID = '$std_id'
             AND ListingID = '$safe_id'";

$mine_result = mysqli_query($conn, $mine_sql);


if ($mine_result && mysqli_num_rows($mine_result) > 0) {

    $mine = mysqli_fetch_assoc($mine_result);

    $my_rating = $mine["Rating"];

    $my_comment = $mine["Comment"];

    $has_my_review = 1;
}

?>