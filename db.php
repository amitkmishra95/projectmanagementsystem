<?php
$conn = new mysqli("localhost", "root", "", "pms");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
