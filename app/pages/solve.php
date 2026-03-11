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

        // Find correct answer indices
        $correctIndices = [];
        foreach ($q['parsed_answers'] as $i => $a) {
            if ($a['correct']) {
                $correctIndices[] = (string)$i;
            }
        }

        // Check if user's selection matches correct answers exactly
        sort($selected);
        sort($correctIndices);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-light">
    <div class="container py-4" style="max-width:900px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0"><?= htmlspecialchars($quiz['name']) ?></h1>
            <a href="index.php" class="btn btn-primary btn-sm">Back to Menu</a>
        </div>

        <?php if ($quiz['description']): ?>
            <p class="text-muted mb-4"><?= htmlspecialchars($quiz['description']) ?></p>
        <?php endif; ?>

        <?php if ($submitted): ?>

            <div class="alert <?= $percent >= 70 ? 'alert-success' : 'alert-danger' ?> text-center py-4 mb-4">
                <h2 class="h4 fw-bold">Your Score: <?= $score ?> / <?= $totalQuestions ?> (<?= $percent ?>%)</h2>
                <p class="mb-0"><?= $percent >= 70 ? 'Great job!' : 'Keep practicing!' ?></p>
            </div>

            <div class="d-flex flex-column gap-3 mb-4">
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
                    <div class="card shadow-sm border-start border-4 <?= $isCorrect ? 'border-success' : 'border-danger' ?>">
                        <div class="card-body">
                            <h6 class="d-flex align-items-center gap-2 fw-bold mb-3">
                                <span class="text-primary"><?= $qi + 1 ?>.</span>
                                <?= htmlspecialchars($q['quiestion']) ?>
                                <span class="ms-auto fs-5 <?= $isCorrect ? 'text-success' : 'text-danger' ?>"><?= $isCorrect ? '&#10003;' : '&#10007;' ?></span>
                            </h6>
                            <div class="d-flex flex-column gap-1">
                                <?php foreach ($q['parsed_answers'] as $ai => $a): ?>
                                    <?php
                                        $wasSelected = in_array((string)$ai, $userAnswers[$q['id']] ?? []);
                                        $isAnswerCorrect = $a['correct'];
                                        $cls = '';
                                        if ($isAnswerCorrect) $cls = 'bg-success-subtle text-success fw-semibold';
                                        elseif ($wasSelected && !$isAnswerCorrect) $cls = 'bg-danger-subtle text-danger fw-semibold';
                                    ?>
                                    <div class="d-flex align-items-center gap-2 rounded px-3 py-2 <?= $cls ?>">
                                        <span class="small text-body-tertiary">
                                            <?php if ($wasSelected): ?>&#9679;<?php else: ?>&#9675;<?php endif; ?>
                                        </span>
                                        <?= htmlspecialchars($a['text']) ?>
                                        <?php if ($isAnswerCorrect): ?>
                                            <span class="ms-auto small fw-semibold text-success">&#10003; correct</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="d-flex gap-2">
                <a href="solve.php?id=<?= $quizId ?>" class="btn btn-success">Try Again</a>
                <a href="index.php" class="btn btn-primary">Back to Menu</a>
            </div>

        <?php elseif (empty($questions)): ?>
            <div class="text-center bg-white rounded shadow-sm p-5">
                <p class="text-muted fs-5 mb-3">This quiz has no questions yet.</p>
                <a href="index.php" class="btn btn-primary">Back to Menu</a>
            </div>

        <?php else: ?>
            <form method="POST" class="d-flex flex-column gap-3 mb-4">
                <?php foreach ($questions as $qi => $q): ?>
                    <?php
                        $hasMultipleCorrect = 0;
                        foreach ($q['parsed_answers'] as $a) {
                            if ($a['correct']) $hasMultipleCorrect++;
                        }
                        $isMulti = $hasMultipleCorrect > 1;
                    ?>
                    <div class="card shadow-sm border-start border-4 border-primary">
                        <div class="card-body">
                            <h6 class="fw-bold mb-2"><span class="text-primary"><?= $qi + 1 ?>.</span> <?= htmlspecialchars($q['quiestion']) ?></h6>
                            <?php if ($isMulti): ?>
                                <small class="text-muted fst-italic d-block mb-2">Select all that apply</small>
                            <?php endif; ?>
                            <div class="d-flex flex-column gap-1">
                                <?php foreach ($q['parsed_answers'] as $ai => $a): ?>
                                    <label class="d-flex align-items-center gap-2 rounded px-3 py-2 border" style="cursor:pointer;" onmouseover="this.classList.add('bg-light')" onmouseout="this.classList.remove('bg-light')">
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

                <div class="d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-success">Submit Answers</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>