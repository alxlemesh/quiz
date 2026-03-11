<?php
session_start();
require_once '../utils/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$results = getResultsByUsername($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-light">
    <div class="container py-4" style="max-width:900px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">My Results</h1>
            <a href="index.php" class="btn btn-primary btn-sm">Back to Menu</a>
        </div>

        <?php if (empty($results)): ?>
            <div class="text-center bg-white rounded shadow-sm p-5">
                <p class="text-muted fs-5 mb-3">You haven't completed any quizzes yet.</p>
                <a href="index.php" class="btn btn-primary">Browse Quizzes</a>
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-muted small text-uppercase">#</th>
                                <th class="text-muted small text-uppercase">Quiz</th>
                                <th class="text-muted small text-uppercase">Score</th>
                                <th class="text-muted small text-uppercase">Percent</th>
                                <th class="text-muted small text-uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $i => $r): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($r['quiz_name']) ?></td>
                                    <td><?= $r['score'] ?> / <?= $r['total_questions'] ?></td>
                                    <td>
                                        <span class="badge rounded-pill <?= $r['percent'] >= 70 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> fw-bold">
                                            <?= $r['percent'] ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <a href="solve.php?id=<?= (int)$r['quizId'] ?>" class="btn btn-success btn-sm">Retake</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
