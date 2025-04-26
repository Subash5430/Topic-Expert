<?php
session_start();
include 'db_connect.php';

// Check if user is already logged in
if (isset($_SESSION['student_id'])) {
    echo "<script>
        if (confirm('You are already logged in. Do you want to log out?')) {
            window.location.href = 'logout.php';
        } else {
            window.location.href = 'student_dashboard.php';
        }
    </script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM students WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['student_id'] = $row['id']; // assuming your table column is 'id'
            echo "<script>alert('Login Successful'); window.location.href='student_dashboard.php';</script>";
        } else {
            echo "<script>alert('Invalid password');</script>";
        }
    } else {
        echo "<script>alert('No account found with that email');</script>";
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
  <title>Sign In | StudyBuddy</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #e0f7fa, #f8f9ff);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .container {
      background: #ffffff;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 15px 25px rgba(0,0,0,0.1);
      max-width: 420px;
      width: 100%;
      transition: all 0.3s ease-in-out;
    }

    .container:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 35px rgba(0,0,0,0.15);
    }

    .container h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #1e3a8a;
      font-size: 28px;
    }

    .form-group {
      margin-bottom: 20px;
      position: relative;
    }

    .form-group label {
      font-weight: 600;
      margin-bottom: 8px;
      display: block;
      color: #374151;
    }

    .form-group i {
      position: absolute;
      top: 50%;
      left: 10px;
      transform: translateY(-50%);
      color: #9ca3af;
    }

    input {
      width: 100%;
      padding: 12px 12px 12px 35px;
      border-radius: 10px;
      border: 1.5px solid #cbd5e1;
      font-size: 15px;
      transition: border 0.2s;
    }

    input:focus {
      border-color: #1e3a8a;
      outline: none;
    }

    .forgot-password {
      text-align: right;
      margin-top: 6px;
    }

    .forgot-password a {
      font-size: 13px;
      color: #1e3a8a;
      text-decoration: none;
    }

    .forgot-password a:hover {
      text-decoration: underline;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #1e3a8a;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s ease-in-out;
    }

    button:hover {
      background: #374fc1;
    }

    .footer-text {
      text-align: center;
      margin-top: 25px;
      font-size: 14px;
      color: #6b7280;
    }

    .footer-text a {
      color: #1e3a8a;
      text-decoration: none;
      font-weight: 600;
    }

    .footer-text a:hover {
      text-decoration: underline;
    }

    @media (max-width: 480px) {
      .container {
        padding: 30px 20px;
      }

      h2 {
        font-size: 24px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Welcome Back!</h2>
    <form method="POST" action="signin.php">
      <div class="form-group">
        <label for="email">Email Address</label>
        <i class="fas fa-envelope"></i>
        <input type="email" id="email" name="email" placeholder="you@example.com" required />
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <i class="fas fa-lock"></i>
        <input type="password" id="password" name="password" placeholder="••••••••" required />
        <div class="forgot-password">
          <a href="forgot_password.php">Forgot Password?</a>
        </div>
      </div>
      <button type="submit">Sign In</button>
    </form>
    <div class="footer-text">
      <p>Don't have an account? <a href="register.php">Register</a></p>
      <p><a href="index.html">← Back to Home</a></p>
    </div>
  </div>
</body>
</html>
