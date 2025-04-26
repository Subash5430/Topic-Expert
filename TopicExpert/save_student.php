<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create a new student entry from the form data
    $student = [
        "name" => $_POST['name'],
        "email" => $_POST['email'],
        "college" => $_POST['college'],
        "skills" => $_POST['skills'],
        "slot" => $_POST['slot']
    ];

    $file = 'students.json';  // JSON file to store students
    $students = [];

    // If file exists, load current students
    if (file_exists($file)) {
        $data = file_get_contents($file);
        $students = json_decode($data, true);
    }

    // Add the new student to the array
    $students[] = $student;

    // Save the updated array back to the file
    file_put_contents($file, json_encode($students, JSON_PRETTY_PRINT));

    // Redirect to the list of available students
    header("Location: available_students.php");
    exit();
}
?>
