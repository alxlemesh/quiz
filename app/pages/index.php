<?php
session_start();
require_once '../utils/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$quizzes = getAllQuizzes();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QUIZ MENU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <h1 class="h3 mb-0">Quiz Menu</h1>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="text-muted small">Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                <a href="results.php" class="btn btn-sm" style="background:#8e44ad;color:#fff;">My Results</a>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                    <a href="create_quiz.php" class="btn btn-primary btn-sm">+ Create Quiz</a>
                <?php endif; ?>
                <a href="../utils/logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>

        <?php if (empty($quizzes)): ?>
            <div class="text-center bg-white rounded shadow-sm p-5">
                <p class="text-muted fs-5 mb-3">No quizzes yet. Be the first to create one!</p>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                    <a href="create_quiz.php" class="btn btn-primary">+ Create Quiz</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($quizzes as $quiz): ?>
                    <div class="col-sm-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                    <h5 class="card-title mb-0" style="word-break:break-word;"><?= htmlspecialchars($quiz['name']) ?></h5>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill"><?= (int)$quiz['question_count'] ?> question<?= $quiz['question_count'] != 1 ? 's' : '' ?></span>
                                </div>
                                <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars($quiz['description']) ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-body-tertiary"><?= htmlspecialchars($quiz['date_added']) ?></small>
                                    <a href="solve.php?id=<?= (int)$quiz['id'] ?>" class="btn btn-success btn-sm">Solve</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>