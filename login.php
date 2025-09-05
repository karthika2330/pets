<?php
session_start(); // Start the session

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "student_management";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        // Use prepared statement to prevent SQL injection
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username); 
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                // Successful login
                $_SESSION['username'] = $username; 
                header("Location: index.html");
                exit();
            } else {
                echo "Invalid password. Please <a href='login.html'>try again</a>.";
            }
        } else {
            echo "User not found. Please <a href='signup.html'>sign up</a>.";
        }

        $stmt->close();
    } else {
        echo "Please fill in both username and password.";
    }
}

$conn->close();
?>
