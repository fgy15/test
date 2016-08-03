<!DOCTYPE HTML>
<html>
<body>
<b> 个人数据库 用户激活</b><hr/>
<?php
if (isset($_GET['token'])) {
    $link = mysqli_connect('localhost:3306', 'root', '', 'test');
    if ($link) {
        $ret = mysqli_query($link, "SELECT user,email FROM act WHERE id='" . $_GET['token'] . "';");
        if ($ret) {
            $count = 0;
            while ($row = $ret->fetch_row()) {
                $uid   = $row[0];
                $email = $row[1];
                $count++;
            }
            if ($count == 1) {
                $sql1 = "DELETE from act WHERE id='" . $_GET['token'] . "';";
                $sql2 = "UPDATE user SET act=1 where user='" . $uid . "' AND email='" . $email . "';";
                if (mysqli_query($link, $sql1) && mysqli_query($link, $sql2)) {
                    echo "用户" . $uid . "绑定" . $email . "成功";
                    echo "<meta http-equiv='refresh' content='2;URL=./index.php'>";
                }
            } else {
                echo "token 不存在或超时";
                echo "<meta http-equiv='refresh' content='2;URL=./index.php'>";
            }
        } else {
            echo mysqli_error($link);
        }
    } else {
        echo "数据库连接失败：" . mysqli_error($link);
    }
}
?>
</body>
</html>

