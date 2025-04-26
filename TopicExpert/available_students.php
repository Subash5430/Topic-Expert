<?php
include 'db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: signin.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$searchSkill = isset($_GET['search']) ? trim($_GET['search']) : '';
$result = null;

// Slot booking logic
// Slot booking logic
if (isset($_GET['student_id'], $_GET['student_name'])) {
  $studentId = $_GET['student_id'];
  $studentName = $_GET['student_name'];

  // Fetch requester name and email from 'students' table
  $requesterName = '';
  $requesterEmail = '';
  $fetchStudent = $conn->prepare("SELECT name, email FROM students WHERE id = ?");
  $fetchStudent->bind_param("i", $student_id);
  $fetchStudent->execute();
  $fetchStudent->bind_result($requesterName, $requesterEmail);
  $fetchStudent->fetch();
  $fetchStudent->close();

  // Insert request
  $stmt = $conn->prepare("INSERT INTO requests (student_id, requester_name, requester_email, requested_slot) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("isss", $studentId, $requesterName, $requesterEmail, $studentName);

  if ($stmt->execute()) {
      echo "<script>alert('Request for slot booked successfully!');</script>";
  } else {
      echo "<script>alert('Error: " . $stmt->error . "');</script>";
  }

  $stmt->close();
}

   

// Skill search logic
if (!empty($searchSkill)) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE skills LIKE ?");
    if ($stmt) {
        $likeSkill = "%$searchSkill%";
        $stmt->bind_param("s", $likeSkill);
        $stmt->execute();
        $result = $stmt->get_result();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Available Students | StudyBuddy</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f9fbfd;
      color: #1e293b;
    }
    header {
      background-color: #1e3a8a;
      padding: 20px 50px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: white;
    }
    header h1 { font-size: 28px; }
    nav a {
      color: white;
      text-decoration: none;
      margin-left: 20px;
      font-weight: 500;
    }
    nav a:hover { text-decoration: underline; }

    .container {
      max-width: 1000px;
      margin: 40px auto;
      padding: 20px;
      flex: 1;
    }
    h2 {
      text-align: center;
      font-size: 30px;
      margin-bottom: 30px;
      color: #1e3a8a;
    }

    .search-form {
      text-align: center;
      margin-bottom: 30px;
    }
    .search-form input[type="text"] {
      width: 300px;
      padding: 10px;
      border: 2px solid #1e40af;
      border-radius: 8px;
      font-size: 16px;
    }
    .search-form button {
      padding: 10px 20px;
      background-color: #1e40af;
      color: white;
      border: none;
      border-radius: 8px;
      margin-left: 10px;
      font-weight: bold;
      cursor: pointer;
    }
    .search-form button:hover {
      background-color: #3742fa;
    }

    .student-card {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
      margin-bottom: 20px;
    }

    .student-card h3 {
      color: #1e40af;
      margin-bottom: 10px;
    }

    .student-card p {
      margin: 6px 0;
      font-size: 16px;
      color: #334155;
    }

    .book-button {
      display: inline-block;
      margin-top: 10px;
      background-color: #1e40af;
      color: white;
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
    }

    .book-button:hover {
      background-color: #3742fa;
    }

    footer {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  text-align: center;
  padding: 20px;
  background: #1e3a8a;
  color: white;
  font-size: 14px;
  z-index: 999;
}

  </style>
</head>
<body>

  <header>
    <h1>StudyBuddy</h1>
    <nav>
      <a href="index.html">Home</a>
      <a href="about.html">About</a>
      <a href="features.html">Features</a>
      <a href="student_dashboard.php">Dashboard</a>
    </nav>
  </header>

  <div class="container">
    <h2>Available Students for Help</h2>

    <!-- Search Form -->
    <form class="search-form" method="GET" action="available_students.php">
      <input type="text" name="search" placeholder="Search by skill..." value="<?php echo htmlspecialchars($searchSkill); ?>">
      <button type="submit">Search</button>
    </form>

    <!-- Dynamic Student Cards -->
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="student-card">
          <h3><?php echo htmlspecialchars($row['name']); ?></h3>
          <p><strong>College:</strong> <?php echo htmlspecialchars($row['college']); ?></p>
          <p><strong>Skills:</strong> <?php echo htmlspecialchars($row['skills']); ?></p>
          <p><strong>Available Slot:</strong> <?php echo htmlspecialchars($row['slot']); ?></p>
          <a href="available_students.php?student_id=<?php echo $row['id']; ?>&student_name=<?php echo urlencode($row['slot']); ?>&search=<?php echo urlencode($searchSkill); ?>" class="book-button">Book Slot</a>
        </div>
      <?php endwhile; ?>
    <?php elseif (!empty($searchSkill)): ?>
      <p style="text-align:center; color:#475569;">No students found with that skill.</p>
    <?php endif; ?>
  </div>

  <footer>
    &copy; 2025 StudyBuddy. Learn. Connect. Grow.
  </footer>

</body>
</html>

<?php $conn->close(); ?>
