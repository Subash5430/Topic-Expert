<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $college = $_POST['college'];
    $skills = $_POST['skills'];
    $slot = $_POST['slot'];
    $password = $_POST['password'];

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO students (name, email, college, skills, slot, password)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $name, $email, $college, $skills, $slot, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='signin.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register | StudyBuddy</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Poppins', sans-serif;
      background: #f0f4f8;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }
    .register-container {
      background: #ffffff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      max-width: 500px;
      width: 100%;
    }
    h2 {
      text-align: center;
      color: #1e3a8a;
      margin-bottom: 30px;
    }
    form input, form textarea, form select {
      width: 100%;
      padding: 12px 15px;
      margin-bottom: 18px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
      background: #f9fafb;
      outline: none;
    }
    form input:focus, form textarea:focus {
      border-color: #1e3a8a;
    }
    form input[type="submit"] {
      background: #1e3a8a;
      color: white;
      font-weight: bold;
      border: none;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    form input[type="submit"]:hover {
      background: #3742fa;
    }
    .back-link {
      text-align: center;
      margin-top: 20px;
    }
    .back-link a {
      color: #1e3a8a;
      text-decoration: none;
    }
    .back-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="register-container">
    <h2>Student Registration</h2>
    <form action="register.php" method="POST">
      <input type="text" name="name" placeholder="Your Full Name" required />
      <input type="email" name="email" placeholder="Email Address" required />
      <input type="text" name="college" placeholder="College Name" required />
      <input type="password" name="password" placeholder="Create Password" required />
      <input type="text" name="skills" placeholder="Skills (e.g. Python, HTML, DBMS)" required />
      <input type="text" name="slot" placeholder="Available Slot (e.g. Mon 4-5 PM)" required />
      <input type="submit" value="Register">
    </form>
    <div class="back-link">
      <p>Already registered? <a href="signin.php">Sign In</a></p>
    </div>
  </div>

</body>
</html>
