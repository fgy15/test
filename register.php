<!DOCTYPE HTML>
<html>
<body>
<b> 个人数据库 用户注册</b><hr/>
<?php
$https_uri = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER["PHP_SELF"];
$dir       = dirname('http://' . $_SERVER['HTTP_HOST'] . $_SERVER["PHP_SELF"]);

$nameErr = $pwdErr = $emailErr = "";
$name    = $pwd    = $email    = "";
if (isset($_POST['reg'])) {
    $name  = $_POST['name'];
    $pwd   = $_POST['password'];
    $email = $_POST['email'];
    if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && !empty($_POST['name']) && !empty($_POST['password']) && empty($_POST['emailErr'])) {
        $link = mysqli_connect('localhost:3306', 'root', '', 'test');
        if ($link) {
            #mysqli_select_db("test",$link);
            $sql1 = "INSERT INTO `user` (`user`, `password`, `email`) VALUES ('" . $name . "', '" . md5($pwd) . "','" . $email . "');";
            $sql2 = "INSERT INTO `act`(`id`,`user`,`email`) VALUES('" . md5($name . $pwd) . "','" . $name . "','" . $email . "');";
            if (mysqli_query($link, $sql1) && mysqli_query($link, $sql2)) {
                echo "注册成功,登陆前请激活邮箱";
                $uri = $dir . '/act.php?token=' . $token = md5($name . $pwd);
                sendmail($email, $name, $uri);
                echo "<meta http-equiv='refresh' content='2;URL=./index.php'>";
                die();
            } else {
                echo "数据库错误：" . mysqli_error($link);
            }
        } else {
            echo "数据库连接失败：" . mysqli_error($link);
        }
    } else if (empty($_POST['name'])) {
        $nameErr = "用户名不能为空";
    } else if (empty($_POST['password'])) {
        $pwdErr = "密码不能为空";
    } else if (empty($_POST['email'])) {
        $emailErr = "邮箱不能为空";
    } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $emailErr = "邮箱格式不对";
    }
}
function sendmail($email, $user, $uri)
{
    require_once '../phpmailer/PHPMailerAutoload.php';
    //示例化PHPMailer核心类
    $mail = new PHPMailer();

    //是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
    $mail->SMTPDebug = 0;

    //使用smtp鉴权方式发送邮件，当然你可以选择pop方式 sendmail方式等 本文不做详解
    //可以参考http://phpmailer.github.io/PHPMailer/当中的详细介绍
    $mail->isSMTP();
    //smtp需要鉴权 这个必须是true
    $mail->SMTPAuth = true;
    //链接qq域名邮箱的服务器地址
    $mail->Host = 'smtp.exmail.qq.com';
    //设置使用ssl加密方式登录鉴权
    $mail->SMTPSecure = 'ssl';
    //设置ssl连接smtp服务器的远程服务器端口号 可选465或587
    $mail->Port = 465;
    //设置smtp的helo消息头 这个可有可无 内容任意
    //$mail->Helo = 'Hello smtp.qq.com Server';
    //设置发件人的主机域 可有可无 默认为localhost 内容任意，建议使用你的域名
    $mail->Hostname = 'fuguoyin.com';
    //设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
    $mail->CharSet = 'UTF-8';
    //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
    $mail->FromName = '管理员';
    //smtp登录的账号 这里填入字符串格式的qq号即可
    $mail->Username = 'admin@fuguoyin.com';
    //smtp登录的密码 这里填入“独立密码” 若为设置“独立密码”则填入登录qq的密码 建议设置“独立密码”
    $mail->Password = $_ENV['mail_passwd'];
    //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
    $mail->From = 'admin@fuguoyin.com';
    //邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
    $mail->isHTML(true);
    //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 这里第二个参数的意义不大
    $mail->addAddress($email, $user);
    //添加多个收件人 则多次调用方法即可
    #$mail->addAddress('xxx@163.com','晶晶在线用户');
    //添加该邮件的主题
    $mail->Subject = '激活账户';
    //添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件
    $mail->Body = "<a href=" . $uri . ">请点击链接激活账户</a>";
    //为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
    #$mail->addAttachment('./d.jpg','mm.jpg');
    //同样该方法可以多次调用 上传多个附件
    #$mail->addAttachment('./Jlib-1.1.0.js','Jlib.js');

    //发送命令 返回布尔值
    //PS：经过测试，要是收件人不存在，若不出现错误依然返回true 也就是说在发送之前 自己需要些方法实现检测该邮箱是否真实有效
    $status = $mail->send();

    //简单的判断与提示信息
    if ($status) {
        echo '发送邮件成功';
    } else {
        echo '发送邮件失败，错误信息未：' . $mail->ErrorInfo;
    }
}
?>

<form action="<?php echo $https_uri; ?>" method="post">
        用户名:<input type="text" name="name" value="<?php echo $name; ?>" />*<?php echo $nameErr; ?><br />
        密码:<input type="password" name="password" value="<?php echo $pwd; ?>" />*<?php echo $pwdErr; ?><br />
        邮箱:<input type="text" name="email" value="<?php echo $email; ?>" />*<?php echo $emailErr; ?><br />
        <input type="submit" name="reg" value="注册"/>
</form>
</body>
</html>
