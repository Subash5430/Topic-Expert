<?php
include 'db_connect.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: signin.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student info
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch requests made to this student
$stmt2 = $conn->prepare("SELECT * FROM requests WHERE student_id = ?");
$stmt2->bind_param("i", $student_id);
$stmt2->execute();
$requests = $stmt2->get_result();

// Fetch requests made by this student to others
$stmt3 = $conn->prepare("SELECT r.*, s.name AS student_name FROM requests r JOIN students s ON r.student_id = s.id WHERE r.requester_email = ?");
$stmt3->bind_param("s", $student['email']);
$stmt3->execute();
$sent_requests = $stmt3->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Dashboard | StudyBuddy</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet"/>
  <link href="navigation.css" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f1f5f9;
      padding: 40px;
      color: #1e293b;
    }
    .dashboard {
      max-width: 800px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.08);
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #1e40af;
    }
    .info p {
      margin: 10px 0;
    }
    .requests {
      margin-top: 30px;
    }
    .request-card {
      background: #f9fafb;
      border: 1px solid #cbd5e1;
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 15px;
    }
    .btn {
      padding: 6px 12px;
      margin-right: 10px;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }
    .btn-approve {
      background: #16a34a;
      color: white;
    }
    .btn-reject {
      background: #dc2626;
      color: white;
    }
    .status-pending {
      color: #f59e0b;
      font-weight: bold;
    }
    .status-approved {
      color: #16a34a;
      font-weight: bold;
    }
    .status-rejected {
      color: #dc2626;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <!-- Responsive Navigation Bar -->
<nav class="navbar">
  <div class="navbar-brand">StudyBuddy</div>
  <div class="navbar-toggle" onclick="toggleMenu()">☰</div>
  <div class="navbar-links" id="navbarLinks">
    <a href="index.html">Home</a>
    <a href="available_students.php">Slot</a>
    <a href="logout.php" class="logout-link">Logout</a>
  </div>
</nav>


<div class="dashboard">
  <h2>Welcome, <?php echo htmlspecialchars($student['name']); ?> 👋</h2>

  <div class="info">
    <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
    <p><strong>College:</strong> <?php echo htmlspecialchars($student['college']); ?></p>
    <p><strong>Skills:</strong> <?php echo htmlspecialchars($student['skills']); ?></p>
    <p><strong>Available Slot:</strong> <?php echo htmlspecialchars($student['slot']); ?></p>
  </div>

  <!-- Incoming Requests -->
  <div class="requests">
    <h3>Slot Requests To You:</h3>
    <?php if ($requests->num_rows > 0): ?>
      <?php while ($req = $requests->fetch_assoc()): ?>
        <div class="request-card">
          <p><strong>Requester:</strong> <?php echo htmlspecialchars($req['requester_name']); ?></p>
          <p><strong>Email:</strong> <?php echo htmlspecialchars($req['requester_email']); ?></p>
          <p><strong>Requested Slot:</strong> <?php echo htmlspecialchars($req['requested_slot']); ?></p>

          <?php if ($req['status'] == 'Pending'): ?>
            <p><strong>Status:</strong> <span class="status-pending">Pending ⏳</span></p>
            <form method="POST" action="update_request.php">
              <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
              <button class="btn btn-approve" name="action" value="Approved">Approve</button>
              <button class="btn btn-reject" name="action" value="Rejected">Reject</button>
            </form>
          <?php elseif ($req['status'] == 'Approved'): ?>
            <p><strong>Status:</strong> <span class="status-approved">Approved ✅</span></p>
          <?php elseif ($req['status'] == 'Rejected'): ?>
            <p><strong>Status:</strong> <span class="status-rejected">Rejected ❌</span></p>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No requests yet.</p>
    <?php endif; ?>
  </div>

  <!-- Sent Requests -->
  <div class="requests">
    <h3>Requests You've Sent:</h3>
    <?php if ($sent_requests->num_rows > 0): ?>
      <?php while ($sent = $sent_requests->fetch_assoc()): ?>
        <div class="request-card">
          <p><strong>To:</strong> <?php echo htmlspecialchars($sent['student_name']); ?></p>
          <p><strong>Requested Slot:</strong> <?php echo htmlspecialchars($sent['requested_slot']); ?></p>
          <p><strong>Status:</strong> 
            <?php if ($sent['status'] == 'Pending'): ?>
              <span class="status-pending">Pending ⏳</span>
            <?php elseif ($sent['status'] == 'Approved'): ?>
              <span class="status-approved">Approved ✅</span>
            <?php elseif ($sent['status'] == 'Rejected'): ?>
              <span class="status-rejected">Rejected ❌</span>
            <?php endif; ?>
          </p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>You haven't sent any slot requests yet.</p>
    <?php endif; ?>
  </div>
</div>

  <script>
  function toggleMenu() {
    const navLinks = document.getElementById("navbarLinks");
    navLinks.classList.toggle("show");
  }
</script>
</body>
</html>

<?php
$stmt2->close();
$stmt3->close();
$conn->close();
?>
