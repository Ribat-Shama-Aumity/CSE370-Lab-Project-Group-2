<!-- ============================================================
     ROOM REVIEW FEATURE - PART 2 : WHAT YOU SEE
     ------------------------------------------------------------
     Included inside room.php, right after the Utilities card.
     All the values it uses were worked out in listing_review.php
============================================================ -->


<style>

    /* ================= REVIEW CARD ================= */

    .review-card {
        background-color: white;

        padding: 25px 28px;
        margin-top: 25px;

        border-radius: 10px;

        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .review-card h2 {
        color: #000080;
        font-size: 20px;

        margin: 0 0 5px 0;
    }


    /* ================= THE AVERAGE ================= */

    .review-average {
        display: flex;
        align-items: center;
        gap: 12px;

        padding-bottom: 20px;
        margin-bottom: 5px;

        border-bottom: 1px solid #ddd;
    }

    .average-number {
        color: #000080;
        font-size: 26px;
        font-weight: bold;
    }

    .average-count {
        color: #777;
        font-size: 14px;
    }


    /* ================= STARS ================= */

    .stars {
        display: inline-flex;
        gap: 2px;
    }

    .stars svg {
        width: 20px;
        height: 20px;

        color: #f5a623;
    }

    .stars.small svg {
        width: 15px;
        height: 15px;
    }


    /* ================= ONE REVIEW ================= */

    .one-review {
        padding: 18px 0;

        border-bottom: 1px solid #eee;
    }

    .one-review:last-child {
        border-bottom: none;
    }

    .review-top {
        display: flex;
        align-items: center;
        gap: 10px;

        margin-bottom: 8px;
    }

    .review-name {
        font-weight: bold;
        font-size: 15px;
    }

    .review-date {
        color: #999;
        font-size: 13px;

        margin-left: auto;
    }

    .review-text {
        font-size: 15px;
        line-height: 1.5;

        margin: 0;
        color: #444;
    }

    .review-empty {
        color: #888;
        font-size: 15px;

        padding: 18px 0;
    }


    /* ================= WRITE A REVIEW ================= */

    .write-review {
        margin-top: 25px;
        padding-top: 22px;

        border-top: 1px solid #ddd;
    }

    .write-review h3 {
        color: #000080;
        font-size: 17px;

        margin: 0 0 15px 0;
    }

    .rating-label {
        font-size: 14px;
        font-weight: bold;
        color: #333;

        margin-bottom: 10px;
    }

    .rating-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;

        margin-bottom: 18px;
    }

    /* the real radio button is hidden.
       we only show the pill next to it.
       this is the same trick as the filter pills. */

    .rating-pill input {
        display: none;
    }

    .rating-pill span {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        border: 1px solid #ccc;
        border-radius: 20px;

        padding: 8px 16px;

        font-size: 14px;
        color: #333;

        cursor: pointer;
    }

    .rating-pill span svg {
        width: 15px;
        height: 15px;
    }

    /* when the hidden radio is chosen,
       paint the pill next to it navy */

    .rating-pill input:checked + span {
        background-color: #000080;
        border-color: #000080;
        color: white;
    }

    .write-review textarea {
        width: 100%;
        height: 90px;

        padding: 12px;

        border: 1px solid #ccc;
        border-radius: 7px;

        font-family: Arial, sans-serif;
        font-size: 15px;

        resize: vertical;
    }

    .write-review textarea:focus {
        outline: none;
        border-color: #000080;
    }

    .review-button {
        margin-top: 15px;

        padding: 12px 28px;

        background-color: #000080;
        color: white;

        border: none;
        border-radius: 7px;

        font-size: 15px;
        cursor: pointer;
    }

    .review-button:hover {
        background-color: #000066;
    }


    /* ================= MESSAGES ================= */

    .review-done {
        background-color: #d4edda;
        color: #155724;

        padding: 14px 18px;
        margin-bottom: 20px;

        border-radius: 8px;
    }

    .review-locked {
        margin-top: 25px;
        padding-top: 22px;

        border-top: 1px solid #ddd;

        color: #888;
        font-size: 15px;
    }

</style>



<div class="review-card">


    <h2>Reviews</h2>


    <!-- message after a review was saved -->

    <?php if (isset($_GET["reviewed"])) { ?>

        <div class="review-done" style="margin-top:15px;">
            Thanks! Your review has been saved.
        </div>

    <?php } ?>


    <!-- ========================================================
         THE STAR AVERAGE
         --------------------------------------------------------
         number_format rounds 4.333333 down to 4.3
    ========================================================= -->

    <div class="review-average">

        <?php if ($review_count > 0) { ?>

            <div class="average-number">
                <?php echo number_format($average_rating, 1); ?>
            </div>


            <div class="stars">

                <?php

                // print 5 stars. Fill in as many as the average.

                for ($i = 1; $i <= 5; $i++) {

                    if ($i <= round($average_rating)) {

                        // filled star
                        echo '<svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2 l3 6.5 l7 0.8 l-5 4.8 l1.3 7
                                         l-6.3 -3.4 l-6.3 3.4 l1.3 -7
                                         l-5 -4.8 l7 -0.8 z"/>
                              </svg>';

                    } else {

                        // empty star
                        echo '<svg viewBox="0 0 24 24" fill="none"
                                   stroke="currentColor" stroke-width="1.6"
                                   stroke-linejoin="round">
                                <path d="M12 2 l3 6.5 l7 0.8 l-5 4.8 l1.3 7
                                         l-6.3 -3.4 l-6.3 3.4 l1.3 -7
                                         l-5 -4.8 l7 -0.8 z"/>
                              </svg>';
                    }
                }

                ?>

            </div>


            <div class="average-count">

                <?php echo $review_count; ?>

                <?php if ($review_count == 1) { ?>
                    review
                <?php } else { ?>
                    reviews
                <?php } ?>

            </div>

        <?php } else { ?>

            <div class="average-count">
                No reviews yet for this room.
            </div>

        <?php } ?>

    </div>



    <!-- ========================================================
         THE LIST OF REVIEWS
    ========================================================= -->

    <?php if ($review_count > 0) { ?>


        <?php while ($review = mysqli_fetch_assoc($list_result)) { ?>


            <div class="one-review">


                <div class="review-top">


                    <div class="stars small">

                        <?php

                        for ($i = 1; $i <= 5; $i++) {

                            if ($i <= $review["Rating"]) {

                                echo '<svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2 l3 6.5 l7 0.8 l-5 4.8 l1.3 7
                                                 l-6.3 -3.4 l-6.3 3.4 l1.3 -7
                                                 l-5 -4.8 l7 -0.8 z"/>
                                      </svg>';

                            } else {

                                echo '<svg viewBox="0 0 24 24" fill="none"
                                           stroke="currentColor" stroke-width="1.6"
                                           stroke-linejoin="round">
                                        <path d="M12 2 l3 6.5 l7 0.8 l-5 4.8 l1.3 7
                                                 l-6.3 -3.4 l-6.3 3.4 l1.3 -7
                                                 l-5 -4.8 l7 -0.8 z"/>
                                      </svg>';
                            }
                        }

                        ?>

                    </div>


                    <div class="review-name">

                        <?php

                        echo htmlspecialchars(
                            $review["First_name"] . " " .
                            $review["Last_name"]
                        );

                        ?>

                    </div>


                    <div class="review-date">

                        <?php

                        // turn 2026-08-31 14:05:00 into 31 Aug 2026

                        echo date(
                            "d M Y",
                            strtotime($review["Created_at"])
                        );

                        ?>

                    </div>


                </div>


                <p class="review-text">

                    <?php echo htmlspecialchars($review["Comment"]); ?>

                </p>


            </div>


        <?php } ?>


    <?php } ?>



    <!-- ========================================================
         WRITE A REVIEW
         --------------------------------------------------------
         Only shown to a student who booked this room.
    ========================================================= -->

    <?php if ($review_allowed == 1) { ?>


        <div class="write-review">


            <h3>
                <?php if ($has_my_review == 1) { ?>
                    Edit Your Review
                <?php } else { ?>
                    Write a Review
                <?php } ?>
            </h3>


            <form method="POST">


                <div class="rating-label">
                    Your rating
                </div>


                <!-- ===== RATING PILLS + STAR ICONS ===== -->

                <div class="rating-group">


                    <?php for ($i = 1; $i <= 5; $i++) { ?>

                        <label class="rating-pill">

                            <input
                                type="radio"
                                name="rating"
                                value="<?php echo $i; ?>"
                                <?php if ($my_rating == $i) { echo "checked"; } ?>
                            >

                            <span>

                                <?php echo $i; ?>

                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2 l3 6.5 l7 0.8 l-5 4.8 l1.3 7
                                             l-6.3 -3.4 l-6.3 3.4 l1.3 -7
                                             l-5 -4.8 l7 -0.8 z"/>
                                </svg>

                            </span>

                        </label>

                    <?php } ?>


                </div>


                <textarea
                    name="comment"
                    placeholder="What was it like living here?"
                    required
                ><?php echo htmlspecialchars($my_comment); ?></textarea>


                <button
                    type="submit"
                    name="save_review"
                    class="review-button"
                >
                    <?php if ($has_my_review == 1) { ?>
                        Update Review
                    <?php } else { ?>
                        Submit Review
                    <?php } ?>
                </button>


            </form>


        </div>


    <?php } else { ?>


        <div class="review-locked">

            Only students who have booked this room can leave a review.

        </div>


    <?php } ?>


</div>