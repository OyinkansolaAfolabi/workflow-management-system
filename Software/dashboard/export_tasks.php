<?php
session_start();

//Check if the user is authenticated
if (!isset($_SESSION['user'])) {
    header('Location: ../');
    exit;
}
//Include the database connection file
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

//Initialize search and filter variables
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
//Build the query
$query = '';
if ($accountType === 'supervisor') {
    // Fetch all tasks
    $query = "SELECT t.id, t.title, t.status, t.due_date, t.review_date 
              FROM tasks t
              WHERE (t.title LIKE ? OR t.status LIKE ?)";
} else {
    // Fetch tasks assigned to the logged-in user and tasks that are visible to everyone
    $query = "SELECT t.id, t.title, t.status, t.due_date, t.review_date 
              FROM tasks t
              LEFT JOIN task_assignments ta ON t.id = ta.task_id AND ta.user_id = ?
              WHERE (ta.user_id IS NOT NULL OR t.visible_to_all = 1)
              AND (t.title LIKE ? OR t.status LIKE ?)";
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

// Fetch the tasks from the result
$tasks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}
//Close the statement and database connection
$stmt->close();
$mysqli->close();
// Generate CSV content
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="tasks.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Title', 'Due Date', 'Review Date', 'Status']);

foreach ($tasks as $task) {
    fputcsv($output, $task);
}

// Close the output stream
fclose($output);
exit;
?>
