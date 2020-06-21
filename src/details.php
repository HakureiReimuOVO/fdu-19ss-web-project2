<?php
session_start();
require_once("config.php");
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>Details</title>
    <link type="text/css" rel="styleSheet" href="CSS/reset.css">
    <link type="text/css" rel="stylesheet" href="CSS/details.css">
</head>
<body>
<header>
    <!--导航栏部分-->
    <a class="navigation" href="../index.php">HOME</a>
    <a class="navigation" href="./browser.php">BROWSE</a>
    <a class="navigation" href="./search.php">SEARCH</a>
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
                <a href="?logout&id='.$_GET['id'].'">Log out</a>
            </div>
        </div>
        '; else echo '<a href="./login.php"><span>Log in</span></a>';
        ?>
    </div>
</header>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pdo = new PDO(DBCTSTRING, DBUSER, DBPASS);
    if (!isset($_SESSION['UID'])) header("location: ./login.php?login");
    else if ($_POST['favor'] == "FAVOR") {
        $newFavor = $pdo->prepare('INSERT INTO travelimagefavor ( FavorID , UID , ImageID ) VALUES ( :favor , :uid , :image )');
        $newFavor->bindValue(':favor', $pdo->query('SELECT FavorID FROM travelimagefavor order by FavorID DESC LIMIT 1')->fetch()['FavorID'] + 1);
        $newFavor->bindValue(':uid', $_SESSION['UID']);
        $newFavor->bindValue(':image', $_GET['id']);
        $newFavor->execute();
    } else $deleteFavor = $pdo->query('DELETE FROM travelimagefavor WHERE UID = ' . $_SESSION['UID'] . ' AND ImageID = ' . $_GET['id']);
}
?>
<section>
    <!--图片详细信息部分-->
    <p>Details</p>
    <?php
    $pdo = new PDO(DBCTSTRING, DBUSER, DBPASS);
    $picture = $pdo->query('SELECT * FROM travelimage WHERE ImageID = ' . $_GET['id'])->fetch();
    $city = ($picture['CityCode'] == '') ? 'Unknown' : $pdo->query('SELECT AsciiName FROM geocities WHERE GeoNameID = ' . $picture['CityCode'])->fetch()['AsciiName'];
    $country = ($picture['Country_RegionCodeISO'] == '') ? 'Unknown' : $pdo->query('SELECT Country_RegionName FROM geocountries_regions WHERE ISO = "' . $picture['Country_RegionCodeISO'] . '"')->fetch()['Country_RegionName'];
    $uploader = $pdo->query('SELECT UserName FROM traveluser WHERE UID = ' . $picture['UID'])->fetch();
    $likeNumber = $pdo->query('SELECT COUNT(1) FROM travelimagefavor WHERE ImageID = ' . $_GET['id'])->fetch();
    echo '
    <div id="content">
        <div id="content_picture">
            <!--图片部分-->
            <img src="../img/travel-images/normal/medium/' . $picture['PATH'] . '">
        </div>
        <div id="content_aside">
            <!--图片信息部分-->
            <div id="title">' . strtoupper($picture['Title']) . '</div>
            <div id="creator">Uploaded by ' . $uploader['UserName'] . '</div>
            <div id="like_number">
                <p class="title">Like Number</p>
                <span>' . $likeNumber['COUNT(1)'] . '</span>
            </div>
            <div id="image_details">
                <p class="title">Photo Details</p>
                <ul>
                    <li>Content: ' . $picture['Content'] . '</li>
                    <li>Country: ' . $country . '</li>
                    <li>City: ' . $city . '</li>
                </ul>
            </div>
            <form action="" method="post">';
    if (isset($_SESSION['UID'])) {
        $count = $pdo->query('SELECT * FROM travelimagefavor WHERE UID = ' . $_SESSION['UID'] . ' AND ImageID = ' . $_GET['id'])->rowCount();
        if ($count == 0) echo '<input type="submit" name="favor" value="FAVOR" id="favor">';
        else echo '<input type="submit" name="favor" value="FAVORED" id="favored">';
    } else echo '<input type="submit" name="favor" value="FAVOR" id="favor">';
    echo '</form>
        </div>
    </div>
    <div id="content_bottom">' . $picture['Description'] . '
    </div>';
    ?>
</section>
<!--页脚部分-->
<footer>Copyright © 2020 QQD All Rights Reserved</footer>
</body>
</html>