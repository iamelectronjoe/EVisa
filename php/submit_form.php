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

// Collect form data
$fullName = $_POST['fullName'];
$dob = $_POST['dob'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$passportNumber = $_POST['passportNumber'];
$passportExpiry = $_POST['passportExpiry'];
$nationality = $_POST['nationality'];
$arrivalDate = $_POST['arrivalDate'];
$departureDate = $_POST['departureDate'];
$purpose = $_POST['purpose'];

// Define the upload directory
$uploadDir = "../uploads/";

// Generate a unique tracking ID
$trackingID = strtoupper(substr(md5(uniqid('', true)), 0, 8));

// Function to generate a unique filename
function generateUniqueFilename($prefix, $trackingID, $uploadDir) {
    $id = 1;
    $filename = "{$trackingID}_{$prefix}_{$id}";
    while (file_exists($uploadDir . $filename . ".pdf") || file_exists($uploadDir . $filename . ".jpg") || file_exists($uploadDir . $filename . ".jpeg") || file_exists($uploadDir . $filename . ".png")) {
        $id++;
        $filename = "{$trackingID}_{$prefix}_{$id}";
    }
    return $filename;
}

// Handle passport photo upload
$passportPhoto = $_FILES['passportPhoto'];
$passportPhotoName = null;
if ($passportPhoto['error'] == UPLOAD_ERR_OK) {
    $photoName = generateUniqueFilename('pPhoto', $trackingID, $uploadDir);
    $photoExt = pathinfo($passportPhoto['name'], PATHINFO_EXTENSION);
    $photoPath = $uploadDir . $photoName . '.' . $photoExt;
    move_uploaded_file($passportPhoto['tmp_name'], $photoPath);
    $passportPhotoName = $photoName . '.' . $photoExt;
}

// Handle passport page upload
$passportPage = $_FILES['passportPage'];
$passportPageName = null;
if ($passportPage['error'] == UPLOAD_ERR_OK) {
    $pageName = generateUniqueFilename('pPage', $trackingID, $uploadDir);
    $pageExt = pathinfo($passportPage['name'], PATHINFO_EXTENSION);
    $pagePath = $uploadDir . $pageName . '.' . $pageExt;
    move_uploaded_file($passportPage['tmp_name'], $pagePath);
    $passportPageName = $pageName . '.' . $pageExt;
}

// Handle additional documents upload
$additionalDocs = $_FILES['additionalDocs'];
$additionalDocsNames = [];
if (isset($additionalDocs['name'])) {
    // Check if additionalDocs['name'] is an array
    if (is_array($additionalDocs['name'])) {
        foreach ($additionalDocs['name'] as $index => $name) {
            if ($additionalDocs['error'][$index] == UPLOAD_ERR_OK) {
                $docName = generateUniqueFilename('addDocx', $trackingID, $uploadDir);
                $docExt = pathinfo($name, PATHINFO_EXTENSION);
                $docPath = $uploadDir . $docName . '.' . $docExt;
                move_uploaded_file($additionalDocs['tmp_name'][$index], $docPath);
                $additionalDocsNames[] = $docName . '.' . $docExt;
            }
        }
    } else {
        // Handle single file upload scenario
        if ($additionalDocs['error'] == UPLOAD_ERR_OK) {
            $docName = generateUniqueFilename('addDocx', $trackingID, $uploadDir);
            $docExt = pathinfo($additionalDocs['name'], PATHINFO_EXTENSION);
            $docPath = $uploadDir . $docName . '.' . $docExt;
            move_uploaded_file($additionalDocs['tmp_name'], $docPath);
            $additionalDocsNames[] = $docName . '.' . $docExt;
        }
    }
}

// Prepare data for insertion into database
$additionalDocsList = implode(',', $additionalDocsNames);

// Insert data into database
$sql = "INSERT INTO visa_applications (fullName, dob, email, phone, passportNumber, passportExpiry, nationality, arrivalDate, departureDate, purpose, passportPhoto, passportPage, additionalDocs, trackingID)
VALUES ('$fullName', '$dob', '$email', '$phone', '$passportNumber', '$passportExpiry', '$nationality', '$arrivalDate', '$departureDate', '$purpose', '$passportPhotoName', '$passportPageName', '$additionalDocsList', '$trackingID')";

if ($conn->query($sql) === TRUE) {
    // Display confirmation HTML page
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet"> <!-- Link to local Bootstrap CSS -->
    <link href="../css/styles1.css" rel="stylesheet"> <!-- Link to your external CSS -->
</head>
<body>
    <div class="container">
        <h1>Application Submitted Successfully!</h1>
        <p>Thank you for submitting your application. Your tracking ID is:</p>
        <h2>$trackingID</h2>
        <a href="../index.html" class="btn btn-primary">Go to Homepage</a>
    </div>
</body>
</html>
HTML;
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
