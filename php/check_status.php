<?php
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

// Get tracking ID from form
$trackingID = $_POST['trackingID'];

// Prepare SQL statement to prevent SQL injection
$stmt = $conn->prepare("SELECT fullName, dob, email, phone, passportNumber, passportExpiry, nationality, arrivalDate, departureDate, purpose, passportPhoto, passportPage, additionalDocs, trackingID, status FROM visa_applications WHERE trackingID = ?");
$stmt->bind_param("s", $trackingID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the data
    $row = $result->fetch_assoc();
    $status = $row['status'];
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet"> <!-- Link to local Bootstrap CSS -->
    <link href="../css/styles1.css" rel="stylesheet"> <!-- Link to your external CSS -->

</head>
<body>
    <div class="container">
        <h1>Application Status</h1>
        <p><strong>Tracking ID:</strong> {$row['trackingID']}</p>
        <p><strong>Status:</strong> $status</p>
        <a href="../index.html" class="btn btn-primary">Go to Homepage</a>
    </div>
</body>
</html>
HTML;
} else {
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Not Found</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet"> <!-- Link to local Bootstrap CSS -->
    <link href="../css/styles1.css" rel="stylesheet"> <!-- Link to your external CSS -->
  
</head>
<body>
    <div class="container">
        <h1>Status Not Found</h1>
        <p>The tracking ID you entered could not be found. Please check and try again.</p>
        <a href="../status.html" class="btn btn-primary">Try Again</a>
        <a href="../index.html" class="btn btn-cancel btn-block">Cancel</a>
    </div>
</body>
</html>
HTML;
}

$stmt->close();
$conn->close();
?>
