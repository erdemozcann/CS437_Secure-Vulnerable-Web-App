<?php
include '../includes/config.php';

// Allowed file extensions
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
$maxFileSize = 2 * 1024 * 1024; // 2 MB

// Handle adding new news
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $link = $_POST['link'];
    
    // Handle image upload
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $file = $_FILES['image'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileName = basename($file['name']);
        $fileSize = $file['size'];
        $fileTmpPath = $file['tmp_name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validate file type
        if (!in_array($fileExtension, $allowedExtensions)) {
            die('Error: Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.');
        }

        // Validate file size
        if ($fileSize > $maxFileSize) {
            die('Error: File size exceeds the 2 MB limit.');
        }

        // Generate a unique file name
        $newFileName = uniqid('img_', true) . '.' . $fileExtension;
        $destination = $uploadDir . $newFileName;

        // Move the uploaded file to the secure directory
        if (move_uploaded_file($fileTmpPath, $destination)) {
            // Save the news entry to the database
            $sql = "INSERT INTO news (title, content, link, image, published_date) VALUES (:title, :content, :link, :image, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'title' => $title,
                'content' => $content,
                'link' => $link,
                'image' => 'uploads/' . $newFileName
            ]);

            echo "News added successfully. File uploaded: $newFileName";
        } else {
            die('Error: Failed to upload the file.');
        }
    } else {
        die('Error: File upload error.');
    }
}

// Fetch all news
$sql = "SELECT * FROM news ORDER BY published_date DESC";
$stmt = $pdo->query($sql);
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Manage News</h1>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Title" required><br>
    <textarea name="content" placeholder="Content" required></textarea><br>
    <input type="url" name="link" placeholder="External Link"><br>
    <input type="file" name="image" required><br>
    <button type="submit">Add News</button>
</form>

<h2>All News</h2>
<?php foreach ($news as $item): ?>
    <div>
        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
        <p><?php echo htmlspecialchars($item['content']); ?></p>
        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Image" style="max-width: 200px;"><br>
        <a href="delete_news.php?id=<?php echo $item['id']; ?>">Delete</a>
    </div>
<?php endforeach; ?>
