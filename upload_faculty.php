<?php
require 'vendor/autoload.php';
include 'db.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_FILES['excel_file']['tmp_name']) {
    $filePath = $_FILES['excel_file']['tmp_name'];
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    foreach ($rows as $index => $row) {
        if ($index === 0) continue;

        list($name, $faculty_id,  $designation,$branch,$email, $password , $contact) = $row;

        $stmt = $conn->prepare("INSERT INTO faculty (name, faculty_id, designation, email, branch,password,contact) VALUES (?, ?, ?, ?, ?,?,?)");
        $stmt->bind_param("sssssss", $name, $faculty_id, $designation,$branch ,$email,$password,$contact );
        $stmt->execute();
    }

    echo "<script>alert('Faculty uploaded successfully!');window.location='register_faculty.php';</script>";
}
?>
