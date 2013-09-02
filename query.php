<?php
	if(isset($_POST['ID'])){
		require('config.php');
		$ID = $_POST['ID'];

		//获取sourceID
		$query = sprintf("select * from entries where ID='%s'", $ID);
		$result = $mysqli->query($query);
		$row = $result->fetch_assoc();
		$source = $row['Source'];

		//获取读信息数组
		$query = sprintf("select * from userdata where User='%d' and Source = '%d'", $_COOKIE['id'], $source);
		$resultofread = $mysqli->query($query);
		$row_read = $resultofread->fetch_assoc();
		$string_read = $row_read['Unread'];
		$array_read = split(',', $string_read);

		
		if(($key = array_search($ID, $array_read)) !== false) {
    		unset($array_read[$key]);
		}
		$stringofread = join(',',$array_read);
		$query = sprintf("update userdata set Unread='%s' where User='%d' and Source = '%d'", $stringofread, $_COOKIE['id'], $source);
		//echo $query;
		$mysqli->query($query);
		//以上为标记已读


		$query = "select * from entries where ID = '$ID'";
		$result = $mysqli->query($query);
		$row = $result->fetch_assoc();
		printf("<a href='%s' class='entry-title-link'>%s</a>", $row['Link'], $row['Title']);
		echo $row['Content'];
		$mysqli->close();
	}
?>