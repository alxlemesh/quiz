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
    <link rel="stylesheet" href="../styles/index.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="page-title">Quiz Menu</h1>
            <div class="user-info">
                <span class="user-greeting">Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                <a href="results.php" class="btn btn-results">My Results</a>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                    <a href="create_quiz.php" class="btn btn-primary">+ Create Quiz</a>
                <?php endif; ?>
                <a href="../utils/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <?php if (empty($quizzes)): ?>
            <div class="empty-state">
                <p>No quizzes yet</p>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                    <a href="create_quiz.php" class="btn btn-primary">+ Create Quiz</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="quiz-grid">
                <?php foreach ($quizzes as $quiz): ?>
                    <div class="quiz-card">
                        <div class="quiz-card-body">
                            <div class="quiz-header">
                                <h5 class="quiz-title"><?= htmlspecialchars($quiz['name']) ?></h5>
                                <span class="quiz-badge"><?= (int)$quiz['question_count'] ?> question<?= $quiz['question_count'] != 1 ? 's' : '' ?></span>
                            </div>
                            <p class="quiz-description"><?= htmlspecialchars($quiz['description']) ?></p>
                            <div class="quiz-footer">
                                <span class="quiz-date"><?= htmlspecialchars($quiz['date_added']) ?></span>
                                <a href="solve.php?id=<?= (int)$quiz['id'] ?>" class="btn btn-success">Solve</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>