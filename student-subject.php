<?php
session_start();
require 'dbcon.php';

// Check if student ID is passed
if (!isset($_GET['student_id'])) {
    $_SESSION['message'] = "No student selected.";
    header("Location: index.php");
    exit;
}

$student_id = $_GET['student_id'];

// Handle Add Subject form submission
if (isset($_POST['add_subject'])) {
    $subject_name = mysqli_real_escape_string($con, $_POST['subject_name']);

    if (!empty($subject_name)) {
        $insert = "INSERT INTO student_subjects (student_id, subject_name) VALUES ('$student_id', '$subject_name')";
        if (mysqli_query($con, $insert)) {
            $_SESSION['message'] = "Subject added successfully.";
        } else {
            $_SESSION['message'] = "Failed to add subject.";
        }
    } else {
        $_SESSION['message'] = "Subject name cannot be empty.";
    }

    header("Location: student-subject.php?student_id=$student_id");
    exit;
}

// Fetch student
$student_query = "SELECT * FROM students WHERE id='$student_id'";
$student_result = mysqli_query($con, $student_query);
$student = mysqli_fetch_assoc($student_result);

if (!$student) {
    $_SESSION['message'] = "Student not found.";
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Subjects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <?php include('message.php'); ?>

    <div class="card">
        <div class="card-header">
            <h4>Subjects for <?= htmlspecialchars($student['name']); ?>
                <a href="index.php" class="btn btn-secondary float-end">Back</a>
            </h4>
        </div>
        <div class="card-body">

            <!-- Add Subject Form -->
            <form method="POST" class="mb-4">
                <div class="mb-3">
                    <label for="subject_name" class="form-label">Add New Subject</label>
                    <input type="text" name="subject_name" id="subject_name" class="form-control" required>
                </div>
                <button type="submit" name="add_subject" class="btn btn-primary">Add Subject</button>
            </form>

            <!-- Subject List -->
            <h5>Assigned Subjects</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Subject Name</th>
                        <th>Date Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $subjects_query = "SELECT * FROM student_subjects WHERE student_id = '$student_id' ORDER BY created_at DESC";
                    $subjects_result = mysqli_query($con, $subjects_query);

                    if (mysqli_num_rows($subjects_result) > 0) {
                        $counter = 1;
                        while ($subject = mysqli_fetch_assoc($subjects_result)) {
                            echo "<tr>
                                    <td>{$counter}</td>
                                    <td>" . htmlspecialchars($subject['subject_name']) . "</td>
                                    <td>{$subject['created_at']}</td>
                                  </tr>";
                            $counter++;
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>No subjects found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

</body>
</html>
