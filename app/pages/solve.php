<?php
session_start();
require_once '../utils/composers.php';
require_once '../utils/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$quizId = (int)($_GET['id'] ?? 0);
if ($quizId <= 0) {
    header('Location: index.php');
    exit;
}

$quiz = getQuizById($quizId);
if (!$quiz) {
    header('Location: index.php');
    exit;
}

$questions = getQuestionsByQuizId($quizId);
$totalQuestions = count($questions);
$submitted = false;
$score = 0;
$userAnswers = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted = true;

    foreach ($questions as $q) {
        $qId = $q['id'];
        $selected = $_POST['q_' . $qId] ?? [];
        if (!is_array($selected)) $selected = [$selected];
        $userAnswers[$qId] = $selected;

        $correctIndices = [];
        foreach ($q['parsed_answers'] as $i => $a) {
          /* [
    ['text' => 'Paris', 'correct' => true],
    ['text' => 'London', 'correct' => false],
    ['text' => 'Berlin', 'correct' => true]
]*/
            if ($a['correct']) {
                $correctIndices[] = (string)$i;
            }
        }
        //na czas terazniejszy istnieje mozliwosc robienia pytan tylko z 1 odpowiedzia, a logika wyzej jest zrobiona dla prostszego rozwijecia logiki w przyszlosci

        // Check if users selection matches correct answers exactly
        sort($selected);
        sort($correctIndices);
        /* Without sorting, ["2", "0"] !== ["0", "2"] even though they contain the same values. Sorting ensures the order doesn't matter — only the actual selections do.

Result: If the arrays match exactly, the user gets 1 point added to their score.*/
        if ($selected === $correctIndices) {
            $score++;
        }
    }

    insertResult($_SESSION['username'], $quizId, $score);
}

$percent = $totalQuestions > 0 ? round(($score / $totalQuestions) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solve: <?= htmlspecialchars($quiz['name']) ?></title>
    <link rel="stylesheet" href="../styles/solve.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="page-title"><?= htmlspecialchars($quiz['name']) ?></h1>
            <a href="index.php" class="btn btn-primary">Back to Menu</a>
        </div>
        <?php if ($quiz['description']): ?>
            <p class="quiz-description"><?= htmlspecialchars($quiz['description']) ?></p>
        <?php endif; ?>

        <?php if ($submitted): ?>

            <div class="alert <?= $percent >= 70 ? 'alert-success' : 'alert-danger' ?>">
                <h2 class="score-title">Your Score: <?= $score ?> / <?= $totalQuestions ?> (<?= $percent ?>%)</h2>
            </div>

            <div class="questions-list">
                <?php foreach ($questions as $qi => $q): ?>
                    <?php
                        $selected = $userAnswers[$q['id']] ?? [];
                        $correctIndices = [];
                        foreach ($q['parsed_answers'] as $i => $a) {
                            if ($a['correct']) $correctIndices[] = (string)$i;
                        }
                        sort($selected);
                        sort($correctIndices);
                        $isCorrect = ($selected === $correctIndices);
                    ?>
                    <div class="question-card <?= $isCorrect ? 'correct' : 'incorrect' ?>">
                        <div class="question-card-body">
                            <h6 class="question-header">
                                <span class="question-number"><?= $qi + 1 ?>.</span>
                                <?= htmlspecialchars($q['quiestion']) ?>
                                <span class="question-status <?= $isCorrect ? 'correct' : 'incorrect' ?>"><?= $isCorrect ? '&#10003;' : '&#10007;' ?></span>
                            </h6>
                            <div class="answers-list">
                                <?php foreach ($q['parsed_answers'] as $ai => $a): ?>
                                    <?php
                                        $wasSelected = in_array((string)$ai, $userAnswers[$q['id']] ?? []);
                                        $isAnswerCorrect = $a['correct'];
                                        $cls = '';
                                        if ($isAnswerCorrect) $cls = 'correct';
                                        elseif ($wasSelected && !$isAnswerCorrect) $cls = 'incorrect';
                                    ?>
                                    <div class="answer-option <?= $cls ?> <?= $wasSelected ? 'selected' : '' ?>">
                                        <span><?= htmlspecialchars($a['text']) ?></span>
                                        <?php if ($isAnswerCorrect): ?>
                                            <span class="answer-correct-mark">&#10003; correct</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="actions">
                <a href="solve.php?id=<?= $quizId ?>" class="btn btn-success">Try Again</a>
                <a href="index.php" class="btn btn-primary">Back to Menu</a>
            </div>

        <?php elseif (empty($questions)): ?>
            <div class="empty-state">
                <p>This quiz has no questions yet.</p>
                <a href="index.php" class="btn btn-primary">Back to Menu</a>
            </div>

        <?php else: ?>
            <form method="POST" class="questions-form">
                <?php foreach ($questions as $qi => $q): ?>
                    <?php
                        $hasMultipleCorrect = 0;
                        foreach ($q['parsed_answers'] as $a) {
                            if ($a['correct']) $hasMultipleCorrect++;
                        }
                        $isMulti = $hasMultipleCorrect > 1;
                    ?>
                    <div class="question-card">
                        <div class="question-card-body">
                            <h6 class="question-header">
                                <span class="question-number"><?= $qi + 1 ?>.</span>
                                <?= htmlspecialchars($q['quiestion']) ?>
                            </h6>
                            <?php if ($isMulti): ?>
                                <span class="question-hint">Select all that apply</span>
                            <?php endif; ?>
                            <div class="answers-list">
                                <?php foreach ($q['parsed_answers'] as $ai => $a): ?>
                                    <label class="answer-option">
                                        <?php if ($isMulti): ?>
                                            <input class="form-check-input" type="checkbox" name="q_<?= $q['id'] ?>[]" value="<?= $ai ?>">
                                        <?php else: ?>
                                            <input class="form-check-input" type="radio" name="q_<?= $q['id'] ?>[]" value="<?= $ai ?>" required>
                                        <?php endif; ?>
                                        <span><?= htmlspecialchars($a['text']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="actions">
                    <button type="submit" class="btn btn-success">Submit Answers</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
