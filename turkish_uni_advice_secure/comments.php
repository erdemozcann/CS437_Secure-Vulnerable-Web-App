<?php
// Include database connection
include 'includes/config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $university_name = htmlspecialchars($_POST['university_name'], ENT_QUOTES, 'UTF-8');
    $commenter_name = htmlspecialchars($_POST['commenter_name'], ENT_QUOTES, 'UTF-8');
    $comment = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');

    try {
        $stmt = $pdo->prepare("INSERT INTO university_comments (university_name, commenter_name, comment) 
                               VALUES (:university_name, :commenter_name, :comment)");
        $stmt->execute([
            ':university_name' => $university_name,
            ':commenter_name' => $commenter_name,
            ':comment' => $comment
        ]);
        echo "Comment added successfully!";
    } catch (PDOException $e) {
        echo "Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Comments</title>
</head>
<body>
    <h1>Leave a Comment about a University</h1>
    <form method="POST" action="">
        <label for="university_name">University Name:</label><br>
        <input type="text" id="university_name" name="university_name" required><br><br>
        
        <label for="commenter_name">Your Name:</label><br>
        <input type="text" id="commenter_name" name="commenter_name" required><br><br>
        
        <label for="comment">Comment:</label><br>
        <textarea id="comment" name="comment" rows="4" required></textarea><br><br>
        
        <button type="submit">Submit Comment</button>
    </form>

    <!-- Button to Redirect to index.php -->
    <form action="index.php" method="GET" style="margin-top: 20px;">
        <button type="submit">Go to Index Page</button>
    </form>

    <h2>Comments</h2>
    <?php
    try {
        // Fetch comments from the database
        $stmt = $pdo->query("SELECT university_name, commenter_name, comment, created_at FROM university_comments ORDER BY created_at DESC");
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($comments as $comment) {
            // Escape output to prevent XSS
            $university_name = htmlspecialchars($comment['university_name'], ENT_QUOTES, 'UTF-8');
            $commenter_name = htmlspecialchars($comment['commenter_name'], ENT_QUOTES, 'UTF-8');
            $comment_text = htmlspecialchars($comment['comment'], ENT_QUOTES, 'UTF-8');
            $created_at = htmlspecialchars($comment['created_at'], ENT_QUOTES, 'UTF-8');

            echo "<div>";
            echo "<h3>" . $university_name . "</h3>";
            echo "<p><strong>" . $commenter_name . ":</strong> " . $comment_text . "</p>";
            echo "<small>Posted on: " . $created_at . "</small>";
            echo "<hr>";
            echo "</div>";
        }
    } catch (PDOException $e) {
        echo "Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
    ?>
</body>
</html>
