<?php
require 'db.php';
require 'auth.php';

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
    if (!empty($data['review_date']) && !preg_match('/\d{4}-\d{2}-\d{2}/', $data['review_date'])) {
        $errors[] = 'Invalid review date.';
    }
    return $errors;
}

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $errors = validate_update_form($_POST);
    if (empty($errors)) {
        $task_id = $_POST['task_id'];
        $status = $_POST['status'];
        $progress_update = $_POST['progress_update'];
        $review_date = $_POST['review_date'];

        $stmt = $mysqli->prepare("UPDATE tasks SET status = ?, review_date = ? WHERE id = ?");
        $stmt->bind_param('ssi', $status, $review_date, $task_id);
        $stmt->execute();

        $stmt = $mysqli->prepare("INSERT INTO task_updates (task_id, update_text, updated_by) VALUES (?, ?, ?)");
        $stmt->bind_param('isi', $task_id, $progress_update, $_SESSION['user_id']);
        $stmt->execute();

        header("Location: dashboard.php");
    } else {
 
    }
} else {
    header('Location: login.php');
    exit;
}
