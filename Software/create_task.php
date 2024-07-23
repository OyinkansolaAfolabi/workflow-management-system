<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Task</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1>Create Task</h1>
    <form action="backend/create_task.php" method="POST" id="createTaskForm">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <div class="form-group">
            <label for="due_date">Due Date</label>
            <input type="date" class="form-control" id="due_date" name="due_date" required>
        </div>
        <div class="form-group">
            <label for="assigned_users">Assign to Users</label>
            <select multiple class="form-control" id="assigned_users" name="assigned_users[]" required>
                <?php
                $users = $mysqli->query("SELECT id, username FROM users");
                while ($user = $users->fetch_assoc()) {
                    echo "<option value=\"{$user['id']}\">{$user['username']}</option>";
                }
                ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create Task</button>
    </form>
</div>
<script>
    document.getElementById('createTaskForm').addEventListener('submit', function(event) {
        const title = document.getElementById('title').value;
        const description = document.getElementById('description').value;
        const dueDate = document.getElementById('due_date').value;
        const assignedUsers = document.getElementById('assigned_users').selectedOptions;

        if (!title || !description || !dueDate || assignedUsers.length === 0) {
            alert('All fields are required.');
            event.preventDefault();
        }
    });
</script>
</body>
</html>
