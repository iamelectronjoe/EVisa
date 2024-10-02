<?php

// Token to access admin registration page
$accessToken = '_SECURE_ACCESS_';

// Check if the token is valid
if (!isset($_GET['token']) || $_GET['token'] !== $accessToken) {
    die("Access denied.");
}

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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adminUsername = $_POST['username'];
    $adminPassword = $_POST['password'];
    $adminPasswordConfirm = $_POST['passwordConfirm'];
    $adminEmail = $_POST['email'];
    
    if ($adminPassword !== $adminPasswordConfirm) {
        echo "<script>
                alert('Passwords do not match. Please try again.');
                window.location.href = 'register_admin.php?token=_SECURE_ACCESS_';
              </script>";
        exit();
    }

    $adminPasswordHashed = password_hash($adminPassword, PASSWORD_BCRYPT); // Hashing the password
    
    // Prepare an SQL statement for execution
    $stmt = $conn->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $adminUsername, $adminPasswordHashed, $adminEmail);
    
    if ($stmt->execute()) {
        // Redirect to confirmation page
        header("Location: registration_success.php?username=" . urlencode($adminUsername));
        exit();
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet"> <!-- Link to local Bootstrap CSS -->
    <link href="../css/styles1.css" rel="stylesheet">
    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var passwordConfirm = document.getElementById("passwordConfirm").value;
            if (password !== passwordConfirm) {
                alert("Passwords do not match. Please try again.");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
</head>
<body>
    <div class="container mt-4">
        <h1>Create Admin Account</h1>
        <form action="" method="post" onsubmit="return validateForm()">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="passwordConfirm" class="form-label">Confirm Password</label>
                <input type="password" id="passwordConfirm" name="passwordConfirm" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Account</button>
            <a href="../index.html" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
