

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .sidebar {
            width: 250px;
            background-color: #343a40;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            padding: 20px;
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 15px 0;
            font-weight: bold;
        }

        .sidebar a:hover {
            color: #ffc107;
        }

        .sidebar.collapsed {
            transform: translateX(-250px);
        }

        .hamburger-menu {
            display: none;
            background-color: #343a40;
            color: white;
            border: none;
            padding: 10px 15px;
            font-size: 18px;
            cursor: pointer;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
        }

        .hamburger-menu:focus {
            outline: none;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-250px);
            }

            .hamburger-menu {
                display: block;
            }
        }
        
        /* Additional styling for content area */
        .content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
        }

        .content.collapsed {
            margin-left: 0;
        }

        /* Loading indicator styling */
        .loading {
            text-align: center;
            padding: 20px;
        }

        .loading img {
            width: 50px;
        }
    </style>
</head>
<body>
    <button class="hamburger-menu" aria-label="Toggle Sidebar" onclick="toggleSidebar()">☰ Menu</button>

    <a href="#content-area" class="sr-only sr-only-focusable">Skip to content</a>

    <div class="sidebar" id="sidebar" role="navigation" aria-label="Sidebar">
        <a href="#" aria-current="page">Dashboard</a>
        <a href="#" id="applicants-link">Applicants</a>
        <a href="#">Statistics</a>
        <a href="#">Settings</a>
        <a href="admin_logout.php" class="btn btn-danger">Logout</a>
    </div>

    <main class="content" id="content-area" role="main">
        <h1>Welcome to the Admin Dashboard</h1>
        <p>Select a menu option to view content.</p>
    </main>

    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../bootstrap/js/jquery.min.js"></script>
    <script src="../js/dashboard.js"></script>
    <script src="../js/applicants.js"></script>
</body>
</html>
