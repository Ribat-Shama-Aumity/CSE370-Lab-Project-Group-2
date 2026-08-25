<?php

session_start();

include "DBconnect.php";

$message = "";


if (isset($_POST["login"])) {

    $username = $_POST["username"];
    $password = $_POST["password"];


    $sql = "SELECT * FROM Room_Provider
            WHERE Username = '$username'
            AND Password = '$password'";


    $result = mysqli_query($conn, $sql);


    if (mysqli_num_rows($result) == 1) {

        $provider = mysqli_fetch_assoc($result);


        $_SESSION["loggedIn"] = true;

        $_SESSION["userType"] = "provider";

        $_SESSION["Provider_ID"] =
            $provider["Provider_ID"];

        $_SESSION["provider_name"] =
            $provider["First_name"] . " " .
            $provider["Last_name"];


        header("Location: provider_dashboard.php");

        exit();

    } else {

        $message = "Invalid Username or Password!";

    }

}

?>


<!DOCTYPE html>
<html>

<head>

    <title>Provider Login</title>

    <style>

        body {
            font-family: Arial;
            background-color: #f5f5f5;
        }

        .box {
            width: 350px;
            margin: 100px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            color: navy;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px 0;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: navy;
            color: white;
            border: none;
            cursor: pointer;
        }

        .message {
            text-align: center;
            color: red;
        }

    </style>

</head>


<body>


<div class="box">

    <h2>Room Provider Login</h2>


    <form method="POST">

        Username:

        <input type="text" name="username" required>


        Password:

        <input type="password" name="password" required>


        <button type="submit" name="login">
            Login
        </button>

    </form>


    <p class="message">

        <?php echo $message; ?>

    </p>


    <p>
        Don't have an account?

        <a href="provider_signup.php">
            Sign Up
        </a>

    </p>


</div>


</body>

</html>