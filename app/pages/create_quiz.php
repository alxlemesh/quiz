<?php
session_start();
require_once '../utils/composers.php';
require_once '../utils/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $questions   = $_POST['questions'] ?? [];

    if ($name === '') {
        $error = 'Quiz name is required.';
    } elseif (empty($questions)) {
        $error = 'Add at least one question.';
    } else {
        $quizId = insertQuiz($name, $description);

        foreach ($questions as $q) {
            $qText   = trim($q['text'] ?? '');
            $answers = $q['answers'] ?? [];
            $correctIndex = $q['correct'] ?? -1;
            if ($qText === '' || empty($answers)) continue;
            $formattedAnswers = [];
            foreach ($answers as $index => $a) {
                $formattedAnswers[] = [
                    'text' => trim($a['text'] ?? ''),
                    'correct' => ($index == $correctIndex)
                ];
            }
            $answersJson = composeAnswers($formattedAnswers);
            insertQuestion($quizId, $qText, $answersJson);
        }

        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz</title>
    <link rel="stylesheet" href="../styles/create_quiz.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="page-title">Create Quiz</h1>
            <a href="index.php" class="btn btn-primary btn-sm">Back to Menu</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" id="quizForm">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="name" class="form-label">Quiz Name</label>
                        <input type="text" class="form-control" id="name" name="name" required maxlength="30">
                    </div>
                    <div class="form-group">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <div id="questions-container"></div>

            <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addQuestion()">+ Add Question</button>

            <div class="actions">
                <button type="submit" class="btn btn-success">Save Quiz</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        let questionIndex = 0;

        function addQuestion() {
            const container = document.getElementById('questions-container');
            const qi = questionIndex++;
            const block = document.createElement('div');
            block.className = 'card question-card';
            block.id = 'question-' + qi;
            block.innerHTML = `
                <button type="button" class="remove-question-btn" onclick="this.closest('.card').remove()">&#10005;</button>
                <div class="card-body">
                    <h6 class="card-title">Question ${qi + 1}</h6>
                    <div class="form-group">
                        <label class="form-label">Question Text</label>
                        <input type="text" class="form-control" name="questions[${qi}][text]" required>
                    </div>
                    <div id="answers-${qi}">
                        ${answerRow(qi, 0)}
                        ${answerRow(qi, 1)}
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addAnswer(${qi})">+ Add Answer</button>
                </div>
            `;
            container.appendChild(block);
        }

        function answerRow(qi, ai) {
            return `
            <div class="answer-row">
                <input type="text" class="answer-input" name="questions[${qi}][answers][${ai}][text]" placeholder="Answer ${ai + 1}" required>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="questions[${qi}][correct]" value="${ai}" id="q${qi}a${ai}">
                    <label class="form-check-label" for="q${qi}a${ai}">Correct</label>
                </div>
                <button type="button" class="remove-answer-btn" onclick="this.closest('.answer-row').remove()">&times;</button>
            </div>
            `;
        }

        function addAnswer(qi) {
            const cnt = document.querySelectorAll(`#answers-${qi} .answer-row`).length;
            const div = document.getElementById('answers-' + qi);
            div.insertAdjacentHTML('beforeend', answerRow(qi, cnt));
        }

        addQuestion();
    </script>
</body>
</html>
