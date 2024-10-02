<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet"> <!-- Link to local Bootstrap CSS -->
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .container {
            text-align: center;
            border: 1px solid #dee2e6;
            padding: 20px;
            border-radius: 8px;
            background-color: #ffffff;
        }
        .btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registration Successful!</h1>
        <p>Your admin account has been created successfully.</p>
        <p>Username: <?php echo htmlspecialchars($_GET['username']); ?></p>
        <a href="admin_login.php" class="btn btn-primary">Proceed to Login</a>
        <a href="../index.html" class="btn btn-secondary">Go to Homepage</a>
    </div>
</body>
</html>
