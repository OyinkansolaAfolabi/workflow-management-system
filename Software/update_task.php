<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Update</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 600px;
            margin: 50px auto;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Task</h2>
        <form id="taskUpdateForm" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="taskStatus">Task Status</label>
                <select class="form-control" id="taskStatus" required>
                    <option value="">Select Status</option>
                    <option value="Not Started">Not Started</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
            <div class="form-group">
                <label for="dueDate">Due Date</label>
                <input type="date" class="form-control" id="dueDate" required>
            </div>
            <div class="form-group">
                <label for="reviewDate">Review Date</label>
                <input type="date" class="form-control" id="reviewDate" required>
            </div>
            <div class="form-group">
                <label for="progressUpdate">Progress Update</label>
                <textarea class="form-control" id="progressUpdate" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Task</button>
        </form>
    </div>

    <script>
        function validateForm() {
            const taskStatus = document.getElementById('taskStatus').value;
            const dueDate = document.getElementById('dueDate').value;
            const reviewDate = document.getElementById('reviewDate').value;
            const progressUpdate = document.getElementById('progressUpdate').value;

            if (!taskStatus || !dueDate || !reviewDate || !progressUpdate) {
                alert('All fields are required.');
                return false;
            }

            const formData = {
                taskStatus,
                dueDate,
                reviewDate,
                progressUpdate
            };

            // Send data to server
            updateTask(formData);
            return false;
        }

        function updateTask(formData) {
            fetch('/api/update-task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Task updated successfully');
                } else {
                    alert('Failed to update task');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the task');
            });
        }
    </script>
</body>
</html>
