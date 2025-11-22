<?php
include('db.php');

if (!isset($_GET['id'])) {
    echo '<p>No complaint selected.</p>';
    exit();
}

$id = intval($_GET['id']);

// Fetch complaint securely
$stmt = $conn->prepare("SELECT * FROM complaints WHERE complaint_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<p>Complaint not found.</p>';
    exit();
}

$complaint = $result->fetch_assoc();

// Display complaint (same as user_view.php)
echo '<table style="width:100%;height:50%; border-collapse:collapse; font-family:Arial, sans-serif;">';

$uploadsFolderServer = $_SERVER['DOCUMENT_ROOT'] . '/Complaint_Portal/uploads/';
$uploadsFolderWeb = '/Complaint_Portal/uploads/';

foreach ($complaint as $key => $value) {
    if (empty($value)) continue;

    echo '<tr>';
    echo '<th style="text-align:left; padding:4px; border:1px solid #ddd; background:teal; color:white;font-size: 0.9rem;">'
            . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) .
         '</th>';

    if ($key === 'file' && !empty($value)) {
        $fileName = basename($value);
        $serverPath = $uploadsFolderServer . $fileName;
        $webPath = $uploadsFolderWeb . $fileName;

        echo '<td style="padding:4px; border:1px solid #ddd;">';

        if (file_exists($serverPath)) {
            echo '<a href="'. htmlspecialchars($webPath) .'" download style="color:#1976d2; font-weight:bold; text-decoration:none;font-size: 0.8rem;">Download File</a>';
        } else {
            echo '<span style="color:red; font-weight:bold;font-size: 0.8rem;">File not found</span>';
        }

        echo '</td>';
    } else {
        echo '<td style="padding:5px; border:1px solid #ddd; color:white; font-weight:600;font-size: 0.9rem;">' . nl2br(htmlspecialchars($value)) . '</td>';
    }

    echo '</tr>';
}

echo '</table>';

$stmt->close();
$conn->close();
?>
