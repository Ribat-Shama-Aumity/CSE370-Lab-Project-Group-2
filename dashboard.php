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

/* Get student's bookmarked listings */

$student_id = $_SESSION["Std_ID"];

$bookmark_sql = "
    SELECT ListingID
    FROM bookmarks
    WHERE Std_ID = '$student_id'
";

$bookmark_result = mysqli_query($conn, $bookmark_sql);

$bookmarked_ids = [];

while ($bookmark = mysqli_fetch_assoc($bookmark_result)) {

    $bookmarked_ids[] = $bookmark["ListingID"];

}

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
        position: relative;

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

    /* =====================================================
   BOOKMARK POPUP
   ===================================================== */

.bookmark-popup {

    display: none;

    position: absolute;

    top: 65px;

    right: 100px;

    width: 330px;

    background-color: white;

    border-radius: 8px;

    box-shadow:
        0 4px 15px rgba(0,0,0,0.2);

    z-index: 1000;

    color: #222;

}


/* HEADER */

.bookmark-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    padding: 15px;

    border-bottom: 1px solid #ddd;

    color: #000080;

    font-size: 17px;

}


#closeBookmarks {

    font-size: 22px;

    color: #666;

    cursor: pointer;

}


/* BOOKMARK ITEM */

.bookmark-item {

    padding: 12px 15px;

    border-bottom: 1px solid #eee;

}


.bookmark-item:hover {

    background-color: #f5f5f5;

}


.bookmark-item h4 {

    margin: 0 0 5px 0;

    color: #000080;

    font-size: 16px;

}


.bookmark-item p {

    margin: 3px 0;

    font-size: 13px;

    color: #666;

}


/* NO BOOKMARKS */

.no-bookmarks {

    padding: 25px;

    text-align: center;

    color: #666;

    line-height: 1.5;

}


.explore-link {

    display: block;

    margin-top: 10px;

    color: #000080;

    font-weight: bold;

    text-decoration: none;

}


.explore-link:hover {

    text-decoration: underline;

}

/* BOOKMARK HEART */

.room-card {
    position: relative;
}


.bookmark-heart {

    position: absolute;

    top: 15px;

    right: 15px;

    background: none;

    border: none;

    color: #777;

    cursor: pointer;

    padding: 0;

    line-height: 1;

}



.bookmark-heart svg {

    width: 26px;

    height: 26px;

    display: block;

}


.bookmark-heart:hover {

    color: #000080;

}


.bookmark-heart.saved {

    color: #e0245e;

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

        <a href="#" id="bookmarkLink">
            Bookmarks
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
           
            <?php

             $is_bookmarked = in_array(
                 $room["ListingID"],
                 $bookmarked_ids
           );

            ?>


             <button

                  class="bookmark-heart
                 <?php echo $is_bookmarked ? 'saved' : ''; ?>"

                 onclick="toggleBookmark(
                     <?php echo $room['ListingID']; ?>,
                     this
                 )"

                 type="button"

             >

                 <?php

                 // The SAME heart shape is used for both states.
                 // Only "fill" changes: none = empty, currentColor
                 // = filled. Before this we used the characters
                 // ♥ and ♡, but they are two different letters in
                 // the font, so the shape jumped when you clicked.

                 if ($is_bookmarked) {

                     echo '<svg viewBox="0 0 24 24" fill="currentColor"
                                stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                             <path d="M12 20 C12 20 3 14 3 8.5
                                      A4.5 4.5 0 0 1 12 6
                                      A4.5 4.5 0 0 1 21 8.5
                                      C21 14 12 20 12 20 Z"/>
                           </svg>';

                 } else {

                     echo '<svg viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                             <path d="M12 20 C12 20 3 14 3 8.5
                                      A4.5 4.5 0 0 1 12 6
                                      A4.5 4.5 0 0 1 21 8.5
                                      C21 14 12 20 12 20 Z"/>
                           </svg>';

                 }

                 ?>

             </button>
           




           <a href="room.php?id=<?php echo $room["ListingID"]; ?>"
              style="text-decoration:none; color:inherit; display:block;">


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


      </a>


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
<!-- =====================================================
     BOOKMARK POPUP
     ===================================================== -->

<div id="bookmarkPopup" class="bookmark-popup">


    <div class="bookmark-header">

        <strong>
            Your Bookmarks
        </strong>


        <span id="closeBookmarks">
            ×
        </span>

    </div>


    <div id="bookmarkContent">

        Loading...

    </div>


</div>

<!-- BOOKMARK JAVASCRIPT -->

<script>

console.log("Bookmark JavaScript loaded");


/* =====================================================
   TOGGLE HEART BOOKMARK
   ===================================================== */

function toggleBookmark(listingID, button) {

    console.log("Heart clicked:", listingID);

    let formData = new FormData();

    formData.append("toggle", "1");
    formData.append("listing_id", listingID);

    fetch("bookmark.php", {
        method: "POST",
        body: formData
    })

    .then(response => response.text())

    .then(text => {

        console.log("Bookmark response:", text);

        let data;

        try {
            data = JSON.parse(text);
        }
        catch (error) {
            console.error("Invalid JSON:", text);
            return;
        }

        if (data.success) {

            if (data.bookmarked) {

                button.innerHTML =
                    '<svg viewBox="0 0 24 24" fill="currentColor" ' +
                    'stroke="currentColor" stroke-width="2" ' +
                    'stroke-linecap="round" stroke-linejoin="round">' +
                    '<path d="M12 20 C12 20 3 14 3 8.5 ' +
                    'A4.5 4.5 0 0 1 12 6 A4.5 4.5 0 0 1 21 8.5 ' +
                    'C21 14 12 20 12 20 Z"/></svg>';

                button.classList.add("saved");

            } else {

                button.innerHTML =
                    '<svg viewBox="0 0 24 24" fill="none" ' +
                    'stroke="currentColor" stroke-width="2" ' +
                    'stroke-linecap="round" stroke-linejoin="round">' +
                    '<path d="M12 20 C12 20 3 14 3 8.5 ' +
                    'A4.5 4.5 0 0 1 12 6 A4.5 4.5 0 0 1 21 8.5 ' +
                    'C21 14 12 20 12 20 Z"/></svg>';

                button.classList.remove("saved");

            }

        } else {

            console.error("Bookmark failed:", data.message);

        }

    })

    .catch(error => {

        console.error("Bookmark request error:", error);

    });

}


/* =====================================================
   OPEN BOOKMARKS
   ===================================================== */

document.addEventListener("DOMContentLoaded", function() {

    console.log("DOM loaded");


    const bookmarkLink =
        document.getElementById("bookmarkLink");

    const bookmarkPopup =
        document.getElementById("bookmarkPopup");

    const closeBookmarks =
        document.getElementById("closeBookmarks");


    /* Check elements */

    console.log("bookmarkLink:", bookmarkLink);
    console.log("bookmarkPopup:", bookmarkPopup);
    console.log("closeBookmarks:", closeBookmarks);


    /* OPEN */

    if (bookmarkLink) {

        bookmarkLink.addEventListener("click", function(event) {

            event.preventDefault();

            console.log("Bookmarks clicked");

            bookmarkPopup.style.display = "block";

            loadBookmarks();

        });

    }


    /* CLOSE */

    if (closeBookmarks) {

        closeBookmarks.addEventListener("click", function() {

            bookmarkPopup.style.display = "none";

        });

    }

});


/* =====================================================
   LOAD BOOKMARKS
   ===================================================== */

function loadBookmarks() {

    console.log("Loading bookmarks...");


    const content =
        document.getElementById("bookmarkContent");


    content.innerHTML = "Loading...";


    fetch("bookmark.php?get=1")

    .then(response => response.text())

    .then(text => {

        console.log("Bookmarks response:", text);


        let data;

        try {

            data = JSON.parse(text);

        }
        catch (error) {

            console.error("Invalid JSON:", text);

            content.innerHTML =
                "Unable to load bookmarks.";

            return;

        }


        if (!data.success) {

            content.innerHTML =
                data.message || "Unable to load bookmarks.";

            return;

        }


        /* NO BOOKMARKS */

        if (data.bookmarks.length === 0) {

            content.innerHTML = `

                <div class="no-bookmarks">

                    <div style="color:#bbb;">
                        <svg viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.6"
                             stroke-linecap="round" stroke-linejoin="round"
                             style="width:44px; height:44px;">
                            <path d="M12 20 C12 20 3 14 3 8.5
                                     A4.5 4.5 0 0 1 12 6
                                     A4.5 4.5 0 0 1 21 8.5
                                     C21 14 12 20 12 20 Z"/>
                        </svg>
                    </div>

                    <div>
                        You haven't saved anything.
                    </div>

                    <div>
                        Want to explore more?
                    </div>

                    <a
                        href="dashboard.php"
                        class="explore-link"
                    >
                        Go to Dashboard
                    </a>

                </div>

            `;

            return;

        }


        /* SHOW BOOKMARKS */

        content.innerHTML = "";


        data.bookmarks.forEach(function(room) {

            let item =
                document.createElement("div");

            item.className =
                "bookmark-item";


            item.innerHTML = `

                <a
                    href="room.php?id=${room.ListingID}"
                    style="
                        text-decoration:none;
                        color:inherit;
                        display:block;
                    "
                >

                    <h4>
                        ${room.RoomType}
                    </h4>

                    <p>
                        ${room.Neighbourhood},
                        ${room.State},
                        ${room.Country}
                    </p>

                    <p>

                        <strong>
                            ${Number(room.Price).toFixed(2)}
                            ${room.Currency}
                        </strong>

                        / month

                    </p>

                </a>

            `;


            content.appendChild(item);

        });

    })

    .catch(error => {

        console.error("Load bookmarks error:", error);

        content.innerHTML =
            "Unable to load bookmarks.";

    });

}

</script>




</body>

</html>