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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 20px;
        }
        .card {
            margin-bottom: 20px;
            position: relative;
            transition: all 0.3s ease;
        }
        .card-body {
            transition: all 0.3s ease;
        }
        .card-expanded {
            height: auto;
        }
        .card-details {
            display: none;
        }
        .card-expanded .card-details {
            display: block;
        }
        .card-title {
            font-size: 1.25rem;
        }
        .card-link {
            margin-top: 10px;
        }
        .btn {
            margin-top: 10px;
        }
        .status-label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            padding: 5px;
            border-radius: 3px;
            color: #fff;
        }
        .bg-warning {
            background-color: #ffc107 !important;
        }
        .bg-orange {
            background-color: #fd7e14 !important;
        }
        .bg-success {
            background-color: #28a745 !important;
        }
        .bg-danger {
            background-color: #dc3545 !important;
        }
        .document-preview {
            max-width: 100%;
            max-height: 300px;
            display: block;
            margin: 10px 0;
        }
        .pdf-thumbnail {
            width: 150px;
            height: auto;
            display: block;
            margin: 10px 0;
        }
        .pdf-thumbnail img {
            width: 100%;
            height: auto;
        }
        .status-update-form {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <a href="admin_logout.php" class="btn btn-danger">Logout</a>
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
                                    <div class="status-update-form" id="status-update-<?php echo $trackingID; ?>">
                                        <form onsubmit="updateStatus('<?php echo $trackingID; ?>'); return false;">
                                            <div class="form-group">
                                                <label for="status-select-<?php echo $trackingID; ?>">Select Status:</label>
                                                <select id="status-select-<?php echo $trackingID; ?>" class="form-control">
                                                    <option value="Application details submitted">Application details submitted</option>
                                                    <option value="Application under review">Application under review</option>
                                                    <option value="Visa approved">Visa approved</option>
                                                    <option value="Visa denied">Visa denied</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Save</button>
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

    <script src="../bootstrap/js/jquery.min.js"></script>
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleCardDetails(trackingID) {
            var allCards = document.querySelectorAll('.card');
            var currentCard = document.getElementById('card-' + trackingID);
            var button = document.getElementById('toggle-btn-' + trackingID);

            allCards.forEach(card => {
                if (card !== currentCard) {
                    card.classList.remove('card-expanded');
                    var otherButton = card.querySelector('.card-link');
                    if (otherButton) {
                        otherButton.innerHTML = 'View Details';
                    }
                }
            });

            if (currentCard.classList.contains('card-expanded')) {
                currentCard.classList.remove('card-expanded');
                button.innerHTML = 'View Details';
            } else {
                currentCard.classList.add('card-expanded');
                button.innerHTML = 'Hide Details';
            }
        }

        function toggleDocumentDetails(trackingID) {
            var allDocumentDetails = document.querySelectorAll('.document-details');
            var currentDocumentDetails = document.getElementById('documents-' + trackingID);
            var button = document.getElementById('view-docs-' + trackingID);

            allDocumentDetails.forEach(details => {
                if (details !== currentDocumentDetails) {
                    details.style.display = 'none';
                    var otherButton = details.previousElementSibling.querySelector('.btn-info');
                    if (otherButton) {
                        otherButton.innerHTML = 'View Documents';
                    }
                }
            });

            if (currentDocumentDetails.style.display === 'none') {
                currentDocumentDetails.style.display = 'block';
                button.innerHTML = 'Hide Documents';
            } else {
                currentDocumentDetails.style.display = 'none';
                button.innerHTML = 'View Documents';
            }
        }

        function toggleUpdateStatus(trackingID) {
            var statusUpdate = document.getElementById('status-update-' + trackingID);
            var button = document.getElementById('update-status-' + trackingID);
            var documentDetails = document.getElementById('documents-' + trackingID);
            
            // Hide document details if visible
            if (documentDetails.style.display === 'block') {
                documentDetails.style.display = 'none';
                var docButton = document.getElementById('view-docs-' + trackingID);
                if (docButton) {
                    docButton.innerHTML = 'View Documents';
                }
            }

            // Toggle status update form
            if (statusUpdate.style.display === 'none') {
                statusUpdate.style.display = 'block';
                button.innerHTML = 'Hide Status Update';
            } else {
                statusUpdate.style.display = 'none';
                button.innerHTML = 'Update Status';
            }
        }

        function updateStatus(trackingID) {
            var statusSelect = document.getElementById('status-select-' + trackingID);
            var newStatus = statusSelect.value;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_status.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    // Update status on the page
                    var statusLabel = document.querySelector('#card-' + trackingID + ' .status-label');
                    statusLabel.textContent = 'Status: ' + newStatus;
                    statusLabel.className = 'status-label ' + getStatusClass(newStatus);

                    // Hide the status update form
                    document.getElementById('status-update-' + trackingID).style.display = 'none';
                    
                    // Change button text back to 'Update Status'
                    var updateButton = document.getElementById('update-status-' + trackingID);
                    if (updateButton) {
                        updateButton.innerHTML = 'Update Status';
                    }
                }
            };
            xhr.send('TrackingID=' + encodeURIComponent(trackingID) + '&Status=' + encodeURIComponent(newStatus));
        }

        function getStatusClass(status) {
            switch (status) {
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
    </script>
</body>
</html>
