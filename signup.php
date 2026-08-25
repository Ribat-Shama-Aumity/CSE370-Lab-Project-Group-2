<?php

include "DBconnect.php";

$message = "";

if (isset($_POST["signup"])) {

    $username = $_POST["username"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $nationality = $_POST["nationality"];
    $cooking = $_POST["cooking"];
    $sleep = $_POST["sleep"];


    /* Check if username or email already exists */

    $check = "SELECT * FROM Student
              WHERE Username = '$username'
              OR Email = '$email'";

    $result = mysqli_query($conn, $check);


    if (mysqli_num_rows($result) > 0) {

        $message = "Username or Email already exists!";

    } else {

        /* Insert new student */

        $sql = "INSERT INTO Student
                (Username, First_name, Last_name, Email, Password,
                 is_Verified, Nationality, CookingHabit, SleepSchedule)

                VALUES
                ('$username', '$first_name', '$last_name', '$email',
                 '$password', 0, '$nationality', '$cooking', '$sleep')";


        if (mysqli_query($conn, $sql)) {

            $message = "Account created successfully!";

        } else {

            $message = "Something went wrong. Please try again.";

        }

    }
}

?>


<!DOCTYPE html>
<html>

<head>

    <title>Global Nest - Sign Up</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;

            font-family: Arial, sans-serif;

            background:
                linear-gradient(
                    rgba(0, 0, 80, 0.5),
                    rgba(0, 0, 80, 0.5)
                ),
                url("images/login-bg.jpg");

            background-size: cover;
            background-position: center;

            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;
        }


        .signup-box {

            width: 500px;

            background-color: white;

            padding: 35px;

            border-radius: 15px;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.25);
        }


        .logo {

            text-align: center;

            color: #000080;

            font-size: 28px;

            font-weight: bold;

            margin-bottom: 5px;
        }


        .subtitle {

            text-align: center;

            color: #666;

            margin-bottom: 25px;
        }


        .row {

            display: flex;

            gap: 15px;
        }


        .field {

            width: 100%;

            margin-bottom: 15px;
        }


        label {

            display: block;

            margin-bottom: 6px;

            font-weight: bold;

            color: #333;
        }


        input,
        select {

            width: 100%;

            padding: 11px;

            border: 1px solid #ccc;

            border-radius: 7px;

            font-size: 14px;
        }


        input:focus,
        select:focus {

            outline: none;

            border-color: #000080;
        }


        .signup-button {

            width: 100%;

            padding: 12px;

            background-color: #000080;

            color: white;

            border: none;

            border-radius: 7px;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;

            margin-top: 10px;
        }


        .signup-button:hover {

            background-color: #000066;
        }


        .message {

            text-align: center;

            margin-top: 15px;

            color: #000080;

            font-size: 14px;
        }


        .login-text {

            text-align: center;

            margin-top: 20px;

            color: #666;

            font-size: 14px;
        }


        .login-text a {

            color: #000080;

            font-weight: bold;

            text-decoration: none;
        }


        .login-text a:hover {

            text-decoration: underline;
        }

    </style>

</head>


<body>


<div class="signup-box">


    <div class="logo">
        Global Nest
    </div>


    <div class="subtitle">
        Create your student account
    </div>


    <form method="POST">


        <!-- FIRST AND LAST NAME -->

        <div class="row">

            <div class="field">

                <label>First Name</label>

                <input
                    type="text"
                    name="first_name"
                    placeholder="First name"
                    required
                >

            </div>


            <div class="field">

                <label>Last Name</label>

                <input
                    type="text"
                    name="last_name"
                    placeholder="Last name"
                >

            </div>

        </div>


        <!-- USERNAME -->

        <div class="field">

            <label>Username</label>

            <input
                type="text"
                name="username"
                placeholder="Choose a username"
                required
            >

        </div>


        <!-- EMAIL -->

        <div class="field">

            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                required
            >

        </div>


        <!-- PASSWORD -->

        <div class="field">

            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Create a password"
                required
            >

        </div>


        <!-- NATIONALITY -->

        <div class="field">

            <label>Nationality</label>

            <input
                type="text"
                name="nationality"
                placeholder="Enter your nationality"
            >

        </div>


        <!-- COOKING HABIT -->

        <div class="field">

            <label>Cooking Habit</label>

            <select name="cooking">

                <option value="Cooks daily">
                    Cooks daily
                </option>

                <option value="Cooks weekends">
                    Cooks weekends
                </option>

                <option value="Rarely cooks">
                    Rarely cooks
                </option>

            </select>

        </div>


        <!-- SLEEP SCHEDULE -->

        <div class="field">

            <label>Sleep Schedule</label>

            <select name="sleep">

                <option value="Early bird">
                    Early bird
                </option>

                <option value="Night owl">
                    Night owl
                </option>

                <option value="Flexible">
                    Flexible
                </option>

            </select>

        </div>


        <!-- SIGN UP BUTTON -->

        <input
            type="submit"
            name="signup"
            value="Create Account"
            class="signup-button"
        >


    </form>


    <?php if ($message != "") { ?>

        <div class="message">
            <?php echo $message; ?>
        </div>

    <?php } ?>


    <div class="login-text">

        Already have an account?

        <a href="login.php">
            Login
        </a>

    </div>


</div>


</body>

</html>