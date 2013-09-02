<?php  
$dbhost="localhost";  
$dbuser="root";  
$dbpassword="123456";  
$dbname="rss";  
$mysqli=new mysqli("$dbhost","$dbuser","$dbpassword","$dbname") ;  
if (mysqli_connect_errno($mysqli)) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
$mysqli->query("SET CHARACTER_SET_CLIENT=utf8");
$mysqli->query("SET CHARACTER_SET_RESULTS=utf8");
?>