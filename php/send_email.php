<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.html");
    exit();
}

$TrackingID = $_GET['TrackingID'];

$servername = "localhost";
$username = "root";
$password = "iamdexter22";
$dbname = "visa_service";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch applicant details
$sql = "SELECT email, Status FROM visa_applications WHERE TrackingID='$TrackingID'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$email = $row['email'];
$Status = $row['Status'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    if (mail($email, $subject, $message)) {
        echo "Email sent successfully.";
    } else {
        echo "Failed to send email.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet"> <!-- Link to local Bootstrap CSS -->
</head>
<body>
    <div class="container mt-4">
        <h1>Send Email to Applicant</h1>
        <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        <form action="" method="post">
            <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" id="subject" name="subject" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Email</button>
        </form>
    </div>
</body>
</html>
