<?php
session_start();
require_once("config.php");
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>Browser</title>
    <link type="text/css" rel="styleSheet" href="CSS/reset.css">
    <link type="text/css" rel="styleSheet" href="CSS/browser.css">
</head>
<body>
<header>
    <!--导航栏部分-->
    <a class="navigation" href="../index.php">HOME</a>
    <a class="navigation" href="./browser.php" id="active">BROWSE</a>
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
                <a href="?logout">Log out</a>
            </div>
        </div>
        '; else echo '<a href="./login.php"><span>Log in</span></a>';
        ?>
    </div>
</header>
<aside>
    <!--侧边筛选栏部分-->
    <div id="search">
        <p class="title_aside">Search by title</p>
        <form method="get">
            <input name="title" type="text">
            <input name="page" value="1" hidden>
            <p id="search-button"><img src="../img/necessary-images/search.png" height="20" width="20"></p>
        </form>
    </div>
    <div id="popular_content">
        <p class="title_aside">Popular content</p>
        <ul>
            <?php
            $pdo = new PDO(DBCTSTRING, DBUSER, DBPASS);
            $popularContent = $pdo->query('SELECT Content, count(*) AS count FROM travelimage GROUP BY Content ORDER BY count DESC LIMIT 4')->fetchAll();
            $popularCity = $pdo->query('SELECT CityCode, count(CityCode) AS count FROM travelimage GROUP BY CityCode ORDER BY count DESC LIMIT 4')->fetchAll();
            $popularCountry = $pdo->query('SELECT Country_RegionCodeISO, count(*) AS count FROM travelimage GROUP BY Country_RegionCodeISO ORDER BY count DESC LIMIT 4')->fetchAll();
            for ($i = 0; $i < 4; $i++)
                if (isset($popularContent[$i]))
                    echo '<li><a href="?content=' . $popularContent[$i]['Content'] . '&page=1">' . $popularContent[$i]['Content'] . '</a></li>';
            echo '</ul></div><div id="popular_country"><p class="title_aside">Popular country</p><ul>';
            for ($i = 0; $i < 4; $i++)
                if (isset($popularCountry[$i])) {
                    $name = $pdo->query('SELECT Country_RegionName FROM geocountries_regions WHERE ISO = "' . $popularCountry[$i]['Country_RegionCodeISO'] . '"')->fetch()['Country_RegionName'];
                    echo '<li><a href="?country=' . $name . '&page=1">' . $name . '</a></li>';
                }
            echo '</ul></div><div id="popular_city"><p class="title_aside">Popular city</p><ul>';
            for ($i = 0; $i < 4; $i++)
                if (isset($popularCity[$i])) {
                    $name = $pdo->query('SELECT AsciiName FROM geocities WHERE GeoNameID = ' . $popularCity[$i]['CityCode'])->fetch()['AsciiName'];
                    echo '<li><a href="?city=' . $name . '&page=1">' . $name . '</a></li>';
                }
            ?>
        </ul>
    </div>
</aside>
<section>
    <p>Filter</p>
    <div id="filter">
        <!--筛选部分-->
        <form action="">
            <select name="content">
                <option value="0">Filter by Content</option>
                <option value="Scenery">Scenery</option>
                <option value="City">City</option>
                <option value="People">People</option>
                <option value="Animal">Animal</option>
                <option value="Building">Building</option>
                <option value="Wonder">Wonder</option>
                <option value="Other">Other</option>
            </select>
            <select name="country" onchange="selectCityByCountry(this,this.form.city);">
                <option value="0">Filter by Country</option>
            </select>
            <select name="city">
                <option value="0">Filter by City</option>
            </select>
            <input name="page" value="1" hidden>
            <input type="submit" value="Filter">
        </form>
    </div>
    <?php
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
        $first = false;
        $pdo = new PDO(DBCTSTRING, DBUSER, DBPASS);
        if (isset($_GET['city']) && $_GET['city'] != '0') $city = $pdo->query('SELECT GeoNameID FROM geocities WHERE AsciiName = "' . $_GET['city'] . '"')->fetch()['GeoNameID'];
        if (isset($_GET['country']) && $_GET['country'] != '0') $country = $pdo->query('SELECT ISO FROM geocountries_regions WHERE Country_RegionName = "' . $_GET['country'] . '"')->fetch()['ISO'];
        if (isset($_GET['content']) && $_GET['content'] != '0') $content = $_GET['content'];
        if (isset($_GET['title'])) {
            $title = $_GET['title'];
            $first = true;
        }
        $string = '';
        if (isset($city)) {
            if ($first) $string .= 'AND ';
            $string .= 'CityCode = "' . $city . '" ';
            $first = true;
        }
        if (isset($country)) {
            if ($first) $string .= 'AND ';
            $string .= 'Country_RegionCodeISO = "' . $country . '" ';
            $first = true;
        }
        if (isset($content)) {
            if ($first) $string .= 'AND ';
            $string .= 'Content = "' . $content . '" ';
            $first = true;
        }
        if (!$first) echo '<h2>At least one condition should be chosen.</h2>';
        else {
            if (isset($title)) $sql = 'SELECT * FROM travelimage WHERE Title LIKE "%' . $title . '%"';
            else $sql = 'SELECT * FROM travelimage WHERE ' . $string;
            $result = $pdo->query($sql);
            if ($result->rowCount() > 0) {
                $pageSize = ($result->rowCount() - 1) / 12 + 1;
                $row = $result->fetchAll();
                echo '<div id="photo">';
                for ($i = $page * 12 - 12; $i < $page * 12; $i++) if (isset($row[$i]))
                    echo '<a href="details.php?id=' . $row[$i]['ImageID'] . '"' . '>
<div class="img-container">
<img src="../img/travel-images/normal/medium/' . $row[$i]['PATH'] . '">
</div>
</a>';
                echo '</div><div id="page">';
                $href = '?' . (isset($_GET['title']) ? '&title=' . $_GET['title'] : '') . (isset($_GET['content']) ? '&content=' . $_GET['content'] : '') .
                    (isset($_GET['country']) ? '&country=' . $_GET['country'] : '') . (isset($_GET['city']) ? '&city=' . $_GET['city'] : '');
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
        }
    } else echo '<h2>You haven\'t browsed anything yet.</h2>'
    ?>
</section>
<!--页脚部分-->
<footer>Copyright © 2020 QQD All Rights Reserved</footer>
<script>
    //实现二级联动
    var p = document.getElementById("search-button");
    var form = document.getElementsByTagName("form").item(0);
    var title = document.getElementsByTagName("input").item(0);
    p.onclick = function () {
        if (title.value === '') alert('Please type in the title.');
        else form.submit();
    };
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
    ?>
    let countrySelector = document.getElementsByName("country").item(0);
    for (let key in cities) {
        let option = document.createElement("Option");
        option.value = key;
        option.innerText = key;
        countrySelector.appendChild(option);
    }

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