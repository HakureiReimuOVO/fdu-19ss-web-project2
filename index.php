<?php
session_start();
require_once("./src/config.php");
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>Index</title>
    <link type="text/css" rel="styleSheet" href="src/CSS/reset.css">
    <link type="text/css" rel="styleSheet" href="src/CSS/index.css">
</head>
<body>
<header>
    <!--导航栏部分-->
    <a class="navigation" href="index.php" id="active">HOME</a>
    <a class="navigation" href="src/browser.php">BROWSE</a>
    <a class="navigation" href="src/search.php">SEARCH</a>
    <div class="dropdown">
        <!--下拉菜单部分-->
        <?php
        if (isset($_GET['logout'])) unset($_SESSION['UID']);
        if (isset($_SESSION['UID'])) echo '
        <span class="dropdown-button">My Account</span>
        <div class="dropdown-content">
            <div class="dropdown-row">
                <img src="img/necessary-images/upload.png" height="20" width="20">
                <a href="src/upload.php">Upload</a>
            </div>
            <div class="dropdown-row"><img src="img/necessary-images/my_photo.png" height="20" width="20">
                <a href="src/my_photo.php?page=1">My photo</a>
            </div>
            <div class="dropdown-row"><img src="img/necessary-images/my_favor.png" height="20" width="20">
                <a href="src/my_favor.php?page=1">My favor</a>
            </div>
            <div class="dropdown-row"><img src="img/necessary-images/logout.png" height="20" width="20">
                <a href="?logout">Log out</a>
            </div>
        </div>
        '; else echo '<a href="./src/login.php"><span>Log in</span></a>';
        ?>
    </div>
</header>
<!--头图部分-->
<div id="picture-highlight"></div>
<section>
    <!--首页展示图部分-->
    <?php
    function displayPicture($row)
    {
        echo '<div class="picture">
        <a href="src/details.php?id=' . $row['ImageID'] . '">
            <div class="img-container">
                <img src="./img/travel-images/normal/medium/' . $row['PATH'] . '">
            </div>
        </a>
        <p class="title">' . strtoupper($row['Title']) . '</p>
        <p class="description">' . ($row['Description'] == "" ? 'No Description' : $row['Description']) . '</p>
    </div>';
    }

    if (isset($_GET['random'])) {
        $pdo = new PDO(DBCTSTRING, DBUSER, DBPASS);
        $popular = $pdo->query('SELECT * FROM travelimage ORDER BY RAND() LIMIT 6');
        for ($i = 0; $i < 6; $i++) displayPicture($popular->fetch());
    } else {
        $pdo = new PDO(DBCTSTRING, DBUSER, DBPASS);
        $popular = $pdo->query('SELECT * FROM travelimage LEFT JOIN (SELECT COUNT(1),ImageID AS ID FROM travelimagefavor GROUP BY ID) AS tb ON tb.ID = travelimage.ImageID ORDER BY `COUNT(1)` DESC LIMIT 6');
        for ($i = 0; $i < 6; $i++) displayPicture($popular->fetch());
    }
    ?>
</section>
<footer>
    <!--页脚部分-->
    <div id="Upper-Footer">
        <div id="links">
            <a href="#">About Us</a>
            <a href="#">Contact Us</a>
            <a href="#">Terms Of Use</a>
            <a href="#">Privacy policy</a>
        </div>
        <div id="code"><img src="img/necessary-images/code.jpg" height="100" width="100"></div>
    </div>
    <div id="Lower-Footer">
        Copyright © 2020 QQD All Rights Reserved<br>
        NO.19302010009
    </div>
</footer>
<!--辅助按钮部分-->
<a id="return-button" href="#picture-highlight"><img src="img/necessary-images/return.png" height="50" width="50"></a>
<a id="refresh-button" href="?random" )"><img src="img/necessary-images/refresh.png" height="50" width="50"/></a>
</body>
</html>