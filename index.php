<!DOCTYPE html>
<html>
<head>
<title>The RSS Reader</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<link href="bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
<link rel="shortcut icon" href="./favicon.ico" >
<link rel="icon" href="./icon-32.png" sizes="32x32">
<link rel="icon" href="./icon-64.png" sizes="64x64">
<script type='text/javascript' src="jquery-2.0.2.min.js"></script>
<script type='text/javascript' src="bootstrap.min.js"></script>
<script> 
$(function() {
	var arr = [];
	$('.content').hide();
	$('.entry').click(function() {
		var id = $(this).attr('id');
		$('.content[id!=\'c'+id+'\']').hide();
		var data = {
			ID: id
		};
		if(($.inArray(id,arr))==-1){
			$('#c'+id).load("query.php", data);
			$('#c'+id).append("<hr />");
			$('#'+id).removeClass("unread");
			$('#'+id).addClass("read");
		}
		$('#c'+id).toggle();
		arr.push(id);
	});
});
</script>
<style tpye="text/css">
body {
	padding-top: 20px;
}
.entry {
	font-family: 'Microsoft YaHei', Arial;
	height: 30px;
	cursor: pointer;
}
.entry h2 {
	font-size: 15px;
	line-height: 30px;
	margin: 0 0px;
}
.left {
	height: 100%;
}
.read {
	background-color: #eee;
}
.read h2 {
	font-weight: normal;
}
.entry-container {
	font-family: 'Microsoft YaHei', Arial;
	margin-top: 15px;
}
.source-title {
	color: black;
	font-weight: bold;
}
.current {
	color: red;
}
.sitelink {
	color: black;
	font-size: 14px;
	font-weight: bold;
}
.source-title-in-content{
	height: 33px;
	padding-top: 7px;
}
hr {
	margin: 0;
}
.welcome {
	font-size: 20px;
	font-weight: bold;
	line-height: 20px;
}
.viewer-main {
	padding-top: 50px;
}
</style>
</head>
<body>
<div class='container-fluid'>
<?php 
if(!(isset($_COOKIE['username']))){
	header('Location: logout.php');
	exit();
}elseif(!($_COOKIE['username'])){
	header('Location: logout.php');
	exit();
}
if(!(isset($_COOKIE['id']))){
	header('Location: logout.php');
	exit();
}
date_default_timezone_set("PRC");
header("content-type:text/html; charset=utf-8");
$entries_per_page = 50;
require("config.php");
?>
<div class="row-fluid"> 

<div class="span2 left">
<div class="sidebar" data-spy="affix">
<?php printf("<div><span class='label welcome'>Welcome, %s !</span></div>", $_COOKIE['username']); ?>
<a href="logout.php" class='btn btn-small btn-inverse'>Log Out</a>
<hr />
<a href="manage.php" class='btn btn-small btn-danger'>Manage Subscriptions</a><br />
<hr />
<?php
$query = sprintf("select * from sources where id in(select Source from userdata where User='%d')",$_COOKIE['id']);
$result = $mysqli->query($query);
while($row = $result->fetch_assoc()){
	$source_link = $row['Link'];
	$sitelink = $row['SiteLink'];
	$icolink = $row['Favicon'];
	$query_unread = sprintf("select * from userdata where User='%s' and Source='%s'", $_COOKIE['id'], $row['ID']);
	$result_unread = $mysqli->query($query_unread);
	$row_unread = $result_unread->fetch_assoc();
	$array_unread = split(',', $row_unread['Unread']);
	$unread_num = count($array_unread);
	$hasnew = " name-unread";
	if(isset($_GET['source'])){
		if(strcmp($row['ID'],$_GET['source'])==0){
			$iscurrent = " current";
		}
		else{
			$iscurrent = "";
		}
	}else{
		$iscurrent = "";
	}
	

	if(empty($row_unread['Unread'])){
		$unread_num = 0;
		$hasnew = "";
	}
	printf("<img src='%s' style='height: 16px; width: 16px;'></img>",$icolink);
	printf("<a href='index.php?source=%d' class='source-title%s%s'>%s</a>", $row['ID'], $hasnew, $iscurrent, $row['Name']);
	printf("(%d)<br />\n", $unread_num);
}
?>
</div>
</div>

<div class="span10">
<?php
if(isset($_GET['source'])){
	$source=$_GET['source'];
	if(isset($_GET['page'])){
		$page = $_GET['page'];
		$query = sprintf("select * from entries where source='%d' order by ID desc limit %d,%d", $source, $page*$entries_per_page, $entries_per_page);
	}
	else{
		$page = 0;
		$query = sprintf("select * from entries where source='%d' order by ID desc limit %d", $source, $entries_per_page);
	}
	$mainresult = $mysqli->query($query);

	//Viewer Header
	printf("<div id='viewer-header' data-spy='affix'><a href='setread.php?id=%s' class='btn'>Mark All As Read</a>",$source);
	if($page>0){
		printf("<a href='index.php?source=%d&page=%d' class='btn btn-info'>Previous Page</a> ",$source,$page-1);
	}
	$query=sprintf("select * from entries where source='%d' order by ID desc limit %d,%d", $source, ($page+1)*$entries_per_page, $entries_per_page);
	$result = $mysqli->query($query);
	if($row = $result->fetch_assoc()){
		printf("<a href='index.php?source=%d&page=%d' class='btn btn-primary'>Next Page</a>",$source,$page+1);
	}
	echo "</div>";

	//Viewer Main
	echo "<div class='viewer-main'>";
	$query_source = sprintf("select * from sources where id='%s'", $source);
	$result_source = $mysqli->query($query_source);
	$row_source = $result_source->fetch_assoc();
	$source_title = $row_source['Name'];
	$source_link = $row_source['Link'];
	printf("<div class='source-title-in-content'><a href='%s' class='sitelink'>%s >></a></div>\n<hr />\n", $row_source['SiteLink'], $source_title);

	//Query read or not info
	$query = sprintf("select * from userdata where User='%d' and Source = '%d'", $_COOKIE['id'], $source);
	$resultofread = $mysqli->query($query);
	$row_read = $resultofread->fetch_assoc();
	$string_read = $row_read['Unread'];
	$array_read = split(',', $string_read);

	while($row = $mainresult->fetch_assoc()){
		if(in_array($row['ID'], $array_read) ){
			printf("\n<div class='unread entry row-fluid' id='%d'><div class='span11'><h2 class='entry-title'>%s</h2></div>", $row['ID'], $row['Title']);
		}
		else{
			printf("\n<div class='read entry row-fluid' id='%d'><div class='span11'><h2 class='entry-title'>%s</h2></div>", $row['ID'], $row['Title']);
		}
		$date = new DateTime($row['Date'], new DateTimeZone('GMT'));
		$datenow = new DateTime(NULL);
		$datenow->sub(new DateInterval("P1D"));
		$date->setTimezone(new DateTimeZone('Asia/Shanghai'));
		$datenow->setTimezone(new DateTimeZone('Asia/Shanghai'));
		if($date->format('Ymd') == $datenow->format('Ymd')){
			//echo "<div class='entry-date span1'>".date('H:i:s', strtotime($row['Date']));
			echo "<div class='entry-date span1'>".$date->format('Y-m-d');
		}
		else{
			//echo "<div class='entry-date span1'>".date('Y-m-d', strtotime($row['Date']));
			echo "<div class='entry-date span1'>".$date->format('H:i:s');
		}
		printf("<a href='%s' class='entry-original' target='_blank'><i class='icon-share-alt'></i></a>",$row['Link']);
		echo "</div>";
		echo "</div>";
		printf("<div id='c%d' class='content entry-container'></div>", $row['ID']);

	}

}
else{
	$query_first_source = sprintf("select * from userdata where User='%s' limit 1",$_COOKIE['id']);
	$result_first_source = $mysqli->query($query_first_source);
	$row_first_suorce = $result_first_source->fetch_assoc();
	$first_source = $row_first_suorce['Source'];
	$href = sprintf("Location: index.php?source=%s",$first_source);
	header($href);
}
?>
<?php
$mysqli->close();
?>
</div></div></div></div>
</body>
</html>