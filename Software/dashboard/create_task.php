<?php
session_start(); //Start the session to manage user authentication
// Check if the user is not logged in, redirect to the login page
if (!isset($_SESSION['user'])) {
    header('Location: ../');
    exit;
}
//Include the database connection file
require '../backend/db.php';
$userId = $_SESSION['user_id'];
$accountType = $_SESSION['account_type'];

//Function to validate the task form data
function validate_task_form($data) {
    $errors = [];
    //Check if the title is provided
    if (empty($data['title'])) {
        $errors[] = 'Title is required.';
    }
    //Check if the title is provided
    if (empty($data['description'])) {
        $errors[] = 'Description is required.';
    }
    //Check if a valid due date is provided
    if (empty($data['due_date']) || !preg_match('/\d{4}-\d{2}-\d{2}/', $data['due_date'])) {
        $errors[] = 'Valid due date is required.';
    }
    //Check if at least one user is assigned to the task
    if (empty($data['assigned_users']) || !is_array($data['assigned_users'])) {
        $errors[] = 'At least one assigned user is required.';
    }
    return $errors;
}


//Check if at least one user is assigned to the task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $errors = validate_task_form($_POST);
    //If there are no errors, proceed to create a new task
    if (empty($errors)) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $due_date = $_POST['due_date'];
        $task_visibility = $_POST['task_visibility'];
        $assigned_users = $_POST['assigned_users']; // array of user IDs
        
        //create new task
        $stmt = $mysqli->prepare("INSERT INTO tasks (title, description, due_date, visible_to_all) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $title, $description, $due_date, $task_visibility);
        $stmt->execute();
        $task_id = $stmt->insert_id;

        foreach ($assigned_users as $user_id) {
            //assign task to each members
            $stmt = $mysqli->prepare("INSERT INTO task_assignments (task_id, user_id) VALUES (?, ?)");
            $stmt->bind_param('ii', $task_id, $user_id);
            $stmt->execute();

            // Send email notification to assigned users
            $stmt = $mysqli->prepare("SELECT email FROM users WHERE id = ?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->bind_result($email);
            $stmt->fetch();
            @mail($email, "New Task Assigned", "A new task ".$title." has been assigned to you.");
        }
//Redirect to the tasks page with a success message
 header('location:tasks.php?tast_created=true');exit;
    } else {
       
    }
} 
?>

<html lang="en" class=""><head>

  <meta charset="UTF-8">
  <title>Create task</title>

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
                        <h5 class="mb-0">New task</h5>
                    </div>
                    <div class="table-responsive">
                    <form action="create_task.php" method="POST" id="createTaskForm" style="max-width:700px; margin:0 auto">
    <div class="form-group">
        <label for="title">&nbsp;</label>
        <input type="text" class="form-control" id="title" name="title" placeholder="Task title" required>
    </div>
    <div class="form-group">
        <label for="description">&nbsp;</label>
        <textarea class="form-control" id="description" name="description" placeholder="Task description" required></textarea>
    </div>
    <div class="form-group">
        <label for="due_date">Due Date</label>
        <input type="date" class="form-control" id="due_date" name="due_date" placeholder="Due date" required>
    </div>
    <div class="form-group">
        <label for="assigned_users">Assign to Users</label>
        <select multiple class="form-control" id="assigned_users" name="assigned_users[]" required>
            <?php
            $users = $mysqli->query("SELECT id, name FROM users");
            while ($user = $users->fetch_assoc()) {
                echo "<option value=\"{$user['id']}\">{$user['name']}</option>";
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="task_visibility">Visibility</label>
        <select  class="form-control" id="task_visibility" name="task_visibility">
            <option value="0" selected>Visible to only assigned users</option>
            <option value="1">Visible to all</option>
        </select>

    </div>

    <br />
    <br />
    <button type="submit" class="btn btn-primary">Create Task</button>
    <br />  <br />
</form>

              
                    </div>
                 
                </div>
            </div>
        </main>
    </div>
</div>
  
  


</body></html>