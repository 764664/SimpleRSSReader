<html>
<head>
<style type="text/css">
  html{font-size:12px;}
  fieldset{width:520px; margin: 0 auto;}
  legend{font-weight:bold; font-size:14px;}
  label{float:left; width:70px; margin-left:10px;}
  .left{margin-left:80px;}
  .input{width:150px;}
  span{color: #666666;}
</style>
<script language=JavaScript>
function InputCheck(RegForm)
{
  if (RegForm.username.value == "")
  {
    alert("用户名不可为空!");
    RegForm.username.focus();
    return (false);
  }
  if (RegForm.password.value == "")
  {
    alert("必须设定登录密码!");
    RegForm.password.focus();
    return (false);
  }
  if (RegForm.repass.value != RegForm.password.value)
  {
    alert("两次密码不一致!");
    RegForm.repass.focus();
    return (false);
  }
  if (RegForm.email.value == "")
  {
    alert("电子邮箱不可为空!");
    RegForm.email.focus();
    return (false);
  }
}
</script>
</head>
<body>
<?php
if(!isset($_POST['submit'])){
    exit('非法访问!');
}
$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];
//注册信息判断
if(!preg_match('/^[\w\x80-\xff]{3,15}$/', $username)){
    exit('错误：用户名不符合规定。<a href="javascript:history.back(-1);">返回</a>');
}
if(strlen($password) < 6){
    exit('错误：密码长度不符合规定。<a href="javascript:history.back(-1);">返回</a>');
}
if(!preg_match('/^w+([-+.]w+)*@w+([-.]w+)*.w+([-.]w+)*$/', $email)){
    exit('错误：电子邮箱格式错误。<a href="javascript:history.back(-1);">返回</a>');
}

require("config.php"); 
//检测用户名是否已经存在
$check_query = mysql_query("select uid from user where username='$username' limit 1");
if(mysql_fetch_array($check_query)){
    echo '错误：用户名 ',$username,' 已存在。<a href="javascript:history.back(-1);">返回</a>';
    exit;
}
//写入数据
$password = MD5($password);
$regdate = time();
$sql = "INSERT INTO user(username,password,email)VALUES('$username','$password','$email')";
if(mysql_query($sql,$conn)){
    exit('用户注册成功！点击此处 <a href="login.html">登录</a>');
} else {
    echo '抱歉！添加数据失败：',mysql_error(),'<br />';
    echo '点击此处 <a href="javascript:history.back(-1);">返回</a> 重试';
}
?>
<fieldset>
<legend>用户注册</legend>
<form name="RegForm" method="post" action="reg.php" onSubmit="return InputCheck(this)">
<p>
<label for="username" class="label">用户名:</label>
<input id="username" name="username" type="text" class="input" />
<span>(必填，3-15字符长度，支持汉字、字母、数字及_)</span>
<p/>
<p>
<label for="password" class="label">密 码:</label>
<input id="password" name="password" type="password" class="input" />
<span>(必填，不得少于6位)</span>
<p/>
<p>
<label for="repass" class="label">重复密码:</label>
<input id="repass" name="repass" type="password" class="input" />
<p/>
<p>
<label for="email" class="label">电子邮箱:</label>
<input id="email" name="email" type="text" class="input" />
<span>(必填)</span>
<p/>
<p>
<input type="submit" name="submit" value="  提交注册  " class="left" />
</p>
</form>
</fieldset>
</body>
</html>