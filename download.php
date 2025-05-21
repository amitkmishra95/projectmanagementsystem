<?php
session_start();
include("db.php");

if (!isset($_GET['id'])) {
    die("Invalid file");
}

$fileId = intval($_GET['id']);

// Get file path from DB securely
$sql = "SELECT file_path, file_type FROM group_uploads WHERE id = $fileId";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) == 0) {
    die("File not found");
}
$row = mysqli_fetch_assoc($result);

$filePath = $row['file_path'];
$fileType = $row['file_type'];

// Security check: file should exist on server
if (!file_exists($filePath)) {
    die("File not found on server");
}

// Send headers and output file
header('Content-Description: File Transfer');
header('Content-Type: ' . $fileType);
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;
?>
