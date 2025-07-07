<?php
require_once 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mobile Application Reviews</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #121212;
      color: #f1f1f1;
      font-family: 'Segoe UI', sans-serif;
    }

    h2 {
      color: #00cec9;
      font-weight: 700;
    }

    .card {
      background-color: #1e1e1e;
      border: none;
      border-radius: 16px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0 12px rgba(0, 206, 201, 0.3);
    }

    .card-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: #81ecec;
    }

    .card-text {
      color: #dfe6e9;
    }

    .badge {
      font-size: 0.8rem;
      padding: 5px 10px;
      border-radius: 10px;
    }

    .bg-success {
      background-color: #00b894 !important;
    }

    .bg-secondary {
      background-color: #636e72 !important;
    }

    .input-group input.form-control {
      background-color: #2c2c2c;
      color: #fff;
      border: 1px solid #444;
    }

    .input-group input.form-control:focus {
      border-color: #00cec9;
      box-shadow: 0 0 0 0.2rem rgba(0, 206, 201, 0.25);
    }

    .btn {
      border-radius: 8px;
    }

    .btn-outline-primary {
      border-color: #00cec9;
      color: #00cec9;
    }

    .btn-outline-primary:hover {
      background-color: #00cec9;
      color: #000;
    }

    .btn-sm {
      padding: 6px 10px;
      font-size: 0.85rem;
    }

    .text-muted.small {
      background-color: #2f2f2f;
      padding: 6px 10px;
      border-radius: 6px;
      color: #b2bec3;
      margin-bottom: 5px;
    }

    .card-img-top {
      border-top-left-radius: 16px;
      border-top-right-radius: 16px;
      height: 200px;
      object-fit: cover;
    }

    a {
      text-decoration: none;
    }

    .card-body > hr {
      margin-top: 1rem;
      margin-bottom: 1rem;
      border-color: #444;
    }

    @media (max-width: 768px) {
      .btn-sm {
        width: 100%;
        margin-bottom: 0.5rem;
      }

      .d-flex.gap-2 {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>

<div class="container py-4">
  <h2 class="text-center mb-4">📱 Mobile Application Reviews</h2>

  <!-- Search Bar -->
  <form method="GET" class="mb-3">
    <div class="input-group">
      <input type="text" name="search" class="form-control" placeholder="Search by title or category" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
      <button class="btn btn-primary" type="submit">Search</button>
    </div>
  </form>

  <!-- Centered Action Buttons -->
  <div class="mb-4 text-center">
    <div class="d-inline-flex flex-column flex-md-row gap-3 justify-content-center">
      <a href="create.php" class="btn btn-lg px-4" style="background-color: #00cec9; color: #121212; font-weight: 600;">
        ➕ Add New Review
      </a>
      <a href="export_pdf.php" class="btn btn-lg btn-outline-light px-4" style="border: 2px solid #ff7675; color: #ff7675;">
        📄 Export to PDF
      </a>
    </div>
  </div>

  <!-- Reviews Grid -->
  <div class="row">
    <?php
    $query = "SELECT r.*, c.name AS category 
              FROM reviews r 
              JOIN categories c ON r.category_id = c.id";

    if (!empty($_GET['search'])) {
      $search = '%' . $_GET['search'] . '%';
      $stmt = $pdo->prepare($query . " WHERE r.title LIKE ? OR c.name LIKE ?");
      $stmt->execute([$search, $search]);
    } else {
      $stmt = $pdo->prepare($query);
      $stmt->execute();
    }

    while ($row = $stmt->fetch()) {
    ?>
    <div class="col-md-4 mb-4">
      <div class="card shadow-sm h-100">
        <!-- App Image -->
        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="App Image">

        <!-- Card Body -->
        <div class="card-body">
          <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
          <p class="card-text"><?= htmlspecialchars($row['description']) ?></p>
          <p><strong>Category:</strong> <?= htmlspecialchars($row['category']) ?></p>
          <p><strong>Status:</strong>
            <?php if ($row['status'] === 'active'): ?>
              <span class="badge bg-success">Active</span>
            <?php else: ?>
              <span class="badge bg-secondary">Inactive</span>
            <?php endif; ?>
          </p>
          <p><strong>Created:</strong> <?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></p>

          <!-- Edit/Delete Buttons -->
          <div class="d-flex gap-2">
            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">✏️ Edit</a>
            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">🗑 Delete</a>
          </div>

          <hr>

          <!-- Comments Section -->
          <h6 class="mt-3">💬 Comments:</h6>
          <?php
          $commentStmt = $pdo->prepare("SELECT * FROM comments WHERE review_id = ?");
          $commentStmt->execute([$row['id']]);
          while ($comment = $commentStmt->fetch()) {
            echo "<p class='text-muted small'>– " . htmlspecialchars($comment['comment']) . "</p>";
          }
          ?>

          <!-- Comment Form -->
          <form method="POST" action="add_comment.php" class="mt-2">
            <div class="input-group input-group-sm">
              <input type="hidden" name="review_id" value="<?= $row['id'] ?>">
              <input type="text" name="comment" class="form-control" placeholder="Write a comment..." required>
              <button type="submit" class="btn btn-outline-primary">Post</button>
            </div>
          </form>

        </div>
      </div>
    </div>
    <?php } ?>
  </div>
</div>

</body>
</html>
