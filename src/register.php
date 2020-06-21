<?php
require_once("config.php");
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link type="text/css" rel="styleSheet" href="CSS/reset.css">
    <link type="text/css" rel="styleSheet" href="CSS/register.css">
</head>
<body>
<section>
    <?php
    function addNewUser()
    {
        date_default_timezone_set("Asia/Shanghai");
        $pdo = new PDO(DBCTSTRING, DBUSER, DBPASS);
        $duplicateCheck = $pdo->prepare('SELECT * FROM traveluser WHERE UserName =:username');
        $duplicateCheck->bindValue(':username', $_POST['username']);
        $duplicateCheck->execute();
        if ($duplicateCheck->rowCount() > 0) return false;
        $salt = md5(time());
        $newUser = $pdo->prepare('INSERT INTO traveluser (UID,UserName,Email,Pass,Salt,State,DateJoined,DateLastModified) VALUES (:uid,:username,:email,:password,:salt,1,:date,:date)');
        $newUser->bindValue(':uid', $pdo->query('SELECT UID FROM traveluser order by UID DESC LIMIT 1')->fetch()['UID'] + 1);
        $newUser->bindValue(':username', $_POST['username']);
        $newUser->bindValue(':email', $_POST['email']);
        $newUser->bindValue(':date', date('Y-m-d H:i:s'));
        $newUser->bindValue(':salt', $salt);
        $newUser->bindValue(':password', md5($_POST['password'] . $salt));
        $newUser->execute();
        return true;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (addNewUser()) header('location: ./login.php');
        else echo '
    <script>
    alert("Duplicated username! Please retry.");
    </script>
    ';
    }
    ?>
    <!--注册界面部分-->
    <a href="./login.php"><img src="../img/necessary-images/cancel.png" height="30" width="30"></a>
    <form method="post" action="">
        <p>Username<br>
            <input type="text" name="username"></p>
        <p>E-mail<br>
            <input type="text" name="email"></p>
        <p>Password<br>
            <input type="password" name="password"></p>
        <p>Confirm your password<br>
            <input type="password" name="confirmPassword"></p>
        <input type="submit" id="submit" value="Sign Up" disabled>
    </form>
</section>
<!--页脚部分-->
<footer>Copyright © 2020 QQD All Rights Reserved</footer>
<script>
    const pattern_username = /^[\w]+$/;
    const pattern_email = /^[\w-_.]+@[\w-_.]+(\.[\w-_]+)+$/;
    const pattern_password = /^[\w]{8,}$/;
    var username = document.getElementsByName("username")[0];
    var email = document.getElementsByName("email")[0];
    var password = document.getElementsByName("password")[0];
    var confirmPassword = document.getElementsByName("confirmPassword")[0];
    var submit = document.getElementById("submit");

    function ifValid() {
        if (username.value.search(pattern_username) !== -1 && email.value.search(pattern_email) !== -1 && password.value.search(pattern_password) !== -1 && confirmPassword.value === password.value)
            submit.disabled = false;
    }

    function setInvalidStyle(element) {
        element.style.backgroundImage = "linear-gradient(#f92f41, #ff898b)";
        element.style.color = "#FFFFFF";
    }

    function setValidStyle(element) {
        element.style.backgroundImage = null;
        element.style.color = null;
    }

    username.onkeyup = function () {
        if (username.value.search(pattern_username) === -1 && username.value !== "") setInvalidStyle(username);
        else {
            setValidStyle(username);
            ifValid();
        }
    };
    email.onkeyup = function () {
        if (email.value.search(pattern_email) === -1 && email.value !== "") setInvalidStyle(email);
        else {
            setValidStyle(email);
            ifValid();
        }
    };
    password.onkeyup = function () {
        if (password.value.search(pattern_password) === -1 && password.value !== "") setInvalidStyle(password);
        else setValidStyle(password);
        confirmPassword.onkeyup();
    };
    confirmPassword.onkeyup = function () {
        if (confirmPassword.value !== password.value && confirmPassword.value !== "") setInvalidStyle(confirmPassword);
        else {
            setValidStyle(confirmPassword);
            ifValid();
        }
    };
</script>
</body>
</html>