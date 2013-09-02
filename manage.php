<html>
<head>
<title>Manage Subscriptions</title>
<link href="bootstrap.min.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
    body {
        background-color: #eee;
      }
    .managebox{
        width:420px;
        height:80%;
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
        margin-left:-210px;
    }
    .managebox h2
    {
        height:25px;
        line-height: 25px;
        font-size:20px;
        font-weight:normal;
        text-align: center;
    }
    .back {
    	position: absolute;
    	left: 15px;
    	top : 15px;
    }
</style>
</head>
<body>
<div class="managebox">
<?php
if(!(isset($_COOKIE['username']))){
	header('Location: logout.php');
	exit();
}
if(!(isset($_COOKIE['id']))){
	header('Location: logout.php');
	exit();
}
printf("<h2>Welcome, %s</h2>", $_COOKIE['username']);
?>
<br />
<div align=center class='back'><a href="index.php" class="btn btn-small btn-danger">Back To Index Page</a></div>
<div class='sourcebox'>
<?php
require("config.php");
if(isset($_POST['source'])){
		$source = $_POST['source'];
		$query = sprintf("select * from sources where Link='%s'",$source);
		$result = $mysqli->query($query);
		if(!($row = $result->fetch_assoc())){  //Couldn't find this source
			exec("/home/addsource.py '$source'");
		}
		$result = $mysqli->query($query);
		$row = $result->fetch_assoc();
		$source_id = $row['ID'];
		$query = sprintf("select * from entries where source='%s' order by ID desc limit 10 ",$source_id);
		$result = $mysqli->query($query);
		$unread_entry_id = array();
		while($row = $result->fetch_assoc()){
			array_push($unread_entry_id,$row['ID']);
		}
		$unread_entry = join(",",$unread_entry_id);
		$query = sprintf("insert into userdata(User,Source,Unread) values('%d', '%d','%s')",$_COOKIE['id'],$source_id,$unread_entry);
		$result = $mysqli->query($query);
	}
?>
</div>
<?php 
	header("content-type:text/html; charset=utf-8");
	$query = sprintf("select * from sources where id in(select Source from userdata where User='%d')",$_COOKIE['id']);
	$result = $mysqli->query($query);
	while($row = $result->fetch_assoc()){
		echo "<input type='checkbox' name='fruit' value ='apple' >";
		printf("<img src='%s' height='16' width='16'></img>",$row['Favicon']);
		echo $row['Name'];
		echo "<br />\n";
	}
?>
<?php 
$mysqli->close();
 ?>
<button type="submit" class="btn">Delete function still developing</button>
<form action="manage.php" method="post" class="form-horizontal">
<input type="text" name="source" placeholder="Please input rss feed address">
<button type="submit" class="btn btn-primary">Add!</button>
</form>
</div>
</body>
</html>