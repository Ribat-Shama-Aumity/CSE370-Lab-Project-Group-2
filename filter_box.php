<!-- ============================================================
     FILTER FEATURE - PART 2 : WHAT YOU SEE
     ------------------------------------------------------------
     Included inside dashboard.php, right after the hero.
     It prints the filter bar and the filter box.

     All the values it uses ($view, $panel, $country ...)
     were worked out in filter.php.
============================================================ -->


<style>

    /* ================= FILTER BAR ================= */

    .filter-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;

        background-color: white;

        margin: 25px 40px 0 40px;
        padding: 12px 20px;

        border-radius: 10px;

        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    .filter-button {
        display: flex;
        align-items: center;
        gap: 8px;

        background-color: white;
        color: #000080;

        border: 1px solid #000080;
        border-radius: 20px;

        padding: 8px 18px;

        font-size: 15px;
        cursor: pointer;
    }

    .filter-button:hover {
        background-color: #000080;
        color: white;
    }

    .filter-button svg {
        width: 16px;
        height: 16px;
    }


    /* ================= LIST / GRID BUTTONS ================= */

    .view-buttons {
        display: flex;
        gap: 8px;
    }

    .view-button {
        width: 38px;
        height: 38px;

        background-color: white;
        color: #888;

        border: 1px solid #ccc;
        border-radius: 8px;

        cursor: pointer;

        display: flex;
        justify-content: center;
        align-items: center;
    }

    .view-button svg {
        width: 20px;
        height: 20px;
    }

    .view-button.active {
        background-color: #000080;
        color: white;
        border-color: #000080;
    }


    /* ================= LIST VIEW ================= */
    /* The room cards themselves are NOT changed at all.
       Only the box around them gets a different class,
       and this CSS lays the same pieces out in a row.   */

    .room-list {
        display: block;
        padding: 25px 40px 50px 40px;
    }

    .room-list .room-card {
        display: flex;
        align-items: center;
        gap: 20px;

        padding: 18px 22px;
        margin-bottom: 16px;
    }

    /* flex-shrink: 0 stops a piece from being squeezed */

    .room-list .room-card h3 {
        width: 150px;
        flex-shrink: 0;
        margin: 0;
    }

    .room-list .room-location {
        width: 220px;
        flex-shrink: 0;
        margin: 0;
    }

    .room-list .room-price {
        width: 150px;
        flex-shrink: 0;
        margin: 0;
    }

    .room-list .room-info {
        width: 105px;
        flex-shrink: 0;
        margin: 0;
    }

    /* margin-left: auto pushes the button to the far right */

    .room-list .interest-button {
        width: 170px;
        flex-shrink: 0;
        margin-left: auto;
    }


    /* ================= THE POP-UP BOX ================= */

    .modal-back {
        position: fixed;
        top: 0;
        left: 0;

        width: 100%;
        height: 100%;

        background-color: rgba(0, 0, 0, 0.5);

        z-index: 10;
    }

    .modal-box {
        width: 800px;
        max-width: 92%;

        background-color: white;

        margin: 60px auto;

        border-radius: 12px;

        overflow: hidden;
    }

    .modal-head {
        display: flex;
        justify-content: space-between;
        align-items: center;

        padding: 18px 25px;

        border-bottom: 1px solid #ddd;
    }

    .modal-head h2 {
        margin: 0;
        color: #000080;
        font-size: 20px;
    }

    .close-button {
        background: none;
        border: none;

        font-size: 24px;
        color: #666;

        cursor: pointer;
    }

    .modal-body {
        display: flex;
        height: 380px;
    }


    /* left menu */

    .modal-menu {
        width: 210px;

        background-color: #fafafa;

        border-right: 1px solid #ddd;
    }

    .menu-item {
        width: 100%;

        background: none;
        border: none;
        border-bottom: 1px solid #eee;

        padding: 18px 22px;

        text-align: left;
        font-size: 15px;
        color: #333;

        cursor: pointer;
    }

    .menu-item:hover {
        background-color: #f0f0f0;
    }

    .menu-item.active {
        color: #000080;
        font-weight: bold;
        background-color: white;
    }


    /* right side */

    .modal-panel {
        flex: 1;
        padding: 22px 25px;
        overflow-y: auto;
    }

    .modal-panel h3 {
        margin: 0 0 18px 0;
        color: #000080;
        font-size: 17px;
    }

    .option-row {
        display: block;

        padding: 9px 0;

        font-size: 15px;
        color: #333;

        cursor: pointer;
    }

    .option-row:hover {
        color: #000080;
    }


    /* budget boxes */

    .price-row {
        display: flex;
        gap: 20px;
    }

    .price-field {
        width: 160px;
    }

    .price-field label {
        display: block;
        margin-bottom: 6px;
        color: #666;
        font-size: 14px;
    }

    .price-input {
        width: 100%;

        padding: 10px 15px;

        border: 1px solid #ccc;
        border-radius: 20px;

        font-size: 14px;
    }

    .price-input:focus {
        outline: none;
        border-color: #000080;
    }


    /* ================= PILLS ================= */

    .pill-title {
        margin: 18px 0 10px 0;
        font-size: 14px;
        font-weight: bold;
        color: #333;
    }

    .pill-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    /* the real checkbox is hidden.
       we only show the pretty box next to it */

    .pill input {
        display: none;
    }

    .pill-box {
        display: inline-flex;
        align-items: center;
        gap: 7px;

        border: 1px solid #ccc;
        border-radius: 20px;

        padding: 8px 16px;

        font-size: 14px;
        color: #333;

        cursor: pointer;
    }

    .pill-box svg {
        width: 17px;
        height: 17px;
    }

    /* when the hidden checkbox is ticked,
       paint the box next to it navy */

    .pill input:checked + .pill-box {
        background-color: #000080;
        border-color: #000080;
        color: white;
    }


    /* ================= BOTTOM OF THE BOX ================= */

    .modal-foot {
        display: flex;
        justify-content: space-between;
        align-items: center;

        padding: 15px 25px;

        border-top: 1px solid #ddd;
    }

    .clear-link {
        color: #888;
        text-decoration: none;
        font-size: 15px;
    }

    .clear-link:hover {
        color: #000080;
    }

    .show-button {
        background-color: #000080;
        color: white;

        border: none;
        border-radius: 8px;

        padding: 12px 35px;

        font-size: 15px;
        cursor: pointer;
    }

    .show-button:hover {
        background-color: #000066;
    }

</style>



<!-- ============================================================
     ONE FORM HOLDS THE BAR AND THE POP-UP BOX.
     Every button inside it sends ALL the choices together,
     so nothing is lost when you press one.
============================================================ -->

<form method="GET">


<!-- these two hidden boxes carry the current view and tab -->

<input type="hidden" name="oldview"
       value="<?php echo htmlspecialchars($view); ?>">

<input type="hidden" name="oldpanel"
       value="<?php echo htmlspecialchars($panel); ?>">



<!-- ============================================================
     THE FILTER BAR
============================================================ -->

<div class="filter-bar">


    <!-- FILTER BUTTON : opens the box on the Country tab -->

    <button class="filter-button" type="submit"
            name="panel" value="country">

        <!-- funnel icon -->
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round">
            <path d="M3 4h18l-7 8v6l-4 2v-8z"/>
        </svg>

        Filters

    </button>


    <!-- LIST AND GRID BUTTONS -->

    <div class="view-buttons">


        <!-- LIST BUTTON -->

        <button type="submit" name="view" value="list"
                class="view-button
                <?php if ($view == "list") { echo "active"; } ?>">

            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M5 4 v14"/>
                <path d="M2 15 l3 3 l3 -3"/>
                <path d="M11 5 h11"/>
                <path d="M11 10 h9"/>
                <path d="M11 15 h6"/>
                <path d="M11 20 h4"/>
            </svg>

        </button>


        <!-- GRID BUTTON -->

        <button type="submit" name="view" value="grid"
                class="view-button
                <?php if ($view == "grid") { echo "active"; } ?>">

            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round">
                <rect x="3"  y="3"  width="7" height="7" rx="2"/>
                <rect x="14" y="3"  width="7" height="7" rx="2"/>
                <rect x="3"  y="14" width="7" height="7" rx="2"/>
                <rect x="14" y="14" width="7" height="7" rx="2"/>
            </svg>

                </button>


    </div>


</div>



<!-- ============================================================
     HIDDEN BOXES THAT CARRY THE CHOICES
     ------------------------------------------------------------
     These sit OUTSIDE the pop-up box, so they are on the page
     even when the box is closed. Without them the List and Grid
     buttons would send no filters and every listing would
     come back.

     A tab's hidden box is only printed when that tab is NOT
     on screen. If it is on screen, its real inputs do the job,
     and printing both would send the same name twice.
============================================================ -->

<?php if ($panel != "country") { ?>
    <input type="hidden" name="country"
           value="<?php echo htmlspecialchars($country); ?>">
<?php } ?>


<?php if ($panel != "locality") { ?>
    <input type="hidden" name="locality"
           value="<?php echo htmlspecialchars($locality); ?>">
<?php } ?>


<?php if ($panel != "budget") { ?>

    <input type="hidden" name="currency"
           value="<?php echo htmlspecialchars($currency); ?>">

    <input type="hidden" name="min_price"
           value="<?php echo htmlspecialchars($min_price); ?>">

    <input type="hidden" name="max_price"
           value="<?php echo htmlspecialchars($max_price); ?>">

<?php } ?>


<?php if ($panel != "room") { ?>

    <?php foreach ($roomtype as $one_room) { ?>

        <input type="hidden" name="roomtype[]"
               value="<?php echo htmlspecialchars($one_room); ?>">

    <?php } ?>

<?php } ?>



<!-- ============================================================
     THE POP-UP BOX
     It is only printed when $panel is not empty.
============================================================ -->

<?php if ($panel != "") { ?>


<div class="modal-back">


    <div class="modal-box">


        <!-- ================= HEAD ================= -->

        <div class="modal-head">

            <h2>Filter</h2>

            <!-- closing the box = send an empty panel -->

            <button class="close-button" type="submit"
                    name="panel" value="">
                &times;
            </button>

        </div>


        <!-- ================= BODY ================= -->

        <div class="modal-body">


            <!-- LEFT MENU : each tab is a submit button -->

            <div class="modal-menu">

                <button type="submit" name="panel" value="country"
                        class="menu-item
                        <?php if ($panel == "country") { echo "active"; } ?>">
                    Country
                </button>

                <button type="submit" name="panel" value="locality"
                        class="menu-item
                        <?php if ($panel == "locality") { echo "active"; } ?>">
                    Locality
                </button>

                <button type="submit" name="panel" value="budget"
                        class="menu-item
                        <?php if ($panel == "budget") { echo "active"; } ?>">
                    Budget
                </button>

                <button type="submit" name="panel" value="room"
                        class="menu-item
                        <?php if ($panel == "room") { echo "active"; } ?>">
                    Room Type
                </button>

            </div>


            <!-- RIGHT SIDE : only the chosen tab is printed -->

            <div class="modal-panel">


                <!-- ==================================================
                     COUNTRY TAB
                ================================================== -->

                <?php if ($panel == "country") { ?>

                    <h3>Country</h3>

                    <label class="option-row">

                        <input type="radio" name="country" value=""
                            <?php if ($country == "") { echo "checked"; } ?>>

                        Any Country

                    </label>

                    <?php while ($row = mysqli_fetch_assoc($country_result)) { ?>

                        <label class="option-row">

                            <input type="radio" name="country"
                                   value="<?php echo htmlspecialchars($row["Country"]); ?>"
                                <?php if ($country == $row["Country"]) { echo "checked"; } ?>>

                            <?php echo htmlspecialchars($row["Country"]); ?>

                        </label>

                    <?php } ?>

                <?php } ?>


                <!-- ==================================================
                     LOCALITY TAB
                ================================================== -->

                <?php if ($panel == "locality") { ?>

                    <h3>Locality</h3>

                    <label class="option-row">

                        <input type="radio" name="locality" value=""
                            <?php if ($locality == "") { echo "checked"; } ?>>

                        Any Locality

                    </label>

                    <?php foreach ($locality_list as $one_locality) { ?>

                        <label class="option-row">

                            <input type="radio" name="locality"
                                   value="<?php echo htmlspecialchars($one_locality); ?>"
                                <?php if ($locality == $one_locality) { echo "checked"; } ?>>

                            <?php echo htmlspecialchars($one_locality); ?>

                        </label>

                    <?php } ?>

                <?php } ?>


                <!-- ==================================================
                     BUDGET TAB
                ================================================== -->

                <?php if ($panel == "budget") { ?>

                    <h3>Budget (per month)</h3>


                    <!-- Currency comes first, because different
                         listings use different currencies.
                         12000 BDT and 620 GBP cannot be compared. -->

                    <div class="pill-title">Currency</div>

                    <label class="option-row">

                        <input type="radio" name="currency" value=""
                            <?php if ($currency == "") { echo "checked"; } ?>>

                        Any Currency

                    </label>
                    <?php foreach ($currency_list as $one_currency) { ?>

                        <label class="option-row">

                            <input type="radio" name="currency"
                                   value="<?php echo htmlspecialchars($one_currency); ?>"
                                <?php if ($currency == $one_currency) { echo "checked"; } ?>>

                            <?php echo htmlspecialchars($one_currency); ?>

                        </label>

                    <?php } ?>


                    <div class="pill-title">Price Range</div>

                    <div class="price-row">

                        <div class="price-field">

                            <label>Minimum</label>

                            <input class="price-input" type="number"
                                   name="min_price" placeholder="0"
                                   value="<?php echo htmlspecialchars($min_price); ?>">

                        </div>


                        <div class="price-field">

                            <label>Maximum</label>

                            <input class="price-input" type="number"
                                   name="max_price" placeholder="20000"
                                   value="<?php echo htmlspecialchars($max_price); ?>">

                        </div>

                    </div>

                <?php } ?>


                <!-- ==================================================
                     ROOM TYPE TAB
                     ===== THE PILLS AND ICONS ARE WRITTEN HERE =====
                ================================================== -->

                <?php if ($panel == "room") { ?>

                    <h3>Room Type</h3>

                    <div class="pill-group">


                        <!-- SINGLE ROOM -->

                        <label class="pill">

                            <input type="checkbox" name="roomtype[]"
                                   value="Single Room"
                                <?php if (in_array("Single Room", $roomtype)) { echo "checked"; } ?>>

                            <span class="pill-box">

                                <!-- one bed icon -->
                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 19v-6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6"/>
                                    <path d="M8 11V8h8v3"/>
                                </svg>

                                Single Room

                            </span>

                        </label>


                        <!-- SHARED ROOM -->

                        <label class="pill">

                            <input type="checkbox" name="roomtype[]"
                                   value="Shared Room"
                                <?php if (in_array("Shared Room", $roomtype)) { echo "checked"; } ?>>

                            <span class="pill-box">

                                <!-- two people icon -->
                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="9" cy="8" r="3"/>
                                    <path d="M3 20v-1a6 6 0 0 1 12 0v1"/>
                                    <path d="M16 5.5a3 3 0 0 1 0 5"/>
                                    <path d="M17 14a6 6 0 0 1 4 5v1"/>
                                </svg>

                                Shared Room

                            </span>

                        </label>


                        <!-- STUDIO -->

                        <label class="pill">

                            <input type="checkbox" name="roomtype[]"
                                   value="Studio"
                                <?php if (in_array("Studio", $roomtype)) { echo "checked"; } ?>>

                            <span class="pill-box">

                                <!-- small house icon -->
                                <svg viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 10 L12 4 L20 10 V20 H4 Z"/>
                                </svg>

                                Studio

                            </span>

                        </label>


                    </div>

                <?php } ?>


            </div>


        </div>


        <!-- ================= FOOT ================= -->

        <div class="modal-foot">

            <!-- Clear All = a plain link with no filter values,
                 but it keeps the view and reopens the box -->

            <a class="clear-link"
               href="dashboard.php?oldview=<?php echo htmlspecialchars($view); ?>&panel=country">
                Clear All
            </a>


            <!-- Show Results = close the box, keep the filters -->

            <button class="show-button" type="submit"
                    name="panel" value="">
                Show Results
            </button>

        </div>


    </div>


</div>


<?php } ?>


</form>