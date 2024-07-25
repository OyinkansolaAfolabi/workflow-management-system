<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../');
    exit;
}
require '../backend/db.php';

if (!isset($_GET['task_id'])) {
    header('location:tasks.php?error=Invalid+task+selected');
    exit;
}

$userId = $_SESSION['user_id'];
$accountType = $_SESSION['account_type'];
$task_id = $_GET['task_id'];

// Fetch task details
$query = "SELECT t.id, t.title, t.description, t.due_date, t.status, t.due_date, t.review_date 
          FROM tasks t
          WHERE t.id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $task_id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

if (!$task) {
    header('location:tasks.php?error=Task+not+found');
    exit;
}

// Fetch all users
$users_query = "SELECT id, name FROM users";
$users_result = $mysqli->query($users_query);

// Fetch assigned users
$assigned_users_query = "SELECT user_id FROM task_assignments WHERE task_id = ?";
$assigned_stmt = $mysqli->prepare($assigned_users_query);
$assigned_stmt->bind_param('i', $task_id);
$assigned_stmt->execute();
$assigned_users_result = $assigned_stmt->get_result();
$assigned_users = [];
while ($row = $assigned_users_result->fetch_assoc()) {
    $assigned_users[] = $row['user_id'];
}

//Function to validate the update form
function validate_update_form($data) {
    $errors = [];
    if (empty($data['task_id']) || !is_numeric($data['task_id'])) {
        $errors[] = 'Invalid task ID.';
    }
    if (empty($data['status']) || !in_array($data['status'], ['Pending', 'In Progress', 'Completed'])) {
        $errors[] = 'Invalid status.';
    }
    if (empty($data['progress_update'])) {
        $errors[] = 'Progress update is required.';
    }
    if (!empty($data['due_date']) && !preg_match('/\d{4}-\d{2}-\d{2}/', $data['due_date'])) {
        $errors[] = 'Invalid due date.';
    }
    if (!empty($data['review_date']) && !preg_match('/\d{4}-\d{2}-\d{2}/', $data['review_date'])) {
        $errors[] = 'Invalid review date.';
    }

    return $errors;
}

//Function to validate the update form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $errors = validate_update_form($_POST);
    if (empty($errors)) {
        $task_id = $_POST['task_id'];
        $status = $_POST['status'];
        $progress_update = $_POST['progress_update'];
        $due_date = $_POST['due_date'];
        $review_date = $_POST['review_date'];
        $assigned_users = $_POST['assigned_users'];

        //Update the task
        $stmt = $mysqli->prepare("UPDATE tasks SET status = ?, due_date = ?, review_date = ? WHERE id = ?");
        $stmt->bind_param('sssi', $status, $due_date, $review_date, $task_id);
        $stmt->execute();
        //Log the task update
        $stmt = $mysqli->prepare("INSERT INTO task_updates (task_id, update_text, updated_by) VALUES (?, ?, ?)");
        $stmt->bind_param('isi', $task_id, $progress_update, $_SESSION['user_id']);
        $stmt->execute();

        // delete existing assigned users and reassign task to both exiting and newly selected users
        $mysqli->query("DELETE FROM task_assignments WHERE task_id = $task_id");
        $assign_stmt = $mysqli->prepare("INSERT INTO task_assignments (task_id, user_id) VALUES (?, ?)");
        foreach ($assigned_users as $user_id) {
            $assign_stmt->bind_param('ii', $task_id, $user_id);
            $assign_stmt->execute();
        }

        header("Location: tasks.php?task_updated=true");
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Task</title>
    <meta name="robots" content="noindex">
    <style>
        @import url(https://unpkg.com/@webpixels/css@1.1.5/dist/index.css);
        @import url("https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.4.0/font/bootstrap-icons.min.css");
    </style>
</head>
<body>
<!-- Dashboard -->
<div class="d-flex flex-column flex-lg-row h-lg-full bg-surface-secondary">
    <!-- Vertical Navbar -->
    <?php include 'inc/nav.php'; ?>
    <!-- Main content -->
    <div class="h-screen flex-grow-1 overflow-y-lg-auto">
        <!-- Header -->
        <?php include('inc/header.php'); ?>
        <!-- Main -->
        <main class="py-6 bg-surface-secondary">
            <div class="container-fluid">
                <div class="card shadow border-0 mb-7">
                    <div class="card-header">
                        <h5 class="mb-0">Update Task</h5>
                    </div>
                    <div class="table-responsive">
                        <form action="" method="POST" id="updateTaskForm" style="max-width:700px; margin:0 auto">
                            <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                            <div class="form-group">
                                <label for="title">Task Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" disabled>
                            </div>
                            <div class="form-group">
                                <label for="description">Task Description</label>
                                <textarea class="form-control" id="description" name="description" disabled><?php echo htmlspecialchars($task['description']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="due_date">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo htmlspecialchars($task['due_date']); ?>" <?php if ($task['status'] === 'Closed') echo 'disabled'; ?>>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>

                                <select class="form-control" id="status" name="status"  <?php if ($task['status'] === 'Closed') echo 'disabled'; ?>>
                                    <option value="Pending" <?php if ($task['status'] === 'Pending') echo 'selected'; ?>>Pending</option>
                                    <option value="In Progress" <?php if ($task['status'] === 'In Progress') echo 'selected'; ?>>In Progress</option>
                                    <option value="Completed" <?php if ($task['status'] === 'Completed') echo 'selected'; ?>>Completed</option>
                                    <?php
                                    if($accountType ==  'supervisor')
                                    {
                                    ?>
                                    <option value="Closed" <?php if ($task['status'] === 'Closed') echo 'selected'; ?>>Closed</option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="progress_update">Progress Update</label>
                                <textarea class="form-control" id="progress_update" name="progress_update" placeholder="Input new progress update" required <?php if ($task['status'] === 'Closed') echo 'disabled'; ?>></textarea>
                            </div>
                            <div class="form-group">
                                <label for="review_date">Review Date</label>
                                <input type="date" class="form-control" id="review_date" name="review_date" value="<?php echo htmlspecialchars($task['review_date']); ?>" <?php if ($task['status'] === 'Closed') echo 'disabled'; ?>>
                            </div>
                            <?php
                    if ($accountType === 'supervisor') {
                    ?>
                            <div class="form-group">
                                <label for="assigned_users">Assign to Users</label>
                                <select multiple class="form-control" id="assigned_users" name="assigned_users[]" required  <?php if ($task['status'] === 'Closed') echo 'disabled'; ?>>
                                    <?php
                                    while ($user = $users_result->fetch_assoc()) {
                                        $selected = in_array($user['id'], $assigned_users) ? 'selected' : '';
                                        echo "<option value=\"{$user['id']}\" $selected>{$user['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php
                    }
                            ?>
                            <br />
                            <br />
                            <button type="submit" class="btn btn-primary"  <?php if ($task['status'] === 'Closed') echo 'disabled'; ?>>Update Task</button>
                            <br />
                            <br />
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
