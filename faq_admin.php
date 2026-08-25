<?php

session_start();


// Only a logged-in admin can open this page
if (!isset($_SESSION["loggedIn"]) || $_SESSION["userType"] != "admin") {

    header("Location: login.php");
    exit();

}


include "DBconnect.php";


$message = "";


// ============================================================
// PART 1 : ADMIN SAVES AN ANSWER
// ============================================================
// The row already exists, so this is an UPDATE, not an INSERT.
// Once Answer is filled in, the question stops being NULL
// and it appears on the student FAQ page.

if (isset($_POST["answer_it"])) {

    $faq_id = $_POST["faq_id"];
    $answer = $_POST["answer"];


    $safe_answer = mysqli_real_escape_string($conn, $answer);

    $safe_faq_id = mysqli_real_escape_string($conn, $faq_id);


    $sql = "UPDATE FAQ

            SET Answer = '$safe_answer'

            WHERE FAQ_ID = '$safe_faq_id'";


    if (mysqli_query($conn, $sql)) {

        $message = "Answer saved. It is now visible to students.";

    } else {

        $message = "Something went wrong. Please try again.";

    }

}


// ============================================================
// PART 2 : GET THE QUESTIONS THAT STILL NEED AN ANSWER
// ============================================================
// IS NULL means "this row has no answer yet".
// LEFT JOIN again, so a question with no Std_ID is still shown.

$sql = "SELECT

            FAQ.FAQ_ID,
            FAQ.Question,
            FAQ.Std_ID,

            Student.First_name,
            Student.Last_name

        FROM FAQ

        LEFT JOIN Student

        ON FAQ.Std_ID = Student.Std_ID

        WHERE FAQ.Answer IS NULL

        ORDER BY FAQ.FAQ_ID ASC";


$result = mysqli_query($conn, $sql);

$pending_count = mysqli_num_rows($result);

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Global Nest - Answer Questions</title>

<style>

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
        background-color: #000080;
        color: white;

        padding: 20px 40px;

        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        font-size: 22px;
        font-weight: bold;
    }

    .header a {
        color: white;
        text-decoration: none;
        margin-left: 20px;
    }

    .header a:hover {
        text-decoration: underline;
    }


    /* ================= MAIN ================= */

    .container {
        width: 90%;
        max-width: 900px;
        margin: 40px auto;
    }

    .page-title {
        color: #000080;
        margin: 0 0 5px 0;
    }

    .page-note {
        color: #666;
        margin: 0 0 30px 0;
    }


    /* ================= ONE QUESTION CARD ================= */

    .question-card {
        background-color: white;

        padding: 25px;
        margin-bottom: 22px;

        border-radius: 10px;

        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .question-text {
        color: #000080;
        font-size: 18px;
        font-weight: bold;

        margin: 0 0 8px 0;
    }

    .asked-by {
        color: #888;
        font-size: 13px;

        margin-bottom: 15px;
    }

    .question-card textarea {
        width: 100%;
        height: 85px;

        padding: 12px;

        border: 1px solid #ccc;
        border-radius: 7px;

        font-family: Arial, sans-serif;
        font-size: 15px;

        resize: vertical;
    }

    .question-card textarea:focus {
        outline: none;
        border-color: #000080;
    }

    .save-button {
        margin-top: 15px;

        padding: 11px 26px;

        background-color: #000080;
        color: white;

        border: none;
        border-radius: 7px;

        font-size: 15px;
        cursor: pointer;
    }

    .save-button:hover {
        background-color: #000066;
    }


    /* ================= MESSAGE ================= */

    .message {
        background-color: #d4edda;
        color: #155724;

        padding: 14px 18px;
        margin-bottom: 25px;

        border-radius: 8px;
    }


    /* ================= EMPTY ================= */

    .empty {
        background-color: white;

        padding: 40px;
        text-align: center;

        border-radius: 10px;
        color: #666;
    }

</style>

</head>


<body>


<!-- ================= HEADER ================= -->

<div class="header">

    <div class="logo">
        Global Nest - Admin
    </div>

    <div>

        <a href="admin_dashboard.php">
            Dashboard
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>

</div>



<!-- ================= MAIN ================= -->

<div class="container">


    <h1 class="page-title">
        Questions Waiting for an Answer
    </h1>

    <p class="page-note">
        A question is hidden from students until you answer it.
    </p>



    <?php if ($message != "") { ?>

        <div class="message">
            <?php echo $message; ?>
        </div>

    <?php } ?>



    <?php if ($pending_count > 0) { ?>


        <?php while ($faq = mysqli_fetch_assoc($result)) { ?>


            <div class="question-card">


                <p class="question-text">

                    <?php echo htmlspecialchars($faq["Question"]); ?>

                </p>


                <div class="asked-by">

                    <?php if ($faq["Std_ID"] != NULL) { ?>

                        Asked by

                        <?php

                        echo htmlspecialchars(
                            $faq["First_name"] . " " .
                            $faq["Last_name"]
                        );

                        ?>

                    <?php } else { ?>

                        General question

                    <?php } ?>

                </div>


                <form method="POST">


                    <!-- this hidden box tells PHP WHICH question
                         is being answered, because there is one
                         form per card on this page -->

                    <input
                        type="hidden"
                        name="faq_id"
                        value="<?php echo $faq["FAQ_ID"]; ?>"
                    >


                    <textarea
                        name="answer"
                        placeholder="Type the answer here..."
                        required
                    ></textarea>


                    <button
                        type="submit"
                        name="answer_it"
                        class="save-button"
                    >
                        Save Answer
                    </button>


                </form>


            </div>


        <?php } ?>


    <?php } else { ?>


        <div class="empty">

            <h2>Nothing Waiting</h2>

            <p>
                Every question has been answered.
            </p>

        </div>


    <?php } ?>


</div>


</body>

</html>