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