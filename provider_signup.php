<?php

include "DBconnect.php";

$message = "";

if (isset($_POST["signup"])) {

    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $first_name = mysqli_real_escape_string($conn, $_POST["first_name"]);
    $last_name = mysqli_real_escape_string($conn, $_POST["last_name"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $phone = mysqli_real_escape_string($conn, $_POST["phone"]);


    // Check username or email already exists
    $check = "SELECT * FROM Room_Provider
              WHERE Username = '$username'
              OR Email = '$email'";

    $result = mysqli_query($conn, $check);


    if (mysqli_num_rows($result) > 0) {

        $message = "Username or Email already exists!";

    } else {

        // Insert new provider
        $sql = "INSERT INTO Room_Provider
                (Username, First_name, Last_name, Email, Password, Phone)
                VALUES
                ('$username', '$first_name', '$last_name',
                 '$email', '$password', '$phone')";


        if (mysqli_query($conn, $sql)) {

            $message = "Account created successfully!";

        } else {

            $message = "Something went wrong!";

        }
    }
}

?>


<!DOCTYPE html>
<html>

<head>

    <title>Room Provider Signup</title>

    <style>

        body {
            font-family: Arial;
            background-color: #9788d4;
        }

        .box {
            width: 400px;
            margin: 60px auto;
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
            margin-top: 15px;
        }

    </style>

</head>


<body>


<div class="box">

    <h2>Room Provider Signup</h2>


    <form method="POST">

        First Name:
        <input type="text" name="first_name" required>


        Last Name:
        <input type="text" name="last_name">


        Username:
        <input type="text" name="username" required>


        Email:
        <input type="email" name="email" required>


        Phone:
        <input type="text" name="phone">


        Password:
        <input type="password" name="password" required>


        <button type="submit" name="signup">
            Sign Up
        </button>

    </form>


    <p class="message">
        <?php echo $message; ?>
    </p>


    <p>
        Already have an account?
        <a href="provider_login.php">Login</a>
    </p>


</div>


</body>

</html>