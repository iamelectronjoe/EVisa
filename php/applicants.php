<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.html");
    exit();
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

// Fetch applicants
$sql = "SELECT * FROM visa_applications";
$result = $conn->query($sql);

if (!$result) {
    die("Error fetching data: " . $conn->error);
}

// Function to get status class
function getStatusClass($status) {
    switch ($status) {
        case 'Application details submitted':
            return 'bg-warning text-dark'; // Yellow
        case 'Application under review':
            return 'bg-orange text-white'; // Orange
        case 'Visa approved':
            return 'bg-success text-white'; // Green
        case 'Visa denied':
            return 'bg-danger text-white'; // Red
        default:
            return 'bg-secondary text-white'; // Default
    }
}

// Function to get document paths
function getDocumentPaths($trackingID, $conn) {
    $sql = "SELECT passportPhoto, passportPage, additionalDocs FROM visa_applications WHERE TrackingID='$trackingID'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    $passportPhoto = $row['passportPhoto'];
    $passportPage = $row['passportPage'];
    $additionalDocs = $row['additionalDocs'];
    $additionalDocsArray = array_filter(explode(',', $additionalDocs)); // Remove empty values

    return [
        'passportPhoto' => $passportPhoto,
        'passportPage' => $passportPage,
        'additionalDocs' => $additionalDocsArray
    ];
}

// Check if it's an AJAX request for updating status
if (isset($_POST['update_status']) && $_POST['update_status'] === 'true') {
    $trackingID = $_POST['trackingID'];
    $status = $_POST['status'];

    // Sanitize inputs
    $trackingID = $conn->real_escape_string($trackingID);
    $status = $conn->real_escape_string($status);

    // Update status in the database
    $updateSql = "UPDATE visa_applications SET Status='$status' WHERE TrackingID='$trackingID'";
    if ($conn->query($updateSql) === TRUE) {
        echo 'success';
    } else {
        echo 'error';
    }
    exit(); // Stop further execution
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/styles2.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <h2>Applicants Cards</h2>
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                    $trackingID = htmlspecialchars($row['TrackingID']);
                    $documents = getDocumentPaths($trackingID, $conn);
                    ?>
                    <div class="col-md-4">
                        <div class="card" id="card-<?php echo $trackingID; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['fullName']); ?></h5>
                                <p class="card-text"><strong>Nationality:</strong> <?php echo htmlspecialchars($row['nationality']); ?></p>
                                <p class="card-text"><strong>Arrival Date:</strong> <?php echo htmlspecialchars($row['arrivalDate']); ?></p>
                                <span class="status-label <?php echo getStatusClass(htmlspecialchars($row['Status'])); ?>">
                                    Status: <?php echo htmlspecialchars($row['Status']); ?>
                                </span>
                                <a href="#" class="btn btn-info card-link" id="toggle-btn-<?php echo $trackingID; ?>" onclick="toggleCardDetails('<?php echo $trackingID; ?>'); return false;">View Details</a>
                                <div class="card-details">
                                    <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($row['dob']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
                                    <p><strong>Passport Number:</strong> <?php echo htmlspecialchars($row['passportNumber']); ?></p>
                                    <p><strong>Passport Expiry:</strong> <?php echo htmlspecialchars($row['passportExpiry']); ?></p>
                                    <p><strong>Departure Date:</strong> <?php echo htmlspecialchars($row['departureDate']); ?></p>
                                    <p><strong>Purpose:</strong> <?php echo htmlspecialchars($row['purpose']); ?></p>
                                    <a href="#" class="btn btn-info" id="view-docs-<?php echo $trackingID; ?>" onclick="toggleDocumentDetails('<?php echo $trackingID; ?>'); return false;">View Documents</a>
                                    <a href="#" class="btn btn-warning" id="update-status-<?php echo $trackingID; ?>" onclick="toggleUpdateStatus('<?php echo $trackingID; ?>'); return false;">Update Status</a>
                                    <a href="send_email.php?TrackingID=<?php echo $trackingID; ?>" class="btn btn-primary">Send Email</a>
                                    <div class="document-details" id="documents-<?php echo $trackingID; ?>" style="display: none;">
                                        <h5>Passport Photo</h5>
                                        <?php if ($documents['passportPhoto']): ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($documents['passportPhoto']); ?>" class="document-preview" alt="Passport Photo">
                                            <p>File Name: <?php echo htmlspecialchars(basename($documents['passportPhoto'])); ?></p>
                                        <?php else: ?>
                                            <p>No passport photo available.</p>
                                        <?php endif; ?>
                                        <h5>Passport Page</h5>
                                        <?php if ($documents['passportPage']): ?>
                                            <?php if (preg_match('/\.(pdf)$/i', $documents['passportPage'])): ?>
                                                <div class="pdf-thumbnail">
                                                    <a href="../uploads/<?php echo htmlspecialchars($documents['passportPage']); ?>" target="_blank">
                                                        <img src="../icons/pdf-icon.png" alt="PDF Thumbnail">
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <img src="../uploads/<?php echo htmlspecialchars($documents['passportPage']); ?>" class="document-preview" alt="Passport Page">
                                            <?php endif; ?>
                                            <p>File Name: <?php echo htmlspecialchars(basename($documents['passportPage'])); ?></p>
                                        <?php else: ?>
                                            <p>No passport page available.</p>
                                        <?php endif; ?>
                                        <h5>Additional Documents</h5>
                                        <?php if (count($documents['additionalDocs']) > 0): ?>
                                            <?php foreach ($documents['additionalDocs'] as $doc): ?>
                                                <?php if (preg_match('/\.(pdf)$/i', $doc)): ?>
                                                    <div class="pdf-thumbnail">
                                                        <a href="../uploads/<?php echo htmlspecialchars($doc); ?>" target="_blank">
                                                            <img src="../icons/pdf-icon.png" alt="PDF Thumbnail">
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <img src="../uploads/<?php echo htmlspecialchars($doc); ?>" class="document-preview" alt="Additional Document">
                                                <?php endif; ?>
                                                <p>File Name: <?php echo htmlspecialchars(basename($doc)); ?></p>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p>No additional documents available.</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="status-update-form" id="status-update-form-<?php echo $trackingID; ?>" style="display: none;">
                                        <form id="status-update-form-<?php echo $trackingID; ?>" onsubmit="updateStatus('<?php echo $trackingID; ?>'); return false;">
                                            <input type="hidden" name="trackingID" value="<?php echo htmlspecialchars($trackingID); ?>">
                                            <label for="status-<?php echo $trackingID; ?>">Update Status:</label>
                                            <select name="status" id="status-<?php echo $trackingID; ?>" class="form-control">
                                                <option value="Application details submitted">Application details submitted</option>
                                                <option value="Application under review">Application under review</option>
                                                <option value="Visa approved">Visa approved</option>
                                                <option value="Visa denied">Visa denied</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary mt-2">Update</button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No applicants found.</p>
            <?php endif; ?>
        </div>
    </div>

   
</body>
</html>
