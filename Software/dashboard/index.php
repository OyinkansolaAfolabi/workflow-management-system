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
                <!-- Card stats -->
                <div class="row g-6 mb-6">
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card shadow border-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <span class="h6 font-semibold text-muted text-sm d-block mb-2">Users</span>
                                        <span class="h3 font-bold mb-0">0</span>
                                    </div>
                                    <div class="col-auto">
                                       
                                    </div>
                                </div>
                                <div class="mt-2 mb-0 text-sm">
                                   
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                
            
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card shadow border-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <span class="h6 font-semibold text-muted text-sm d-block mb-2">Tasks</span>
                                        <span class="h3 font-bold mb-0">0</span>
                                    </div>
                                    <div class="col-auto">
                                       
                                    </div>
                                </div>
                                <div class="mt-2 mb-0 text-sm">
                                   
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                
                </div>



                <div class="card shadow border-0 mb-7">
                    <div class="card-header">
                        <h5 class="mb-0">Tasks</h5>
                        <a href="tasks.php" class="btn btn-default">View all tasks</a>
                    </div>
                    <div class="table-responsive">
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