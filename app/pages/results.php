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
    <link rel="stylesheet" href="../styles/results.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="page-title">My Results</h1>
            <a href="index.php" class="btn btn-primary">Back to Menu</a>
        </div>

        <?php if (empty($results)): ?>
            <div class="empty-state">
                <p>You haven't completed any quizzes yet.</p>
                <a href="index.php" class="btn btn-primary">Browse Quizzes</a>
            </div>
        <?php else: ?>
            <div class="results-card">
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Quiz</th>
                            <th>Score</th>
                            <th>Percent</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $i => $r): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($r['quiz_name']) ?></td>
                                <td><?= $r['score'] ?> / <?= $r['total_questions'] ?></td>
                                <td>
                                    <span class="badge <?= $r['percent'] >= 70 ? 'badge-success' : 'badge-danger' ?>">
                                        <?= $r['percent'] ?>%
                                    </span>
                                </td>
                                <td>
                                    <a href="solve.php?id=<?= (int)$r['quizId'] ?>" class="btn btn-success">Retake</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
