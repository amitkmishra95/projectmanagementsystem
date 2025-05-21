
<?php
require 'vendor/autoload.php';
include 'db.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_FILES['excel_file']['tmp_name']) && $_FILES['excel_file']['tmp_name']) {
    $filePath = $_FILES['excel_file']['tmp_name'];
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    foreach ($rows as $index => $row) {
        if ($index === 0) continue; // Skip header row

        // Safely unpack the row values
        list($name, $erpid, $rollno, $batch_start, $batch_end, $email, $contact, $password,$vcode, $verify, $section, $branch) = $row;
    
        // Prepare statement correctly - use column names without $ sign
        $stmt = $conn->prepare("INSERT INTO users (name, erpid, rollno, batch_start, batch_end, email, contact, password, vcode, verify, section, branch) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $vcode = uniqid(); // or you can use any static/fixed value for vcode

        $stmt->bind_param("ssssssssssss", $name, $erpid, $rollno, $batch_start, $batch_end, $email, $contact, $password, $vcode, $verify, $section, $branch);
        $stmt->execute();
    }

    echo "<script>alert('Users uploaded successfully!'); window.location='register_users.php';</script>";
}
?>
