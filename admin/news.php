<?php
session_start();
// Require admin login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/config/db.php';

// Ensure table exists (lightweight bootstrap)
$createSql = "CREATE TABLE IF NOT EXISTS news (
  news_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT,
  image_url VARCHAR(255),
  status ENUM('Active','Inactive') DEFAULT 'Active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
@mysqli_query($conn, $createSql);

// Handle actions
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['body'] ?? '');
    $status = ($_POST['status'] ?? 'Active') === 'Inactive' ? 'Inactive' : 'Active';
    
    // Handle file upload
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../uploads/news/';
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $fileName;
        
        // Move uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            $image_url = 'uploads/news/' . $fileName;
        } else {
            $error = 'Failed to upload image.';
        }
    } else if (!empty($_POST['image_url'])) {
        // Use provided URL if no file uploaded
        $image_url = trim($_POST['image_url']);
    }

    if ($title === '') {
        $error = 'Title is required.';
    } else if (empty($error)) {
        $stmt = mysqli_prepare($conn, "INSERT INTO news (title, content, image_url, status) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, 'ssss', $title, $content, $image_url, $status);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header('Location: news.php');
        exit();
    }
}

// Toggle status
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $res = mysqli_query($conn, "SELECT status FROM news WHERE news_id = $id");
    if ($row = mysqli_fetch_assoc($res)) {
        $new = ($row['status'] === 'Active') ? 'Inactive' : 'Active';
        mysqli_query($conn, "UPDATE news SET status='$new' WHERE news_id=$id");
    }
    header('Location: news.php');
    exit();
}

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM news WHERE news_id=$id");
    header('Location: news.php');
    exit();
}

// Fetch news
$news = [];
$res = mysqli_query($conn, "SELECT news_id, title, LEFT(content, 140) AS preview, image_url, status, created_at FROM news ORDER BY created_at DESC");
if ($res) { $news = mysqli_fetch_all($res, MYSQLI_ASSOC); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>News - Admin</title>
    <?php include __DIR__ . '/layout.php'; ?>
</head>
<body>
<div class="admin-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <main class="main-content">
        <div class="page-header">
            <h1><i class="fa fa-newspaper"></i> News</h1>
            <div class="text-muted">Create and manage news shown on the homepage.</div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Add News</div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Upload Image</label>
                            <input type="file" name="image" class="form-control" />
                            <small class="form-text text-muted">Or enter URL below</small>
                            <input type="text" name="image_url" class="form-control mt-1" placeholder="http://example.com/image.jpg" />
                        </div>
                        <div class="col-12">
                            <label class="form-label">Body</label>
                            <textarea name="body" class="form-control" rows="4" placeholder="Short description..."></textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="Active" selected>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-plus"></i> Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">All News</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Preview</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($news)): ?>
                            <tr><td colspan="6" class="text-muted">No news found.</td></tr>
                        <?php else: foreach ($news as $i => $n): ?>
                            <tr>
                                <td><?php echo $i+1; ?></td>
                                <td><?php echo htmlspecialchars($n['title']); ?></td>
                                <td><?php echo htmlspecialchars($n['preview']); ?>...</td>
                                <td>
                                    <span class="badge <?php echo ($n['status']==='Active'?'badge-active':'badge-pending'); ?>">
                                        <?php echo htmlspecialchars($n['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($n['created_at']); ?></td>
                                <td>
                                    <a href="news.php?toggle=<?php echo (int)$n['news_id']; ?>" class="btn btn-sm btn-info">Toggle</a>
                                    <a href="news.php?delete=<?php echo (int)$n['news_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this news item?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>


