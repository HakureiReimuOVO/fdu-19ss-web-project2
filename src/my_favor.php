<?php
session_start();
require_once("config.php");
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>My Favor</title>
    <link type="text/css" rel="styleSheet" href="CSS/reset.css">
    <link type="text/css" rel="styleSheet" href="CSS/my_favor.css">
</head>
<body>
<header>
    <!--导航栏部分-->
    <a class="navigation" href="../index.php">HOME</a>
    <a class="navigation" href="browser.php">BROWSE</a>
    <a class="navigation" href="search.php">SEARCH</a>
    <div class="dropdown">
        <!--下拉菜单部分-->
        <?php
        if (isset($_GET['logout'])) {
            unset($_SESSION['UID']);
            header("location: ../index.php");
        }
        ?>
        <span class="dropdown-button">My Account</span>
        <div class="dropdown-content">
            <div class="dropdown-row">
                <img src="../img/necessary-images/upload.png" height="20" width="20">
                <a href="upload.php">Upload</a>
            </div>
            <div class="dropdown-row"><img src="../img/necessary-images/my_photo.png" height="20" width="20">
                <a href="my_photo.php?page=1">My photo</a>
            </div>
            <div class="dropdown-row"><img src="../img/necessary-images/my_favor.png" height="20" width="20">
                <a href="my_favor.php?page=1" id="active">My favor</a>
            </div>
            <div class="dropdown-row"><img src="../img/necessary-images/logout.png" height="20" width="20">
                <a href="?logout">Log out</a>
            </div>
        </div>
    </div>
</header>
<section>
    <!--收藏照片部分-->
    <p>My favor</p>
    <?php
    $pdo = new PDO(DBCTSTRING, DBUSER, DBPASS);
    if ($_SERVER["REQUEST_METHOD"] == "POST")
        $deleteFavor = $pdo->query('DELETE FROM travelimagefavor WHERE UID = ' . $_SESSION['UID'] . ' AND ImageID = ' . $_POST['id']);
    $page = $_GET['page'];
    $sql = 'SELECT * FROM travelimage LEFT JOIN (SELECT ImageID FROM travelimagefavor WHERE UID = ' . $_SESSION['UID'] . ') AS tb ON tb.ImageID = travelimage.ImageID WHERE travelimage.ImageID = tb.ImageID';
    $result = $pdo->query($sql);
    if ($result->rowCount() > 0) {
        $pageSize = ($result->rowCount() - 1) / 4 + 1;
        $row = $result->fetchAll();
        for ($i = $page * 4 - 4; $i < $page * 4; $i++) if (isset($row[$i])) echo
        '<div class="photo_section">
        <a href="details.php?id=' . $row[$i]['ImageID'] . '"' . ' class="photo_picture">
            <div class="img-container">
                <img src="../img/travel-images/normal/small/' . $row[$i]['PATH'] . '">
            </div>
        </a>
        <div class="photo_detail">
            <p class="picture-title">' . $row[$i]['Title'] . '</p>
            <p class="picture-description">' . ($row[$i]['Description'] == "" ? 'No Description' : $row[$i]['Description']) . '</p>
            <form method="post" action="' . ($result->rowCount() % 4 == 1 && $result->rowCount() != 1 && $page != 1 ? '?page=' . ($page - 1) : '') . '">
                <input type="submit" value="Delete">
                <input name="id" value="' . $row[$i]['ImageID'] . '" hidden>
            </form>
        </div>
        </div>';
        echo '<div id="page">';
        if ($_GET['page'] != 1) echo ' <a href="?page=' . ($_GET['page'] - 1) . '"><</a>';
        if ((int)$pageSize > 5) {
            $extraStartPage = $_GET['page'] - (int)($pageSize) + 2;
            $extraEndPage = 3 - $_GET['page'];
            $endPage = ($extraStartPage > 0) ? (int)($pageSize) : $_GET['page'] + 2;
            $startPage = ($extraEndPage > 0) ? 1 : ($_GET['page'] - 2);
            $endPage += ($extraEndPage > 0) ? $extraEndPage : 0;
            $startPage -= ($extraStartPage > 0) ? $extraStartPage : 0;
        } else {
            $startPage = 1;
            $endPage = (int)$pageSize;
        }
        if ($startPage > 1) echo '...';
        for ($i = $startPage; $i <= $endPage; $i++) echo ' <a ' . ($_GET['page'] == $i ? 'id="current"' : '') . 'href="?page=' . $i . '">' . $i . '</a>';
        if ($endPage < (int)$pageSize) echo '...';
        if ($_GET['page'] != (int)$pageSize) echo ' <a href="?page=' . ($_GET['page'] + 1) . '">></a>';
        echo '</div>';
    } else echo '<h2>You haven\'t favored any picture.</h2>';
    ?>
</section>
<!--页脚部分-->
<footer>Copyright © 2020 QQD All Rights Reserved</footer>
</body>
</html>