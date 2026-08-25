<?php

session_start();


// Only a logged-in student can open this page
if (!isset($_SESSION["loggedIn"]) || $_SESSION["userType"] != "student") {

    header("Location: login.php");
    exit();

}


include "DBconnect.php";


$std_id = $_SESSION["Std_ID"];

$message = "";


// ============================================================
// PART 1 : STUDENT SENDS A NEW QUESTION
// ============================================================
// The Answer is left NULL, which means "not answered yet".
// An unanswered question does NOT appear in the list below,
// so we tell the student what happened with a message.

if (isset($_POST["ask"])) {

    $question = $_POST["question"];


    // Put a \ in front of any quote the student typed,
    // so they cannot break our SQL (SQL injection)

    $safe_question = mysqli_real_escape_string($conn, $question);


    $sql = "INSERT INTO FAQ
            (Question, Answer, Std_ID)
            VALUES
            ('$safe_question', NULL, '$std_id')";


    if (mysqli_query($conn, $sql)) {

        $message = "Thanks! Your question has been sent to the admin.
                    It will appear on this page once it is answered.";

    } else {

        $message = "Something went wrong. Please try again.";

    }

}


// ============================================================
// PART 2 : GET THE ANSWERED QUESTIONS
// ============================================================
// IS NOT NULL means "this row has an answer".
// You cannot write  Answer != NULL  in SQL. NULL means
// "no value at all", so it has its own words:
// IS NULL and IS NOT NULL.
//
// LEFT JOIN brings in the name of the student who asked.
// It has to be a LEFT join, not a plain JOIN, because
// Std_ID is allowed to be NULL for general questions
// written by the admin. A plain JOIN would throw those
// rows away.

$sql = "SELECT

            FAQ.FAQ_ID,
            FAQ.Question,
            FAQ.Answer,
            FAQ.Std_ID,

            Student.First_name,
            Student.Last_name

        FROM FAQ

        LEFT JOIN Student

        ON FAQ.Std_ID = Student.Std_ID

        WHERE FAQ.Answer IS NOT NULL

        ORDER BY FAQ.FAQ_ID DESC";


$result = mysqli_query($conn, $sql);

$faq_count = mysqli_num_rows($result);

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Global Nest - FAQ</title>

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

    .navigation a {
        color: white;
        text-decoration: none;
        font-size: 16px;
        margin-left: 25px;
    }

    .navigation a:hover {
        text-decoration: underline;
    }


    /* ================= MAIN ================= */

    .container {
        width: 90%;
        max-width: 850px;
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


    /* ================= ONE FAQ CARD ================= */

    .faq-card {
        background-color: white;

        padding: 22px 25px;
        margin-bottom: 18px;

        border-radius: 10px;

        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .faq-question {
        color: #000080;
        font-size: 18px;
        font-weight: bold;

        margin: 0 0 10px 0;
    }

    .faq-answer {
        font-size: 15px;
        line-height: 1.5;

        margin: 0;
    }

    .faq-asked-by {
        color: #888;
        font-size: 13px;

        margin-top: 12px;
    }


    /* ================= ASK BOX ================= */

    .ask-box {
        background-color: white;

        padding: 25px;
        margin-top: 35px;

        border-radius: 10px;

        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .ask-box h2 {
        color: #000080;
        margin-top: 0;
        font-size: 20px;
    }

    .ask-box textarea {
        width: 100%;
        height: 90px;

        padding: 12px;

        border: 1px solid #ccc;
        border-radius: 7px;

        font-family: Arial, sans-serif;
        font-size: 15px;

        resize: vertical;
    }

    .ask-box textarea:focus {
        outline: none;
        border-color: #000080;
    }

    .ask-button {
        margin-top: 15px;

        padding: 12px 28px;

        background-color: #000080;
        color: white;

        border: none;
        border-radius: 7px;

        font-size: 15px;
        cursor: pointer;
    }

    .ask-button:hover {
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

        <strong>Global Nest</strong>
        - Student Room Matcher

    </div>


    <div class="navigation">

        <a href="dashboard.php">
            Back to Dashboard
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>

</div>



<!-- ================= MAIN ================= -->

<div class="container">


    <h1 class="page-title">
        Frequently Asked Questions
    </h1>

    <p class="page-note">
        Answers to the questions students ask most often.
    </p>



    <!-- MESSAGE AFTER SENDING A QUESTION -->

    <?php if ($message != "") { ?>

        <div class="message">
            <?php echo $message; ?>
        </div>

    <?php } ?>



    <!-- ================= THE LIST ================= -->

    <?php if ($faq_count > 0) { ?>


        <?php while ($faq = mysqli_fetch_assoc($result)) { ?>


            <div class="faq-card">


                <p class="faq-question">

                    <?php echo htmlspecialchars($faq["Question"]); ?>

                </p>


                <p class="faq-answer">

                    <?php echo htmlspecialchars($faq["Answer"]); ?>

                </p>


                <!-- Std_ID is NULL for general questions written
                     by the admin, so we only print this line
                     when somebody really asked it -->

                <?php if ($faq["Std_ID"] != NULL) { ?>

                    <div class="faq-asked-by">

                        Asked by

                        <?php

                        echo htmlspecialchars(
                            $faq["First_name"] . " " .
                            $faq["Last_name"]
                        );

                        ?>

                    </div>

                <?php } ?>


            </div>


        <?php } ?>


    <?php } else { ?>


        <div class="empty">

            <h2>No Questions Yet</h2>

            <p>
                There are no answered questions right now.
            </p>

        </div>


    <?php } ?>



    <!-- ================= ASK A QUESTION ================= -->

    <div class="ask-box">


        <h2>Ask a Question</h2>


        <form method="POST">


            <textarea
                name="question"
                placeholder="Type your question here..."
                required
            ></textarea>


            <button
                type="submit"
                name="ask"
                class="ask-button"
            >
                Send Question
            </button>


        </form>


    </div>


</div>


</body>

</html>

