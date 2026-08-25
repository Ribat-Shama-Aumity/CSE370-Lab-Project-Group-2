<?php

// ============================================================
// FILTER FEATURE - PART 1 : THE PHP WORK
// ------------------------------------------------------------
// Included at the TOP of dashboard.php, after DBconnect.php
// (because it needs $conn).
//
// It does 5 jobs:
//   1. work out which view is on  (grid or list)
//   2. work out which filter tab is open
//   3. read what the student picked
//   4. build the WHERE part of the SQL query
//   5. get the lists shown inside the filter box
// ============================================================


// ------------------------------------------------------------
// JOB 1 : WHICH VIEW - GRID OR LIST
// ------------------------------------------------------------
// "oldview" is a hidden box that always carries the view we
// are already on. "view" only arrives when a view button is
// pressed.
//
// THE RULE: if a new value came, use it.
//           if not, keep the old one.

$oldview = "grid";

if (isset($_GET["oldview"])) {
    $oldview = $_GET["oldview"];
}


$view = $oldview;

if (isset($_GET["view"])) {
    $view = $_GET["view"];
}


// ------------------------------------------------------------
// JOB 2 : WHICH TAB IS OPEN
// ------------------------------------------------------------
// An EMPTY $panel means the filter box is CLOSED.
// Same old / new rule as above.

$oldpanel = "";

if (isset($_GET["oldpanel"])) {
    $oldpanel = $_GET["oldpanel"];
}


$panel = $oldpanel;

if (isset($_GET["panel"])) {
    $panel = $_GET["panel"];
}


// ------------------------------------------------------------
// JOB 3a : READ WHAT THE STUDENT PICKED
// ------------------------------------------------------------
// Everything comes back inside the address bar, so we read
// it with $_GET. We keep the plain value here and clean it
// later, just before it goes into the SQL.


// --- Country (only one can be picked) ---

$country = "";

if (isset($_GET["country"])) {
    $country = $_GET["country"];
}


// --- Locality (only one can be picked) ---

$locality = "";

if (isset($_GET["locality"])) {
    $locality = $_GET["locality"];
}


// --- Currency (only one can be picked) ---

$currency = "";

if (isset($_GET["currency"])) {
    $currency = $_GET["currency"];
}


// --- Lowest price ---

$min_price = "";

if (isset($_GET["min_price"])) {
    $min_price = $_GET["min_price"];
}


// --- Highest price ---

$max_price = "";

if (isset($_GET["max_price"])) {
    $max_price = $_GET["max_price"];
}


// --- Room type (MANY can be picked, so it is an array) ---

$roomtype = array();

if (isset($_GET["roomtype"])) {
    $roomtype = $_GET["roomtype"];
}

// ------------------------------------------------------------
// JOB 3b : WHICH CURRENCIES BELONG TO THE CHOSEN COUNTRY
// ------------------------------------------------------------
// Canada listings are in CAD, Bangladesh in BDT, UK in GBP.
// So when a country is chosen we only show that country's
// currencies. When no country is chosen we show them all.

$currency_sql = "SELECT DISTINCT Currency
                 FROM Listings
                 WHERE Verification_Status = 'Approved' ";


// only add this line when a country was picked

if ($country != "") {

    $safe_country_list =
        mysqli_real_escape_string($conn, $country);

    $currency_sql = $currency_sql .
        " AND Country = '$safe_country_list' ";
}


$currency_sql = $currency_sql . " ORDER BY Currency";

$currency_result = mysqli_query($conn, $currency_sql);


// Put the currencies into a simple array, because we
// need them twice: once to show the list, and once to
// check the student's old choice below.

$currency_list = array();

while ($row = mysqli_fetch_assoc($currency_result)) {

    $currency_list[] = $row["Currency"];
}


// If the student picked BDT and then switched to Canada,
// BDT is no longer in the list, so we forget it.
// Without this line the page would show 0 listings.

if ($currency != "" && !in_array($currency, $currency_list)) {

    $currency = "";
}

// ------------------------------------------------------------
// JOB 3c : WHICH LOCALITIES BELONG TO THE CHOSEN COUNTRY
// ------------------------------------------------------------
// Mohakhali and Badda are in Bangladesh, Downtown is in
// Canada. So when a country is chosen we only show that
// country's localities. Exactly the same idea as the
// currencies above.

$locality_sql = "SELECT DISTINCT Neighbourhood
                 FROM Listings
                 WHERE Verification_Status = 'Approved'
                 AND Neighbourhood IS NOT NULL ";


// only add this line when a country was picked

if ($country != "") {

    $safe_country_loc =
        mysqli_real_escape_string($conn, $country);

    $locality_sql = $locality_sql .
        " AND Country = '$safe_country_loc' ";
}


$locality_sql = $locality_sql . " ORDER BY Neighbourhood";

$locality_result = mysqli_query($conn, $locality_sql);


// Put the localities into a simple array

$locality_list = array();

while ($row = mysqli_fetch_assoc($locality_result)) {

    $locality_list[] = $row["Neighbourhood"];
}


// If the student picked Badda and then switched to Canada,
// Badda is no longer in the list, so we forget it.
// Without this line the page would show 0 listings.

if ($locality != "" && !in_array($locality, $locality_list)) {

    $locality = "";
}

// ------------------------------------------------------------
// JOB 4 : BUILD THE WHERE PART OF THE QUERY
// ------------------------------------------------------------
// We start with the Approved rule, then glue on one extra
// AND for every filter the student actually used.
//
// mysqli_real_escape_string() puts a \ in front of any quote
// the student typed, so they cannot break our SQL.
// That attack is called SQL injection.

$filter = " WHERE Verification_Status = 'Approved' ";


// --- Country ---

if ($country != "") {

    $safe_country = mysqli_real_escape_string($conn, $country);

    $filter = $filter . " AND Country = '$safe_country' ";
}


// --- Locality ---

if ($locality != "") {

    $safe_locality = mysqli_real_escape_string($conn, $locality);

    $filter = $filter . " AND Neighbourhood = '$safe_locality' ";
}


// --- Currency ---

if ($currency != "") {

    $safe_currency = mysqli_real_escape_string($conn, $currency);

    $filter = $filter . " AND Currency = '$safe_currency' ";
}


// --- Lowest price ---
// is_numeric() makes sure it really is a number

if ($min_price != "" && is_numeric($min_price)) {

    $filter = $filter . " AND Price >= '$min_price' ";
}


// --- Highest price ---

if ($max_price != "" && is_numeric($max_price)) {

    $filter = $filter . " AND Price <= '$max_price' ";
}


// --- Room type (many values) ---
// We clean every value one by one, then implode() glues
// them together with ',' in between, so the SQL reads:
//     AND RoomType IN ('Studio','Single Room')

if (count($roomtype) > 0) {

    $safe_rooms = array();

    foreach ($roomtype as $one_room) {

        $safe_rooms[] =
            mysqli_real_escape_string($conn, $one_room);
    }

    $roomtype_text = implode("','", $safe_rooms);

    $filter = $filter . " AND RoomType IN ('$roomtype_text') ";
}


// ------------------------------------------------------------
// JOB 5 : THE LISTS SHOWN INSIDE THE FILTER BOX
// ------------------------------------------------------------
// DISTINCT means "show each value only one time".
// We read the options from the database, so a new listing
// automatically adds its country to the filter list.

$country_sql = "SELECT DISTINCT Country
                FROM Listings
                WHERE Verification_Status = 'Approved'
                AND Country IS NOT NULL
                ORDER BY Country";

$country_result = mysqli_query($conn, $country_sql);






// ------------------------------------------------------------
// EXTRA : THE CLASS NAME FOR THE BOX AROUND THE CARDS
// ------------------------------------------------------------
// dashboard.php just prints this variable, so it does not
// need any if-else of its own.

$view_class = "room-grid";

if ($view == "list") {
    $view_class = "room-list";
}

?>