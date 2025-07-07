<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $category_id = $_POST['category_id'];

    $imageName = '';
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $imageName);
    }

    $stmt = $pdo->prepare("INSERT INTO reviews (title, description, image, status, category_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $imageName, $status, $category_id]);

    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add New Review</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #f1f1f1;
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            max-width: 650px;
            background-color: #1e1e1e;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 206, 201, 0.15);
        }

        h2 {
            color: #00cec9;
            font-weight: 600;
            margin-bottom: 30px;
        }

        label {
            color: #ccc;
            margin-bottom: 5px;
        }

        .form-control {
            background-color: #2c2c2c;
            color: #fff;
            border: 1px solid #444;
            border-radius: 8px;
        }

        .form-control:focus {
            border-color: #00cec9;
            box-shadow: 0 0 0 0.2rem rgba(0, 206, 201, 0.25);
        }

        .btn-success {
            background: linear-gradient(135deg, #00cec9, #6c5ce7);
            border: none;
            font-weight: bold;
            border-radius: 50px;
            transition: 0.3s ease;
        }

        .btn-success:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(108, 92, 231, 0.5);
        }

        .btn-secondary {
            border-radius: 50px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2>➕ Add New Review</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Title:</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Description:</label>
            <textarea name="description" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label>Status:</label>
            <select name="status" class="form-control">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Category:</label>
            <select name="category_id" class="form-control">
                <?php
                $cat = $pdo->query("SELECT * FROM categories");
                while ($row = $cat->fetch()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label>App Image:</label>
            <input type="file" name="image" class="form-control">
        </div>

        <button class="btn btn-success me-2">Submit</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</div>

</body>
</html>
