
<?php
/* php& mysqldb connection file */
$user = "root"; //mysqlusername
$pass = ""; //mysqlpassword
$host = "localhost"; //server name or ipaddress
$dbname = "expensemanagementsystem"; //your db name

/*echo $user ."<br>";
echo $pass ."<br>";
echo $host ."<br>";
echo $dbname ."<br>"; */

$dbconn = mysqli_connect($host, $user, $pass, $dbname) or die(mysqli_error($dbconn));
?>