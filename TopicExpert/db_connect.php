<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "topicexpert";

// Create connection to MySQL server
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Create the database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    echo "✅ Database '$database' created or already exists.<br>";
} else {
    die("❌ Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($database);

// Create 'students' table if not exists
$createStudents = "CREATE TABLE IF NOT EXISTS students (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    college VARCHAR(100),
    skills TEXT,
    slot VARCHAR(100),
    password VARCHAR(255),
    registered_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($createStudents) === TRUE) {
} else {
    echo "❌ Error creating 'students' table: " . $conn->error . "<br>";
}

// Create 'requests' table if not exists
$createRequests = "CREATE TABLE IF NOT EXISTS requests (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    student_id INT(11),
    requester_name VARCHAR(100),
    requester_email VARCHAR(100),
    requested_slot VARCHAR(100),
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    FOREIGN KEY (student_id) REFERENCES students(id)
)";
if ($conn->query($createRequests) === TRUE) {
} else {
    echo "❌ Error creating 'requests' table: " . $conn->error . "<br>";
}

$conn->close();
?>
