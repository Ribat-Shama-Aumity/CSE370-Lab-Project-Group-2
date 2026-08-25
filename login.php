<?php

session_start();

include "DBconnect.php";

$message = "";

if (isset($_POST["login"])) {

    $userType = $_POST["userType"];
    $username = $_POST["username"];
    $password = $_POST["password"];


    /* STUDENT LOGIN */

    if ($userType == "student") {

        $sql = "SELECT * FROM Student
                WHERE Username = '$username'
                AND Password = '$password'";

        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {

            $student = mysqli_fetch_assoc($result);

            $_SESSION["loggedIn"] = true;
            $_SESSION["userType"] = "student";
            $_SESSION["Std_ID"] = $student["Std_ID"];
            $_SESSION["full_name"] =
                $student["First_name"] . " " . $student["Last_name"];
            $_SESSION["is_Verified"] =
                $student["is_Verified"];

            header("Location: dashboard.php");
            exit();

        } else {

            $message = "Invalid student username or password!";

        }
    }


    /* ADMIN LOGIN */

    else if ($userType == "admin") {

        $sql = "SELECT * FROM Admin
                WHERE Username = '$username'
                AND Password = '$password'";

        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {

            $admin = mysqli_fetch_assoc($result);

            $_SESSION["loggedIn"] = true;
            $_SESSION["userType"] = "admin";
            $_SESSION["Admin_ID"] = $admin["Admin_ID"];
            $_SESSION["full_name"] = $admin["Username"];

            header("Location: admin_dashboard.php");
            exit();

        } else {

            $message = "Invalid admin username or password!";

        }
    }

        /* ROOM PROVIDER LOGIN */

    else if ($userType == "provider") {

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

            $_SESSION["full_name"] =
                $provider["First_name"] . " " .
                $provider["Last_name"];
				
			$_SESSION["provider_name"] =
                $provider["First_name"] . " " .
                $provider["Last_name"];

            header("Location: provider_dashboard.php");
            exit();

        } else {

            $message = "Invalid provider username or password!";

        }
    }

}

?>


<!DOCTYPE html>
<html>

<head>

    <title>Global Nest - Login</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
    margin: 0;
    font-family: Arial, sans-serif;

    background-image: url("pic2.png");

    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;

    height: 100vh;

    display: flex;
    justify-content: center;
    align-items: center;
}


        /* LOGIN BOX */

        .login-box {

            width: 380px;

            background-color: white;

            padding: 40px;

            border-radius: 15px;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.25);
        }


        /* LOGO */

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

            margin-bottom: 30px;
        }


        /* LABEL */

        label {

            display: block;

            margin-bottom: 7px;

            font-weight: bold;

            color: #333;
        }


        /* INPUT AND SELECT */

        input,
        select {

            width: 100%;

            padding: 12px;

            border: 1px solid #ccc;

            border-radius: 7px;

            font-size: 15px;

            margin-bottom: 18px;
        }


        input:focus,
        select:focus {

            outline: none;

            border-color: #000080;
        }


        /* LOGIN BUTTON */

        .login-button {

            width: 100%;

            padding: 12px;

            background-color: #000080;

            color: white;

            border: none;

            border-radius: 7px;

            font-size: 16px;

            font-weight: bold;

            cursor: pointer;
        }


        .login-button:hover {

            background-color: #000066;
        }


        /* ERROR MESSAGE */

        .error {

            color: #d00000;

            text-align: center;

            margin-top: 18px;

            font-size: 14px;
        }


        .footer {

            text-align: center;

            margin-top: 25px;

            color: #888;

            font-size: 13px;
        }

        .signup-text {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }

        .signup-text a {
            color: #000080;
            font-weight: bold;
            text-decoration: none;
        }

        .signup-text a:hover {
            text-decoration: underline;
        }

    </style>

</head>


<body>


    <div class="login-box">


        <div class="logo">
            Global Nest
        </div>


        <div class="subtitle">
            Student Room Matcher
        </div>


        <form method="POST">


            <label>
                Login As
            </label>

            <select name="userType" required>

                <option value="">
                    Select User Type
                </option>

                <option value="student">
                    Student
                </option>

                <option value="admin">
                    Admin
                </option>

                <option value="provider">
                    Room Provider
                </option>

            </select>


            <label>
                Username
            </label>

            <input
                type="text"
                name="username"
                placeholder="Enter your username"
                required
            >


            <label>
                Password
            </label>

            <input
                type="password"
                name="password"
                placeholder="Enter your password"
                required
            >


            <input
                type="submit"
                name="login"
                value="Login"
                class="login-button"
            >
            <div class="signup-text">

                Don't have an account?

                <a href="signup.php">
                    Student Sign Up
                </a>
                <br><br>

                Are you a room provider?

                <a href="provider_signup.php">
                    Provider Sign Up
                </a>

            </div>

        </form>


        <?php if ($message != "") { ?>

            <div class="error">
                <?php echo $message; ?>
            </div>

        <?php } ?>


        <div class="footer">
            © 2026 Global Nest
        </div>


    </div>


</body>

</html>