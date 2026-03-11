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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-light">
    <div class="container py-4" style="max-width:900px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Create Quiz</h1>
            <a href="index.php" class="btn btn-primary btn-sm">Back to Menu</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" id="quizForm">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Quiz Name</label>
                        <input type="text" class="form-control" id="name" name="name" required maxlength="30">
                    </div>
                    <div class="mb-0">
                        <label for="description" class="form-label fw-semibold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <div id="questions-container"></div>

            <button type="button" class="btn btn-primary btn-sm mb-3" onclick="addQuestion()">+ Add Question</button>

            <div class="d-flex gap-2 mt-3">
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
            block.className = 'card shadow-sm mb-3 position-relative';
            block.id = 'question-' + qi;
            block.innerHTML = `
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" onclick="this.closest('.card').remove()"></button>
                <div class="card-body">
                    <h6 class="card-title fw-bold text-secondary">Question ${qi + 1}</h6>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Question Text</label>
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
            <div class="d-flex align-items-center gap-2 mb-2 answer-row">
                <input type="text" class="form-control form-control-sm" name="questions[${qi}][answers][${ai}][text]" placeholder="Answer ${ai + 1}" required>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="questions[${qi}][correct]" value="${ai}" id="q${qi}a${ai}">
                    <label class="form-check-label small" for="q${qi}a${ai}">Correct</label>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.answer-row').remove()">&times;</button>
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
