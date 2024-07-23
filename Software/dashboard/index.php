<?php
session_start();

// Check if the user is authenticated
if (!isset($_SESSION['user'])) {
    header('Location: ../');
    exit;
}

include '../backend/db.php';
$userId = $_SESSION['user_id'];
$accountType = $_SESSION['account_type'];

// Delete a task
if (isset($_GET['delete_task']) && $_GET['delete_task'] === 'true' && isset($_GET['task_id'])) {
    $taskId = $_GET['task_id'];

    // Delete the task
    $deleteQuery = "DELETE FROM tasks WHERE id = ?";
    $stmt = $mysqli->prepare($deleteQuery);
    $stmt->bind_param('i', $taskId);
    $stmt->execute();
    $stmt->close();

    // Redirect to tasks.php
    header('Location: tasks.php?task_deleted=true');
    exit;
}

$searchQuery = '';
$statusFilter = '';
$dueDateOrder = '';
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}
if (isset($_GET['status'])) {
    $statusFilter = $_GET['status'];
}
if (isset($_GET['due_date'])) {
    $dueDateOrder = $_GET['due_date'];
}

$query = '';
if ($accountType === 'supervisor') {
    // Fetch all tasks
    $query = "SELECT t.id, t.title, t.status, t.due_date, t.review_date 
              FROM tasks t
              WHERE (t.title LIKE ? OR t.status LIKE ?) LIMIT 10";
} else {
    // Fetch tasks assigned to the logged-in user and tasks that are visible to everyone
    $query = "SELECT t.id, t.title, t.status, t.due_date, t.review_date 
              FROM tasks t
              LEFT JOIN task_assignments ta ON t.id = ta.task_id AND ta.user_id = ?
              WHERE (ta.user_id IS NOT NULL OR t.visible_to_all = 1)
              AND (t.title LIKE ? OR t.status LIKE ?) LIMIT 10";
}

// Add status filter
if ($statusFilter !== '') {
    $query .= " AND t.status = ?";
}

// Add due date sorting
if ($dueDateOrder === 'asc') {
    $query .= " ORDER BY t.due_date ASC";
} elseif ($dueDateOrder === 'desc') {
    $query .= " ORDER BY t.due_date DESC";
}

$stmt = $mysqli->prepare($query);
$searchPattern = '%' . $searchQuery . '%';
if ($accountType === 'supervisor') {
    if ($statusFilter !== '') {
        $stmt->bind_param('sss', $searchPattern, $searchPattern, $statusFilter);
    } else {
        $stmt->bind_param('ss', $searchPattern, $searchPattern);
    }
} else {
    if ($statusFilter !== '') {
        $stmt->bind_param('isss', $userId, $searchPattern, $searchPattern, $statusFilter);
    } else {
        $stmt->bind_param('iss', $userId, $searchPattern, $searchPattern);
    }
}

$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}

$stmt->close();
$mysqli->close();
?>
<html lang="en" class=""><head>

  <meta charset="UTF-8">
  <title>Dashboard</title>

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
                        <span> <h5 class="mb-0">Top 10 Tasks</h5></span>
                       
                        <a href="tasks.php" class="btn btn-default">View all tasks</a>

                        <div class="row">
                        <div class="col-6">
                        <form method="GET" action="" style="">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Search by title or status">
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" <?php if ($statusFilter === 'pending') echo 'selected'; ?>>Pending</option>
                                <option value="in progress" <?php if ($statusFilter === 'in progress') echo 'selected'; ?>>In Progress</option>
                                <option value="completed" <?php if ($statusFilter === 'completed') echo 'selected'; ?>>Completed</option>
                                <option value="closed" <?php if ($statusFilter === 'closed') echo 'selected'; ?>>Closed</option>
                            </select>
                            <select name="due_date" class="form-select">
                                <option value="">Sort by Due Date</option>
                                <option value="asc" <?php if ($dueDateOrder === 'asc') echo 'selected'; ?>>Ascending</option>
                                <option value="desc" <?php if ($dueDateOrder === 'desc') echo 'selected'; ?>>Descending</option>
                            </select>
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                        </form>   
</div>
<div class="col-6">
    <a href="create_task.php" class="btn d-inline-flex btn-sm btn-primary mx-1" style="float:right;">
                            <span class="pe-2">
                                <i class="bi bi-plus"></i>
                            </span>
                            <span>Create task</span>
                        </a>
                        <a href="export_tasks.php?search=<?php echo htmlspecialchars($searchQuery); ?>&status=<?php echo htmlspecialchars($statusFilter); ?>&due_date=<?php echo htmlspecialchars($dueDateOrder); ?>" class="btn d-inline-flex btn-sm btn-success mx-1" style="float:right;">
                            <span class="pe-2">
                                <i class="bi bi-file-earmark-spreadsheet"></i>
                            </span>
                            <span>Export to CSV</span>
                        </a>
</div>
</div>
                    </div>
                    <div class="table-responsive">
                        <?php
                        if (isset($_GET['tast_created'])) {
                            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                  <strong>Success!</strong> Task created successfully.
                                  </div>';
                        } else if (isset($_GET['task_updated'])) {
                            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                  <strong>Success!</strong> Task was updated successfully.
                                  </div>';
                        } else if (isset($_GET['task_deleted'])) {
                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                  <strong>Success!</strong> Task was deleted successfully.
                                  </div>';
                        }
                        ?>
                        <table class="table table-hover table-nowrap">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Due date</th>
                                    <th scope="col">Review date</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (count($tasks) > 0): ?>
                                <?php foreach ($tasks as $task): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($task['id']); ?></td>
                                        <td><?php echo htmlspecialchars($task['title']); ?></td>
                                        <td><?php echo htmlspecialchars($task['due_date']); ?></td>
                                        <td><?php echo htmlspecialchars($task['review_date']); ?></td>
                                        <td><?php echo htmlspecialchars($task['status']); ?></td>
                                        <td>
                                            <a href="task_update.php?task_id=<?php echo $task['id']; ?>" class="btn d-inline-flex btn-sm btn-neutral border-base mx-1">
                                                <span class="pe-2">
                                                    <i class="bi bi-pen"></i>
                                                </span>
                                                <span>Update</span>
                                            </a>
                                            <a href="task-progress-updates.php?task_id=<?php echo $task['id']; ?>" class="btn d-inline-flex btn-sm btn-neutral border-base mx-1">
                                                <span class="pe-2">
                                                    <i class="bi bi-eye"></i>
                                                </span>
                                                <span>View progress updates</span>
                                            </a>
                                            <?php if ($accountType == 'supervisor'): ?>
                                                <a href="?delete_task=true&task_id=<?php echo $task['id']; ?>" class="btn d-inline-flex btn-sm btn-danger mx-1" onclick="return confirm('Are you sure you want to delete this task?');">
                                                    <span class="pe-2">
                                                        <i class="bi bi-trash"></i>
                                                    </span>
                                                    <span>Delete</span>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No tasks found.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer border-0 py-5">
                      
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
  
  


</body></html>