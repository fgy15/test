<!DOCTYPE HTML>
<html>
<body>
<b> 个人数据库 用户登录</b><hr/>
<?php
$https_uri = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER["PHP_SELF"];
$dir       = dirname('http://' . $_SERVER['HTTP_HOST'] . $_SERVER["PHP_SELF"]);
?>
<form action="<?php echo $https_uri; ?>" method="post">
    用户名:<input type="text" name="name" /><br />
    密码:<input type="password" name="password" /><br />
    <input type="submit" name="submit" value="登录">
<?php
echo "<input type='button' onclick=\"javascrtpt:window.location.href='" . $dir . "/register.php'\" value='注册'> ";
?>
</form>

<?php
if (isset($_GET['t'])) {
    if ($_GET['t'] == 'logout') {
        setcookie("name", "", time() - 3600);
        setcookie("token", "", time() - 3600);
        die();
    }
}

if (isset($_COOKIE['name']) && isset($_COOKIE['token'])) {
    $name  = $_COOKIE['name'];
    $token = $_COOKIE['token'];
    if ($token == md5($name + md5("1024"))) {
        echo "<meta http-equiv='refresh' content='0;URL=" . $dir . "/home.php'>";
        die();
    } else {
        setcookie("name", "", time() - 3600);
        setcookie("token", "", time() - 3600);
        die();
    }

}

$nameErr = $pwdErr = "";
$name    = $pwd    = "";
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $pwd  = $_POST['password'];
    if (!empty($name) && !empty($pwd)) {
        $link = mysqli_connect('localhost:3306', 'root', '', 'test');
        if ($link) {
            $sql = "SELECT password,act from user WHERE user='" . $name . "'";
            $ret = mysqli_query($link, $sql);
            if ($ret) {
                $count = 0;
                while ($row = $ret->fetch_row()) {
                    $pwd2 = $row[0];
                    $act  = $row[1];
                    $count++;
                }
                if ($act == 0) {
                    echo "请先点击邮箱链接激活账户";
                } else if ($count == 1 && md5($pwd) == $pwd2) {
                    setcookie("name", $name, time() + 3600 * 24);
                    setcookie("token", md5($name + md5("1024")), time() + 3600 * 24);
                    echo "<meta http-equiv='refresh' content='0;URL=" . $dir . "/home.php'>";
                    die();
                }

            } else {
                echo "数据库错误：" . mysqli_error($link);
            }
        } else {
            echo "数据库连接失败：" . mysqli_error($link);
        }

    }
}
?>
</body
</html>

