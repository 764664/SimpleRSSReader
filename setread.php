<?php
	if(isset($_GET['id'])){
		require('config.php');
		$ID = $_GET['id'];

		$query = sprintf("update userdata set Unread='' where User='%s' and Source='%s'",$_COOKIE['id'],$ID);
		$mysqli->query($query);

		$mysqli->close();

		$href = sprintf("Location: index.php?source=%s",$ID);
		header($href);
	}
?>