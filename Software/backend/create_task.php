<?php
require 'db.php';
require 'auth.php';

function validate_task_form($data) {
    $errors = [];
    if (empty($data['title'])) {
        $errors[] = 'Title is required.';
    }
    if (empty($data['description'])) {
        $errors[] = 'Description is required.';
    }
    if (empty($data['due_date']) || !preg_match('/\d{4}-\d{2}-\d{2}/', $data['due_date'])) {
        $errors[] = 'Valid due date is required.';
    }
    if (empty($data['assigned_users']) || !is_array($data['assigned_users'])) {
        $errors[] = 'At least one assigned user is required.';
    }
    return $errors;
}

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $errors = validate_task_form($_POST);
    if (empty($errors)) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $due_date = $_POST['due_date'];
        $assigned_users = $_POST['assigned_users']; // array of user IDs

        $stmt = $mysqli->prepare("INSERT INTO tasks (title, description, due_date) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $title, $description, $due_date);
        $stmt->execute();
        $task_id = $stmt->insert_id;

        foreach ($assigned_users as $user_id) {
            $stmt = $mysqli->prepare("INSERT INTO task_assignments (task_id, user_id) VALUES (?, ?)");
            $stmt->bind_param('ii', $task_id, $user_id);
            $stmt->execute();

            // Send email notification
            $stmt = $mysqli->prepare("SELECT email FROM users WHERE id = ?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->bind_result($email);
            $stmt->fetch();
            mail($email, "New Task Assigned", "A new task '$title' has been assigned to you.");
        }

        header("Location: dashboard.php");
    } else {
       
    }
} else {
    header('Location: ../login.php');
    exit;
}
