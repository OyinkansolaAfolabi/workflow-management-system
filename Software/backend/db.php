<?php
//connect to YHROCU database
$mysqli = new mysqli('localhost', 'root', '', 'YHROCU');
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

?>