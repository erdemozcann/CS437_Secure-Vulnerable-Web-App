<?php
// Include database configuration
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate user input
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    // Use parameter statements to avoid shell commands
    $sanitizedUsername = escapeshellarg($username); // Escapes the input for shell use
    $output = shell_exec("echo " . $sanitizedUsername); // Executes safely
    echo "WELCOME " . htmlspecialchars($output, ENT_QUOTES, 'UTF-8') . "<br>";

    // Prepare SQL to insert user data securely
    $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'user')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username, 'email' => $email, 'password' => $password]);

    echo "Registration successful! <a href='login.php'>Login here</a>";
}
?>

<!-- Registration Form -->
<form method="POST">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Register</button>
</form>
