<html>
<head>
<title>The RSS Reader Login Page</title>
<link href="bootstrap.min.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
    body {
        background-color: #eee;
      }
    .loginbox{
        width:420px;
        height:230px;
        padding:40 20px;
        border:1px solid #fff; 
        color:#000; 
        margin-top:40px; 
        border-radius:8px;
        background: white;
        box-shadow:0 0 15px #222; 
        background: -moz-linear-gradient(top, #fff, #efefef 8%);
        background: -webkit-gradient(linear, 0 0, 0 100%, from(#f6f6f6), to(#f4f4f4));
        font:11px/1.5em 'Microsoft YaHei' ;
        position: absolute;
        left:50%;
        top:50%;
        margin-left:-210px;
        margin-top:-115px;
    }
    .loginbox > h2
    {
        height:25px;
        font-size:20px;
        font-weight:normal;
        text-align: center;
    }
</style>
</head>
<body>
<div class="container">
<?php 
require("config.php");
if(isset($_COOKIE['username'])){
	if($_COOKIE['username']){
		header('Location: index.php');
		exit();
	}
}
	
if(isset($_POST['username'])){
	$login_name = $_POST['username'];
	$login_pass = $_POST['password'];
    $md5_pass = md5($login_pass);
    $query = "select * from users where Username = '$login_name' and Password = '$md5_pass'";
    if ($result = $mysqli->query($query)) {
        if ($row = $result->fetch_assoc()) {
        	setcookie("username",$row['Username'],time()+86400);
        	setcookie("id",$row['ID'],time()+86400);
        	header('Location: index.php');
        } else {
            echo "Login Error";
        }
        mysqli_free_result($result);
    }
}

?>
<div class="loginbox">
<form action="login.php" method="post" class="form-horizontal">
    <div class="control-group">
        <label class="control-label" for="username">Username</label>
        <div class="controls">
            <input type="text" name="username" placeholder="Username">
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="password">Password</label>
        <div class="controls">
            <input type="password" name="password"  placeholder="Password">
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <button type="submit" class="btn btn-primary">Sign In</button>
        </div>
    </div>
</form>
<p class="text-center">
<h2>Register is not open yet.</h2>
<br />
<h2>Demo Account: test/test</h2>
</p>
</div>
</div>
 </body></html>