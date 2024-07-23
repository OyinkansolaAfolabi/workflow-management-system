<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

include '../backend/db.php';
// Fetch all users
$result = $mysqli->query("SELECT * FROM users");

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$mysqli->close();
?>
<html lang="en" class=""><head>

  <meta charset="UTF-8">
  <title>Users</title>

  <meta name="robots" content="noindex">

 
  <style>

@import url(https://unpkg.com/@webpixels/css@1.1.5/dist/index.css);

/* Bootstrap Icons */
@import url("https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.4.0/font/bootstrap-icons.min.css");

  </style>

</head>

<body>

<!-- Dashboard -->
<div class="d-flex flex-column flex-lg-row h-lg-full bg-surface-secondary">
    <!-- Vertical Navbar -->
   <?php
   include 'inc/nav.php';
   ?>
    <!-- Main content -->
    <div class="h-screen flex-grow-1 overflow-y-lg-auto">
        <!-- Header -->
   <?php
   include('inc/header.php');
   ?>
        <!-- Main -->
        <main class="py-6 bg-surface-secondary">
            <div class="container-fluid">
           
                <div class="card shadow border-0 mb-7">
                    <div class="card-header">
                        <h5 class="mb-0">Users</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-nowrap">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Account type</th>
                                    <th scope="col">Registration date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php if (count($users) > 0): ?>
                                <?php foreach ($users as $user): ?>

                                <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['account_type']); ?></td>
                            <td><?php echo htmlspecialchars($user['date_created']); ?></td>
                                  
                                </tr>

                                <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No users found</td>
                    </tr>
                <?php endif; ?>
                              
                            </tbody>
                        </table>
                    </div>
                 
                </div>
            </div>
        </main>
    </div>
</div>
  
  


</body></html>