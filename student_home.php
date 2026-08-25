<?php

session_start();

// Kick out anyone who isn't a logged-in student
if (!isset($_SESSION["loggedIn"]) || $_SESSION["userType"] != "student") {
    header("Location: project.html");
    exit();
}

include "DBconnect.php";

$sql = "SELECT * FROM rooms WHERE available = 1 ORDER BY created_at DESC";
$result = $conn->query($sql);
$roomCount = $result ? $result->num_rows : 0;

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Global Nest - Available Rooms</title>

<style>

    :root {

        /* ===== ACTIVE PALETTE: Navy & White Classic ===== */
        --color-primary: #000080;
        --color-primary-dark: #000066;
        --color-bg: #f5f5f5;
        --color-card-bg: #ffffff;
        --color-text: #222222;
        --color-muted: #666666;
        --color-accent: #b5b5b5;

        /* ============================================================
           ALTERNATE PALETTES
           To switch the whole page's look, copy one of the blocks
           below and paste it OVER the :root block above.
           ============================================================

        --- Teal & Sand Coastal ---
        --color-primary: #0f766e;
        --color-primary-dark: #0b544e;
        --color-bg: #faf3e7;
        --color-card-bg: #ffffff;
        --color-text: #2b2b2b;
        --color-muted: #7a7a7a;
        --color-accent: #f97362;

        --- Slate & Amber Modern ---
        --color-primary: #1e293b;
        --color-primary-dark: #0f172a;
        --color-bg: #f8f8f6;
        --color-card-bg: #ffffff;
        --color-text: #1e1e1e;
        --color-muted: #6b7280;
        --color-accent: #f5a623;

        --- Lavender & Charcoal Soft ---
        --color-primary: #6d5bd0;
        --color-primary-dark: #5747ad;
        --color-bg: #f4f1fb;
        --color-card-bg: #ffffff;
        --color-text: #2e2e2e;
        --color-muted: #7d7d7d;
        --color-accent: #b9aef0;

        */
    }

    * {
        box-sizing: border-box;
    }

    body {
    font-family: Arial, sans-serif;
    margin: 0;

    background:
        linear-gradient(
            rgba(245, 245, 245, 0.85),
            rgba(245, 245, 245, 0.85)
        ),
        url("images/login-bg.png");

    background-size: cover;
    background-position: center;
    background-attachment: fixed;

    color: #222;
}

    /* HEADER (same structure as project.html so it feels like one site) */
    .header {
        width: 100%;
        height: 75px;
        background-color: var(--color-primary);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-left: 40px;
        padding-right: 40px;
        box-sizing: border-box;
    }

    .logo {
        color: white;
        font-size: 24px;
    }

    .logo strong {
        font-weight: bold;
    }

    .navigation {
        display: flex;
        align-items: center;
    }

    .navigation a,
    .navigation span {
        color: white;
        text-decoration: none;
        font-size: 16px;
        margin-left: 25px;
    }

    .navigation a:hover {
        text-decoration: underline;
    }

    /* HOME HERO (mirrors the homepage hero, scaled down) */
    .home-hero {
        width: 100%;
        padding: 60px 40px;
        text-align: center;
        background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
        box-sizing: border-box;
    }

    .home-hero h1 {
        color: white;
        font-size: 38px;
        font-weight: bold;
        margin: 0 0 12px 0;
    }

    .home-hero p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 18px;
        margin: 0;
    }

    .home-hero .room-count {
        font-weight: bold;
        color: white;
    }

    /* CARD GRID */
    .room-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        padding: 35px 40px 50px 40px;
    }

    .room-card {
        background-color: var(--color-card-bg);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .room-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.18);
    }

    .room-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        display: block;
    }

    .room-card-body {
        padding: 16px;
    }

    .room-card-body h3 {
        margin: 0 0 6px 0;
        color: var(--color-primary);
        font-size: 19px;
    }

    .room-city {
        color: var(--color-muted);
        font-size: 14px;
        margin-bottom: 10px;
    }

    .room-price {
        font-size: 20px;
        font-weight: bold;
        color: var(--color-primary);
        margin-bottom: 10px;
    }

    .room-price span {
        font-size: 13px;
        font-weight: normal;
        color: var(--color-muted);
    }

    .room-desc {
        font-size: 14px;
        color: var(--color-text);
        margin-bottom: 14px;
        line-height: 1.4;
    }

    .interest-button {
        width: 100%;
        height: 42px;
        background-color: var(--color-primary);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 15px;
        cursor: pointer;
    }

    .interest-button:hover {
        background-color: var(--color-primary-dark);
    }

    .no-rooms {
        padding: 60px 40px;
        text-align: center;
        color: var(--color-muted);
        font-size: 18px;
    }

</style>
</head>

<body>

<!-- HEADER -->
<div class="header">

    <div class="logo">
        <strong>Global Nest</strong> - Student Room Matcher
    </div>

    <div class="navigation">
        <span>Hi, <?php echo htmlspecialchars($_SESSION["full_name"]); ?></span>
        <a href="logout.php">Logout</a>
    </div>

</div>

<!-- HOME HERO -->
<div class="home-hero">
    <h1>Welcome back, <?php echo htmlspecialchars($_SESSION["full_name"]); ?></h1>
    <p>We found <span class="room-count"><?php echo $roomCount; ?></span> room<?php echo $roomCount == 1 ? "" : "s"; ?> matching students right now.</p>
</div>

<!-- ROOM GRID -->
<?php if ($result && $result->num_rows > 0) { ?>

<div class="room-grid">

    <?php while ($room = $result->fetch_assoc()) { ?>

    <div class="room-card">

        <img src="<?php echo htmlspecialchars($room["image_url"]); ?>"
             alt="<?php echo htmlspecialchars($room["title"]); ?>">

        <div class="room-card-body">

            <h3><?php echo htmlspecialchars($room["title"]); ?></h3>

            <div class="room-city">
                <?php echo htmlspecialchars($room["city"]); ?> &middot; <?php echo htmlspecialchars($room["room_type"]); ?>
            </div>

            <div class="room-price">
                $<?php echo number_format($room["price"], 2); ?> <span>/ month</span>
            </div>

            <div class="room-desc">
                <?php echo htmlspecialchars($room["description"]); ?>
            </div>

            <button class="interest-button" type="button">I'm Interested</button>

        </div>

    </div>

    <?php } ?>

</div>

<?php } else { ?>

<div class="no-rooms">No rooms available right now. Check back soon!</div>

<?php } ?>

</body>
</html>