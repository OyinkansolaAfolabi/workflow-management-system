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
if (!isset($_GET['task_id'])) {
    header('location:tasks.php?error=Invalid+task+selected');
    exit;
}

$task_id = $_GET['task_id'];

//Delet a task
if (isset($_GET['delete_progress']) && $_GET['delete_progress'] === 'true' && isset($_GET['progress_id'])) {
    $progress_id = $_GET['progress_id'];

    // Delete the task
    $deleteQuery = "DELETE FROM task_updates WHERE id = ?";
    $stmt = $mysqli->prepare($deleteQuery);
    $stmt->bind_param('i', $progress_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to tasks.php
    header('Location: task-progress-updates.php?task_id='.$task_id.'&progress_deleted=true');
    exit;
}



    $query = "SELECT p.id, p.update_text, p.updated_by, p.created_at, u.name 
              FROM task_updates p
              LEFT JOIN users u  on p.updated_by = u.id
              WHERE p.task_id = ?";


$stmt = $mysqli->prepare($query);
$stmt->bind_param('s',  $task_id);


$stmt->execute();
$result = $stmt->get_result();

$progress_history = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $progress_history[] = $row;
    }
}

$stmt->close();
$mysqli->close();
?>

<html lang="en" class=""><head>

  <meta charset="UTF-8">
  <title>Task update log</title>

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
                        <h5 class="mb-0">Task update log</h5>
                       
                    </div>

                    <div class="col-sm-3 col-3 text-sm-start">
                    <div class="mx-n1">
                    <a href="tasks.php" class="btn d-inline-flex btn-sm btn-neutral border-base mx-1">
                                    <span class=" pe-2">
                                        <i class="bi bi-arrow-left"></i>
                                    </span>
                                    <span>Back</span>
                                </a>

                                </div></div>


                    <div class="table-responsive">
                        <?php
                     if(isset($_GET['progress_deleted']))
                        {
                            echo  '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> Progress update was deleted succsessfully.
                          </div>'; 
                        }

                        
                        ?>
                        <table class="table table-hover table-nowrap">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Update text</th>
                                    <th scope="col">Updated by</th>
                                    <th scope="col">Updated at</th>
                                    <th scope="col">Actions</th>
                                   
                                </tr>
                            </thead>
                            <tbody>

                            <?php if (count($progress_history) > 0): ?>
                                <?php foreach ($progress_history as $progress): ?>

                                <tr>
                            <td><?php echo htmlspecialchars($progress['id']); ?></td>
                            <td><?php echo htmlspecialchars($progress['update_text']); ?></td>
                            <td><?php echo htmlspecialchars($progress['name']); ?></td>
                            <td><?php echo htmlspecialchars($progress['created_at']); ?></td>
                            <td>
                                <?php
                                  if($accountType ==  'supervisor')
                                    {
                                    ?>
                                     <a href="?task_id=<?php echo $_GET['task_id'];?>&delete_progress=true&progress_id=<?php echo $progress['id'];?>" class="btn d-inline-flex btn-sm btn-danger border-base mx-1">
                                    <span class=" pe-2">
                                        <i class="bi bi-trash"></i>
                                    </span>
                                    <span>Delete</span>
                                </a>
                                    <?php
                                    }
                                    ?>
                          
                            </td>
                                  
                                </tr>

                                <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No progress history found for this task</td>
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