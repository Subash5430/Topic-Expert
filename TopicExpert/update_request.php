<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = $_POST['request_id'];
    $action = $_POST['action']; // "Approved" or "Rejected"

    $stmt = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $action, $request_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: student_dashboard.php");
exit();
?>
