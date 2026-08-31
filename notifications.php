<?php

session_start();

// Notifications are only for students and providers.
// An admin has no Std_ID or Provider_ID, so we send
// them back to their own dashboard.

if (!isset($_SESSION["loggedIn"])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION["userType"] != "student" &&
    $_SESSION["userType"] != "provider") {

    header("Location: admin_dashboard.php");
    exit();
}

include "DBconnect.php";


/* ================= USER ================= */

if ($_SESSION["userType"] == "provider") {

    $user_id = $_SESSION["Provider_ID"];

} else {

    $user_id = $_SESSION["Std_ID"];

}


/* ================= MARK NOTIFICATIONS AS READ ================= */

$sql = "UPDATE notifications
        SET is_read = 1
        WHERE user_id = '$user_id'";

mysqli_query($conn, $sql);


/* ================= GET NOTIFICATIONS ================= */

$sql = "SELECT *
        FROM notifications
        WHERE user_id = '$user_id'
        ORDER BY created_at DESC
        LIMIT 50";

$result = mysqli_query($conn, $sql);

$notifications = array();


while ($row = mysqli_fetch_assoc($result)) {

    $notifications[] = $row;

}


/* ================= BACK PAGE ================= */

if ($_SESSION["userType"] == "provider") {

    $back_page = "provider_dashboard.php";

} else {

    $back_page = "dashboard.php";

}

?>


<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Global Nest - Notifications</title>


<style>

/* ================= GENERAL ================= */

* {
    box-sizing: border-box;
}


body {

    margin: 0;

    font-family: Arial, sans-serif;

    background-color: #f5f5f5;

    color: #222;

}


/* ================= HEADER ================= */

.header {

    height: 75px;

    background-color: #000080;

    color: white;

    display: flex;

    justify-content: space-between;

    align-items: center;

    padding: 0 40px;

}


.logo {

    font-size: 24px;

}


.nav a {

    color: white;

    text-decoration: none;

    margin-left: 25px;

    font-size: 16px;

}


.nav a:hover {

    text-decoration: underline;

}


/* ================= MAIN ================= */

.container {

    width: 90%;

    max-width: 850px;

    margin: 40px auto 60px auto;

}


/* ================= BACK BUTTON ================= */

.back {

    display: inline-block;

    color: #000080;

    text-decoration: none;

    font-size: 16px;

    margin-bottom: 20px;

}


.back:hover {

    text-decoration: underline;

}


/* ================= NOTIFICATION CARD ================= */

.card {

    background-color: white;

    border-radius: 10px;

    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);

}


/* ================= TITLE ================= */

.title {

    padding: 25px 30px;

    border-bottom: 1px solid #eee;

}


.title h1 {

    margin: 0;

    color: #000080;

    font-size: 26px;

}


/* ================= NOTIFICATION ================= */

.item {

    padding: 22px 30px;

    border-bottom: 1px solid #eee;

}


.item:last-child {

    border-bottom: none;

}


.type {

    color: #000080;

    font-weight: bold;

    font-size: 16px;

    margin-bottom: 8px;

}


.message {

    color: #555;

    font-size: 15px;

    line-height: 1.6;

}


.time {

    color: #888;

    font-size: 12px;

    margin-top: 10px;

}


/* ================= EMPTY ================= */

.empty {

    padding: 50px;

    text-align: center;

    color: #777;

    font-size: 15px;

}


/* ================= MOBILE ================= */

@media (max-width: 600px) {

    .header {

        padding: 0 20px;

    }


    .logo {

        font-size: 20px;

    }


    .nav a {

        margin-left: 12px;

    }


    .container {

        width: 94%;

    }


    .item {

        padding: 20px;

    }

}

</style>

</head>


<body>


<!-- ================= HEADER ================= -->

<div class="header">


    <div class="logo">

        <strong>Global Nest</strong>

    </div>


    <div class="nav">

        <a href="<?php echo $back_page; ?>">

            Back

        </a>


        <a href="logout.php">

            Logout

        </a>

    </div>


</div>



<!-- ================= MAIN ================= -->

<div class="container">


    <a href="<?php echo $back_page; ?>" class="back">

        ← Back

    </a>



    <!-- ================= NOTIFICATION CARD ================= -->

    <div class="card">


        <!-- TITLE -->

        <div class="title">

            <h1>

                Notifications

            </h1>

        </div>



        <!-- NOTIFICATIONS -->

        <?php if (count($notifications) > 0) { ?>


            <?php foreach ($notifications as $notification) { ?>


                <div class="item">


                    <div class="type">

                        <?php

                        if (!empty($notification["type"])) {

                            echo htmlspecialchars(
                                $notification["type"]
                            );

                        } else {

                            echo "Notification";

                        }

                        ?>

                    </div>



                    <div class="message">

                        <?php

                        echo htmlspecialchars(
                            $notification["message"]
                        );

                        ?>

                    </div>



                    <?php if (!empty($notification["created_at"])) { ?>

                        <div class="time">

                            <?php

                            echo htmlspecialchars(
                                $notification["created_at"]
                            );

                            ?>

                        </div>

                    <?php } ?>


                </div>


            <?php } ?>


        <?php } else { ?>


            <!-- NO NOTIFICATIONS -->

            <div class="empty">

                No notifications yet.

            </div>


        <?php } ?>


    </div>


</div>


</body>

</html>