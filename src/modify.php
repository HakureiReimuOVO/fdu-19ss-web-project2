<?php
session_start();
require_once("config.php");
require_once("compress.php");
if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    if ($_POST['content'] != '0' && $_POST['country'] != '0' && $_POST['city'] != '0') {
        if ($_FILES['photo']['name'] != '') {
            if (($_FILES['photo']['type'] == 'image/gif') || ($_FILES['photo']['type'] == 'image/jpeg') || ($_FILES['photo']['type'] == 'image/png')) {
                $pdo = new PDO(DBCTSTRING, DBUSER, DBPASS);
                $oldPath = $pdo->query('SELECT PATH FROM travelimage WHERE ImageID = ' . $_GET['id'])->fetch()['PATH'];
                unlink($_SERVER['DOCUMENT_ROOT'] . '\\img\\travel-images\\normal\\medium\\' . $oldPath);
                unlink($_SERVER['DOCUMENT_ROOT'] . '\\img\\travel-images\\normal\\small\\' . $oldPath);
                unlink($_SERVER['DOCUMENT_ROOT'] . '\\img\\travel-images\\normal\\tiny\\' . $oldPath);
                $path = time() . '.jpg';
                (new compress($_FILES['photo']['tmp_name'], 1))->compressImg($_SERVER['DOCUMENT_ROOT'] . '\\img\\travel-images\\normal\\medium\\' . $path);
                (new compress($_FILES['photo']['tmp_name'], 0.5))->compressImg($_SERVER['DOCUMENT_ROOT'] . '\\img\\travel-images\\normal\\small\\' . $path);
                (new compress($_FILES['photo']['tmp_name'], 0.25))->compressImg($_SERVER['DOCUMENT_ROOT'] . '\\img\\travel-images\\normal\\tiny\\' . $path);
                $city = $pdo->query('SELECT Latitude, Longitude, GeoNameID FROM geocities WHERE AsciiName = "' . $_POST['city'] . '"')->fetch();
                $country = $pdo->query('SELECT ISO FROM geocountries_regions WHERE Country_RegionName = "' . $_POST['country'] . '"')->fetch();
                $imageID = $pdo->query('SELECT ImageID FROM travelimage order by ImageID DESC LIMIT 1')->fetch()['ImageID'] + 1;
                $pdo->query('UPDATE travelimage SET Title="' . $_POST['title'] . '",Description="' . $_POST['description'] . '",Latitude=' . $city['Latitude'] . ',Longitude=' . $city['Longitude'] . '
                    ,CityCode=' . $city['GeoNameID'] . ',Country_RegionCodeISO="' . $country['ISO'] . '",PATH="' . $path . '",Content="' . $_POST['content'] . '"WHERE ImageID=' . $_GET['id']);
                header('location: ./my_photo.php?page=1');
            } else echo '
    <script>
    alert("Please upload the photo with the correct file type.");
    </script>
    ';
        } else {
            $pdo = new PDO(DBCTSTRING, DBUSER, DBPASS);
            $city = $pdo->query('SELECT Latitude, Longitude, GeoNameID FROM geocities WHERE AsciiName = "' . $_POST['city'] . '"')->fetch();
            $country = $pdo->query('SELECT ISO FROM geocountries_regions WHERE Country_RegionName = "' . $_POST['country'] . '"')->fetch();
            $imageID = $pdo->query('SELECT ImageID FROM travelimage order by ImageID DESC LIMIT 1')->fetch()['ImageID'] + 1;
            $pdo->query('UPDATE travelimage SET Title="' . $_POST['title'] . '",Description="' . $_POST['description'] . '",Latitude=' . $city['Latitude'] . ',Longitude=' . $city['Longitude'] . '
                    ,CityCode=' . $city['GeoNameID'] . ',Country_RegionCodeISO="' . $country['ISO'] . '",Content="' . $_POST['content'] . '"WHERE ImageID=' . $_GET['id']);
            header('location: ./my_photo.php?page=1');
        }
    } else echo '
    <script>
    alert("Please select your shooting details.");
    </script>
    ';
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>Modify</title>
    <link type="text/css" rel="styleSheet" href="CSS/reset.css">
    <link type="text/css" rel="styleSheet" href="CSS/upload.css">
    <script>
        //显示上传的图片
        function uploadImage(file, preview) {
            var div = document.getElementById(preview);
            if (file.files && file.files[0]) {
                div.innerHTML = '<img id="Upload-Image">';
                var img = document.getElementById("Upload-Image");
                img.onload = function () {
                    img.style.width = "50%";
                };
                var reader = new FileReader();
                reader.onload = function (evt) {
                    img.src = evt.target.result;
                };
                reader.readAsDataURL(file.files[0]);
            }
        }
    </script>
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
                <a href="my_photo.php?page=1" id="active">My photo</a>
            </div>
            <div class="dropdown-row"><img src="../img/necessary-images/my_favor.png" height="20" width="20">
                <a href="my_favor.php?page=1">My favor</a>
            </div>
            <div class="dropdown-row"><img src="../img/necessary-images/logout.png" height="20" width="20">
                <a href="?logout">Log out</a>
            </div>
        </div>
    </div>
</header>
<section>
    <!--上传照片部分-->
    <?php
    $pdo = new PDO(DBCTSTRING, DBUSER, DBPASS);
    $image = $pdo->query('SELECT * FROM travelimage WHERE ImageID = ' . $_GET['id'])->fetch();
    echo '
        <p>Modify</p>
        <form method="post" action="" enctype="multipart/form-data">
        <div id="picture_upload">
            <div id="preview"><img width="50%" src="../img/travel-images/normal/medium/' . $image['PATH'] . '"></div>
            <p class="upload-container">
                <input type="file" name="photo" onchange=uploadImage(this,"preview");>Choose Photo
            </p>
        </div>
        <div id="picture_detail">
            <p>Photo Title</p>
            <input type="text" name="title" value="' . $image['Title'] . '" required>
            <p>Photo Description</p>
            <textarea name="description">' . (isset($image['Description']) ? $image['Description'] : '') . '</textarea>
            <p>Photo Details</p>
            <select name="content">
                <option value="0">Shooting Content</option>
                <option value="Scenery">Scenery</option>
                <option value="City">City</option>
                <option value="People">People</option>
                <option value="Animal">Animal</option>
                <option value="Building">Building</option>
                <option value="Wonder">Wonder</option>
                <option value="Other">Other</option>
            </select>
            <select name="country" onchange="selectCityByCountry(this,this.form.city);">
                <option value="0">Shooting Country</option>
            </select>
            <select name="city">
                <option value="0">Shooting City</option>
            </select>
            <br><input type="submit" value="Modify">
        </div>
    </form>';
    ?>
</section>
<!--页脚部分-->
<footer>Copyright © 2020 QQD All Rights Reserved</footer>
<script>
    cities = {};
    <?php
    $pdo = new PDO(DBCTSTRING, DBUSER, DBPASS);
    $sql = 'SELECT Country_RegionName,ISO FROM geocountries_regions ORDER BY Population DESC LIMIT 30';
    $country = $pdo->query($sql);
    while ($row = $country->fetch()) {
        $sql = 'SELECT AsciiName FROM geocities WHERE Country_RegionCodeISO = "' . $row['ISO'] . '"ORDER BY Population DESC LIMIT 30';
        $city = $pdo->query($sql)->fetchAll();
        $cities = '[';
        foreach ($city as $key => $value) {
            $cities .= '"' . $value['AsciiName'] . '",';
        }
        $cities = substr($cities, 0, -1) . ']';
        echo 'cities' . '["' . $row['Country_RegionName'] . '"] = ' . $cities . ';';
    }
    $image = $pdo->query('SELECT * FROM travelimage WHERE ImageID = ' . $_GET['id'])->fetch();
    $content = $image['Content'];
    $city = $pdo->query('SELECT AsciiName FROM geocities WHERE GeoNameID = ' . $image['CityCode'])->fetch()['AsciiName'];
    $country = $pdo->query('SELECT Country_RegionName FROM geocountries_regions WHERE ISO = "' . $image['Country_RegionCodeISO'] . '"')->fetch()['Country_RegionName'];
    echo 'var content = "' . $content . '";';
    echo 'var city = "' . $city . '";';
    echo 'var country = "' . $country . '";';
    ?>
    var contentSelect = document.getElementsByName("content").item(0);
    var citySelect = document.getElementsByName("city").item(0);
    var countrySelect = document.getElementsByName("country").item(0);
    for (let key in cities) {
        let option = document.createElement("Option");
        option.value = key;
        option.innerText = key;
        countrySelect.appendChild(option);
    }
    for (let i = 0; i < contentSelect.options.length; i++)
        if (contentSelect.options[i].value === content) contentSelect.options[i].selected = true;
    for (let i = 0; i < countrySelect.options.length; i++)
        if (countrySelect.options[i].value === country) countrySelect.options[i].selected = true;
    selectCityByCountry(countrySelect, citySelect);
    for (let i = 0; i < citySelect.options.length; i++)
        if (citySelect.options[i].value === city) citySelect.options[i].selected = true;

    function selectCityByCountry(country, city) {
        var cty;
        cty = country.value;
        for (var i = 1; i < city.options.length; i++) {
            city.removeChild(city.options[i]);
            city.options.length = 1;
        }
        if (cty == "0") return;
        for (var i = 0; i < cities[cty].length; i++) {
            city.options[i + 1] = new Option();
            city.options[i + 1].text = cities[cty][i];
            city.options[i + 1].value = cities[cty][i];
        }
    }
</script>
</body>
</html>
