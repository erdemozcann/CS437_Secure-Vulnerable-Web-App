<?php
// Start the session
session_start();

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include database connection
include 'includes/config.php';

// Initialize error message variable
$error_message = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_message = "Invalid CSRF token.";
    } else {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        // Check if both fields are filled
        if (!empty($username) && !empty($password)) {
            // Fetch the user from the database
            $sql = "SELECT * FROM users WHERE username = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
            // Check if user exists and password is correct
            if ($user && password_verify($password, $user['password'])) {
                // Set session variables for the logged-in user
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: admin/dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Please fill in both fields.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h1>Login</h1>

<!-- Display error message if it exists -->
<?php if (!empty($error_message)): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
<?php endif; ?>

<!-- Login Form -->
<form action="login.php" method="POST">
    <!-- CSRF Token -->
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    
    <label for="username">Username</label>
    <input type="text" name="username" id="username" placeholder="Enter your username" required><br>

    <label for="password">Password</label>
    <input type="password" name="password" id="password" placeholder="Enter your password" required><br>

    <button type="submit">Login</button>
</form>

<p>Don't have an account? <a href="register.php">Register here</a></p>

</body>
</html>
