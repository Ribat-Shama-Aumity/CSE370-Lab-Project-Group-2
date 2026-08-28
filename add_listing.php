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

$message = "";


if (isset($_POST["add_listing"])) {

    $price = $_POST["price"];
    $currency = $_POST["currency"];

    $room_type = $_POST["room_type"];

    $country = $_POST["country"];
    $state = $_POST["state"];
    $neighbourhood = $_POST["neighbourhood"];

    $clinic = $_POST["clinic"];
    $grocery = $_POST["grocery"];
    $campus = $_POST["campus"];

    $provider_id = $_SESSION["Provider_ID"];

    
    // ===== UTILITY AMOUNTS =====

    $electricity = $_POST["electricity"];
    $wifi = $_POST["wifi"];
    $gas = $_POST["gas"];
    $water = $_POST["water"];
    $heating = $_POST["heating"];


    /*
       Legal document upload
    */

    $legal_doc = "";


    if (
        isset($_FILES["legal_doc"]) &&
        $_FILES["legal_doc"]["error"] == 0
    ) {

        $file_name = $_FILES["legal_doc"]["name"];
        $file_tmp = $_FILES["legal_doc"]["tmp_name"];


        // Create uploads folder if it does not exist

        if (!is_dir("uploads")) {

            mkdir("uploads");

        }


        // Create a new file name

        $new_file_name =
            "uploads/" . time() . "_" . $file_name;


        // Move uploaded file

        move_uploaded_file(
            $file_tmp,
            $new_file_name
        );


        $legal_doc = $new_file_name;

    }


    /*
       Insert listing into database
    */

    $sql = "INSERT INTO Listings
            (
                Price,
                Currency,
                RoomType,
                Country,
                State,
                Neighbourhood,
                Clinic,
                Grocery,
                Campus,
                Legal_doc,
                Provider_ID,
                Verification_Status
            )
            VALUES
            (
                '$price',
                '$currency',
                '$room_type',
                '$country',
                '$state',
                '$neighbourhood',
                '$clinic',
                '$grocery',
                '$campus',
                '$legal_doc',
                '$provider_id',
                'Pending'
            )";


    if (mysqli_query($conn, $sql)) {


        // ====================================================
        // GET THE ID OF THE LISTING WE JUST SAVED
        // ----------------------------------------------------
        // ListingID is AUTO_INCREMENT, so we did not choose
        // it. mysqli_insert_id() asks MySQL "what number did
        // you just give that row?"
        // We need it for the utility and photo rows.
        // ====================================================

        $new_listing_id = mysqli_insert_id($conn);


        // ====================================================
        // SAVE THE FOUR UTILITIES
        // ----------------------------------------------------
        // One row per utility, because Listing_Utility holds
        // one utility per row.
        // ====================================================

        $utility_sql = "INSERT INTO Listing_Utility
                        (ListingID, UtilityName, Amount)
                        VALUES
                        ('$new_listing_id', 'Electricity', '$electricity'),
                        ('$new_listing_id', 'Wifi', '$wifi'),
                        ('$new_listing_id', 'Gas', '$gas'),
                        ('$new_listing_id', 'Water', '$water'),
                        ('$new_listing_id', 'Heating', '$heating')";

        mysqli_query($conn, $utility_sql);


        // ====================================================
        // SAVE THE PHOTOS
        // ----------------------------------------------------
        // Photos go in their own folder, so public room
        // pictures are not mixed with private legal papers.
        //
        // Photos are optional. We look at each of the three
        // boxes and only save the ones that have a file.
        // ====================================================

        $photo_folder = "uploads/photos/";


        if (!is_dir($photo_folder)) {

            mkdir($photo_folder, 0777, true);

        }


        $photo_boxes = array("photo1", "photo2", "photo3");

        $photo_number = 1;


        foreach ($photo_boxes as $one_box) {


            if (
                isset($_FILES[$one_box]) &&
                $_FILES[$one_box]["error"] == 0
            ) {

                // build a file name nobody else will have

                $photo_path =
                    $photo_folder . time() . "_" .
                    $photo_number . "_" .
                    basename($_FILES[$one_box]["name"]);


                move_uploaded_file(
                    $_FILES[$one_box]["tmp_name"],
                    $photo_path
                );


                $safe_photo =
                    mysqli_real_escape_string($conn, $photo_path);


                $photo_sql = "INSERT INTO Listing_Photo
                              (ListingID, PhotoURL)
                              VALUES
                              ('$new_listing_id', '$safe_photo')";

                mysqli_query($conn, $photo_sql);


                $photo_number = $photo_number + 1;

            }

        }


        $message =
            "Listing submitted successfully! Waiting for admin verification.";

    } else {

        $message =
            "Something went wrong while adding the listing.";

    }

}

?>


<!DOCTYPE html>
<html>

<head>

    <title>Add Listing - Global Nest</title>


    <style>

        body {

            font-family: Arial, sans-serif;

            margin: 0;

            background-color: #f5f5f5;

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


        .header a {

            color: white;

            text-decoration: none;

        }


        /* FORM CONTAINER */

        .container {

            width: 600px;

            margin: 40px auto;

            background-color: white;

            padding: 30px;

            border-radius: 10px;

            box-shadow:
                0 3px 10px rgba(0,0,0,0.15);

        }


        h1 {

            text-align: center;

            color: navy;

            margin-bottom: 30px;

        }


        /* LABEL */

        label {

            display: block;

            margin-top: 15px;

            margin-bottom: 6px;

            font-weight: bold;

        }


        /* INPUT */

        input,
        select {

            width: 100%;

            padding: 10px;

            box-sizing: border-box;

            border: 1px solid #ccc;

            border-radius: 5px;

            font-size: 15px;

        }


        input:focus,
        select:focus {

            outline: none;

            border-color: navy;

        }


        /* SUBMIT BUTTON */

        .submit-button {

            width: 100%;

            margin-top: 25px;

            padding: 12px;

            background-color: navy;

            color: white;

            border: none;

            border-radius: 6px;

            font-size: 16px;

            cursor: pointer;

        }


        .submit-button:hover {

            background-color: #000066;

        }


        /* SUCCESS MESSAGE */

        .message {

            text-align: center;

            margin-top: 20px;

            color: green;

            font-weight: bold;

        }


        /* BACK LINK */

        .back {

            display: block;

            text-align: center;

            margin-top: 20px;

            color: navy;

            text-decoration: none;

        }

    </style>

</head>


<body>


<!-- HEADER -->

<div class="header">

    <strong>
        Global Nest
    </strong>


    <div>

        Hi,
        <?php
        echo htmlspecialchars($_SESSION["provider_name"]);
        ?>

        &nbsp;&nbsp;

        <a href="provider_dashboard.php">
            Dashboard
        </a>

        &nbsp;&nbsp;

        <a href="logout.php">
            Logout
        </a>

    </div>

</div>



<!-- FORM -->

<div class="container">


    <h1>
        Add New Listing
    </h1>


    <form
        method="POST"
        enctype="multipart/form-data"
    >


        <!-- PRICE -->

        <label>
            Price
        </label>

        <input
            type="text"
            name="price"
            placeholder="Enter price e.g. 250.50"
            required
        >


        <!-- CURRENCY -->

        <label>
            Currency
        </label>

        <input
            type="text"
            name="currency"
            placeholder="Enter currency e.g. BDT, USD, GBP"
            required
        >


        <!-- ROOM TYPE -->

        <label>
            Room Type
        </label>

        <select
            name="room_type"
            required
        >

            <option value="">
                Select Room Type
            </option>

            <option value="Single Room">
                Single Room
            </option>

            <option value="Shared Room">
                Shared Room
            </option>

            <option value="Studio">
                Studio
            </option>

        </select>


        <!-- COUNTRY -->

        <label>
            Country
        </label>

        <input
            type="text"
            name="country"
            placeholder="Enter country"
            required
        >


        <!-- STATE / CITY -->

        <label>
            State / City
        </label>

        <input
            type="text"
            name="state"
            placeholder="Enter state or city"
            required
        >


        <!-- NEIGHBOURHOOD -->

        <label>
            Neighbourhood
        </label>

        <input
            type="text"
            name="neighbourhood"
            placeholder="Enter neighbourhood"
            required
        >


        <!-- CLINIC -->

        <label>
            Distance from Clinic (km)
        </label>

        <input
            type="text"
            name="clinic"
            placeholder="Example: 0.80"
            required
        >


        <!-- GROCERY -->

        <label>
            Distance from Grocery (km)
        </label>

        <input
            type="text"
            name="grocery"
            placeholder="Example: 0.30"
            required
        >


        <!-- CAMPUS -->

        <label>
            Distance from Campus (km)
        </label>

        <input
            type="text"
            name="campus"
            placeholder="Example: 1.50"
            required
        >

                <!-- ===== UTILITY COSTS ===== -->

        <label>
            Electricity (per month)
        </label>

        <input
            type="number"
            step="0.01"
            name="electricity"
            placeholder="Example: 1200 (type 0 if not included)"
            required
        >


        <label>
            Wifi (per month)
        </label>

        <input
            type="number"
            step="0.01"
            name="wifi"
            placeholder="Example: 800 (type 0 if not included)"
            required
        >


        <label>
            Gas (per month)
        </label>

        <input
            type="number"
            step="0.01"
            name="gas"
            placeholder="Example: 500 (type 0 if not included)"
            required
        >


        <label>
            Water (per month)
        </label>

        <input
            type="number"
            step="0.01"
            name="water"
            placeholder="Example: 300 (type 0 if not included)"
            required
        >


        <label>
            Heating (per month)
        </label>

        <input
            type="number"
            step="0.01"
            name="heating"
            placeholder="Example: 0 (type 0 if not included)"
            required
        >


        <!-- ===== ROOM PHOTOS (optional) ===== -->

        <label>
            Room Photo 1 (optional)
        </label>

        <input
            type="file"
            name="photo1"
            accept="image/*"
        >


        <label>
            Room Photo 2 (optional)
        </label>

        <input
            type="file"
            name="photo2"
            accept="image/*"
        >


        <label>
            Room Photo 3 (optional)
        </label>

        <input
            type="file"
            name="photo3"
            accept="image/*"
        >

        <!-- LEGAL DOCUMENT -->

        <label>
            Legal Document
        </label>

        <input
            type="file"
            name="legal_doc"
            required
        >


        <!-- SUBMIT -->

        <button
            type="submit"
            name="add_listing"
            class="submit-button"
        >

            Submit Listing

        </button>


    </form>



    <?php if ($message != "") { ?>

        <div class="message">

            <?php
            echo $message;
            ?>

        </div>

    <?php } ?>



    <a
        href="provider_dashboard.php"
        class="back"
    >
        ← Back to Dashboard
    </a>


</div>


</body>

</html>