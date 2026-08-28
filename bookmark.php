```php
<?php

session_start();

include "DBconnect.php";


/* =====================================================
   CHECK STUDENT LOGIN
   ===================================================== */

if (
    !isset($_SESSION["loggedIn"]) ||
    $_SESSION["userType"] != "student"
) {

    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);

    exit();
}


/* Get logged-in student's ID */

$student_id = $_SESSION["Std_ID"];


/* =====================================================
   ADD / REMOVE BOOKMARK
   ===================================================== */

if (isset($_POST["toggle"])) {

    $listing_id = intval($_POST["listing_id"]);


    /* Check if bookmark already exists */

    $check_sql = "
        SELECT BookmarkID
        FROM bookmarks
        WHERE Std_ID = '$student_id'
        AND ListingID = '$listing_id'
    ";

    $check_result = mysqli_query($conn, $check_sql);


    /* =================================================
       IF ALREADY BOOKMARKED → REMOVE IT
       ================================================= */

    if (mysqli_num_rows($check_result) > 0) {

        $delete_sql = "
            DELETE FROM bookmarks
            WHERE Std_ID = '$student_id'
            AND ListingID = '$listing_id'
        ";

        mysqli_query($conn, $delete_sql);


        echo json_encode([
            "success" => true,
            "bookmarked" => false
        ]);

    }


    /* =================================================
       IF NOT BOOKMARKED → ADD IT
       ================================================= */

    else {

        $insert_sql = "
            INSERT INTO bookmarks
            (Std_ID, ListingID)
            VALUES
            ('$student_id', '$listing_id')
        ";

        mysqli_query($conn, $insert_sql);


        echo json_encode([
            "success" => true,
            "bookmarked" => true
        ]);

    }

    exit();
}


/* =====================================================
   GET ALL BOOKMARKS
   ===================================================== */

if (isset($_GET["get"])) {

    $sql = "
        SELECT

            Listings.ListingID,
            Listings.RoomType,
            Listings.Price,
            Listings.Currency,
            Listings.Neighbourhood,
            Listings.State,
            Listings.Country

        FROM bookmarks

        INNER JOIN Listings
        ON bookmarks.ListingID = Listings.ListingID

        WHERE bookmarks.Std_ID = '$student_id'

        ORDER BY bookmarks.CreatedAt DESC
    ";


    $result = mysqli_query($conn, $sql);


    $bookmarks = [];


    while ($row = mysqli_fetch_assoc($result)) {

        $bookmarks[] = $row;

    }


    echo json_encode([
        "success" => true,
        "bookmarks" => $bookmarks
    ]);


    exit();

}

?>
```
