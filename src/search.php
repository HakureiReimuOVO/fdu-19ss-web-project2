<?php
session_start();
require_once("config.php");
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>Search</title>
    <link type="text/css" rel="styleSheet" href="CSS/reset.css">
    <link type="text/css" rel="stylesheet" href="CSS/search.css">
</head>
<body>
<header>
    <!--导航栏部分-->
    <a class="navigation" href="../index.php">HOME</a>
    <a class="navigation" href="./browser.php">BROWSE</a>
    <a class="navigation" href="./search.php" id="active">SEARCH</a>
    <div class="dropdown">
        <!--下拉菜单部分-->
        <?php
        if (isset($_GET['logout'])) unset($_SESSION['UID']);
        if (isset($_SESSION['UID'])) echo '
        <span class="dropdown-button">My Account</span>
        <div class="dropdown-content">
            <div class="dropdown-row">
                <img src="../img/necessary-images/upload.png" height="20" width="20">
                <a href="./upload.php">Upload</a>
            </div>
            <div class="dropdown-row"><img src="../img/necessary-images/my_photo.png" height="20" width="20">
                <a href="./my_photo.php?page=1">My photo</a>
            </div>
            <div class="dropdown-row"><img src="../img/necessary-images/my_favor.png" height="20" width="20">
                <a href="./my_favor.php?page=1">My favor</a>
            </div>
            <div class="dropdown-row"><img src="../img/necessary-images/logout.png" height="20" width="20">
                <a href="?logout">Log out</a>
            </div>
        </div>
        '; else echo '<a href="./login.php"><span>Log in</span></a>';
        ?>
    </div>
</header>
<div id="search_section">
    <!--搜索栏部分-->
    <form name="filter" method="get" action="">
        <p>Search</p>
        <div id="search_title">
            <input type="radio" value="title" name="filter_type" required>Filter by Title<br>
            <input type="text" name="title_content">
        </div>
        <div id="search_description">
            <input type="radio" value="description" name="filter_type" required>Filter by Description<br>
            <textarea name="description_content"></textarea>
        </div>
        <input name="page" value="1" hidden>
        <input type="submit" value="Filter">
    </form>
</div>
<section>
    <!--搜索结果部分-->
    <p>Result</p>
    <?php
    $pdo = new PDO(DBCTSTRING, DBUSER, DBPASS);
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
        if ($_GET['filter_type'] == 'title') {
            $content = $_GET['title_content'];
            $ifTitle = true;
        } else {
            $content = $_GET['description_content'];
            $ifTitle = false;
        }
        $sql = 'SELECT * FROM travelimage WHERE ' . (($ifTitle) ? 'Title' : 'Description') . ' LIKE "%' . $content . '%"';
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
        </div>
        </div>';
            echo '<div id="page">';
            $href = '?filter_type=' . $_GET['filter_type'] . (($ifTitle) ? '&title_content=' . $_GET['title_content'] : '&description_content=' . $_GET['description_content']);
            if ($_GET['page'] != 1) echo ' <a href="' . $href . '&page=' . ($_GET['page'] - 1) . '"><</a>';
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
            for ($i = $startPage; $i <= $endPage; $i++) echo ' <a ' . ($_GET['page'] == $i ? 'id="current"' : '') . 'href="' . $href . '&page=' . $i . '">' . $i . '</a>';
            if ($endPage < (int)$pageSize) echo '...';
            if ($_GET['page'] != (int)$pageSize) echo ' <a href="' . $href . '&page=' . ($_GET['page'] + 1) . '">></a>';
            echo '</div>';
        } else echo '<h2>No photo is found.</h2>';
    } else echo '<h2>You haven\'t searched anything yet.</h2>';
    ?>
</section>
<!--页脚部分-->
<footer>Copyright © 2020 QQD All Rights Reserved</footer>
<script>
    let radio_one = document.getElementsByName("filter_type").item(0);
    let radio_two = document.getElementsByName("filter_type").item(1);
    let title = document.getElementsByName("title_content").item(0);
    let description = document.getElementsByName("description_content").item(0);
    function checkValidity() {
        if (radio_one.checked === true) {
            title.required = true;
            description.required = false;
        } else {
            title.required = false;
            description.required = true;
        }
    }
    radio_one.onclick = checkValidity;
    radio_two.onclick = checkValidity;
</script>
</body>
</html>