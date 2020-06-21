<?php
session_start();
require_once("config.php");
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>Log In</title>
    <link type="text/css" rel="styleSheet" href="CSS/reset.css">
    <link type="text/css" rel="stylesheet" href="CSS/login.css">
</head>
<body>
<?php
function validLogin()
{
    $pdo = new PDO(DBCTSTRING, DBUSER, DBPASS);
    $result = $pdo->prepare('SELECT * FROM traveluser WHERE Pass=:password');
    $salt = $pdo->prepare('SELECT Salt FROM traveluser WHERE UserName=:login or Email=:login');
    $salt->bindValue(':login', $_POST['login']);
    $salt->execute();
    if ($salt->rowCount() == 0) return false;
    $row = $salt->fetch();
    $result->bindValue(':password', md5($_POST['password'] . $row['Salt']));
    $result->execute();
    if ($result->rowCount() > 0) {
        date_default_timezone_set("Asia/Shanghai");
        $UID = $result->fetch()['UID'];
        $pdo->query('UPDATE traveluser SET DateLastModified = "' . date('Y-m-d H:i:s') . '" WHERE UID = ' . $UID);
        $_SESSION['UID'] = $UID;
        return true;
    } else return false;
}

if (isset($_GET['login'])) echo '
    <script>
    alert("Please login first!")
    </script>
    ';
if ($_SERVER["REQUEST_METHOD"] == 'POST')
    if (validLogin()) header('Location: ../index.php');
    else echo '
    <script>
    alert("Fail to login! Please check your username and password.");
    </script>
    ';
?>
<section>
    <!--登录界面部分-->
    <a href="../index.php"><img src="../img/necessary-images/cancel.png" height="30" width="30"></a>
    <form method="post" action="">
        <p>Username or E-mail<br>
            <input type="text" name="login"></p>
        <p>Password<br>
            <input type="password" name="password"></p>
        <input type="submit" value="Log In">
    </form>
</section>
<!--跳转到注册界面-->
<div>Press <a id="link" href="register.php">here</a> to sign up</div>
<!--页脚部分-->
<footer>Copyright © 2020 QQD All Rights Reserved</footer>
</body>
</html>