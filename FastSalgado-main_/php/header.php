<?php
// Ensure that the session is started before accessing session variables
session_start();

// Check if the 'data' variable is set in the session
$data = isset($_SESSION['data']) ? $_SESSION['data'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebParking</title>
    <!-- Add your CSS links and other head content here -->
</head>
<body>
    <div id="session">
        <?php if ($data) { ?>
            <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="container-fluid">
                    <a class="navbar-brand" href="index.php"><i class="bi bi-p-square"></i>&nbsp;WebParking</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarText">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="index.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="history.php">History</a>
                            </li>
                            <?php if ($data['role'] === 'ADMIN') { ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="users.php">Users</a>
                                </li>
                            <?php } ?>
                        </ul>
                        <span class="navbar-text">
                            <i class="bi bi-person"></i><?php echo $data['name']; ?>
                            <button onclick="logout()" class="btn btn-sm btn-danger" type="button"><i class="bi bi-arrow-right-circle"></i></button>
                        </span>
                    </div>
                </div>
            </nav>
        <?php } ?>
    </div>

    <!-- Add your scripts and other body content here -->

    <script>
        // Define the logout function (you need to implement this)
        function logout() {
            // Add the logout logic here
            // For example, you can use AJAX to communicate with the server and destroy the session
        }
    </script>
</body>
</html>
