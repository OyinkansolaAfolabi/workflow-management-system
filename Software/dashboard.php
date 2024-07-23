<?php
require 'db.php';
require 'auth.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$query = "SELECT t.id, t.title, t.status, t.due_date, GROUP_CONCAT(u.username SEPARATOR ', ') AS assigned_users 
          FROM tasks t
          LEFT JOIN task_assignments ta ON t.id = ta.task_id
          LEFT JOIN users u ON ta.user_id = u.id
          GROUP BY t.id";

$result = $mysqli->query($query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1>Dashboard</h1>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Title</th>
            <th>Status</th>
            <th>Due Date</th>
            <th>Assigned Users</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($task = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($task['title']) ?></td>
                <td><?= htmlspecialchars($task['status']) ?></td>
                <td><?= htmlspecialchars($task['due_date']) ?></td>
                <td><?= htmlspecialchars($task['assigned_users']) ?></td>
                <td>
                    <a href="update_task.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-warning">Update</a>
                    <?php if ($_SESSION['user_role'] === 'supervisor'): ?>
                        <a href="delete_task.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
