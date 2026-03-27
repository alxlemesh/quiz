# solve.php

**Path:** `app/pages/solve.php`
**Purpose:** Quiz solving page - displays questions, handles answer submission, and shows results with detailed feedback.

## Line-by-Line Logic

### Lines 1-9: Session, Dependencies, and Authorization

```php
session_start();
require_once '../utils/composers.php';
require_once '../utils/database.php';
```
- **Line 1:** Starts or resumes PHP session
- **Line 2:** Imports composer utilities (for answer processing)
- **Line 3:** Imports database utility functions

```php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
```
- **Line 6:** Checks if user is NOT logged in
- **Line 7:** Redirects unauthenticated users to login page
- **Line 8:** Stops script execution

### Lines 11-24: Quiz Loading and Validation

```php
$quizId = (int)($_GET['id'] ?? 0);
if ($quizId <= 0) {
    header('Location: index.php');
    exit;
}
```
- **Line 11:** Gets quiz ID from URL query parameter, casts to integer
- **Lines 12-15:** Validates ID is positive; redirects if invalid

```php
$quiz = getQuizById($quizId);
if (!$quiz) {
    header('Location: index.php');
    exit;
}
```
- **Line 17:** Fetches quiz data from database
- **Lines 18-21:** Redirects if quiz doesn't exist

```php
$questions = getQuestionsByQuizId($quizId);
$totalQuestions = count($questions);
```
- **Line 23:** Fetches all questions for this quiz
- **Line 24:** Counts total questions for scoring

### Lines 25-27: State Variables
```php
$submitted = false;
$score = 0;
$userAnswers = [];
```
- **Line 25:** Tracks if form has been submitted
- **Line 26:** Initializes score counter
- **Line 27:** Initializes array to store user's answers

### Lines 29-63: POST Handler (Form Submission)

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted = true;
```
- **Lines 29-30:** Detects form submission, sets flag

```php
foreach ($questions as $q) {
    $qId = $q['id'];
    $selected = $_POST['q_' . $qId] ?? [];
    if (!is_array($selected)) $selected = [$selected];
    $userAnswers[$qId] = $selected;
```
- **Lines 32-36:** For each question:
  - Gets selected answer(s) from POST
  - Normalizes to array (handles single checkbox vs multiple)
  - Stores in userAnswers array

```php
$correctIndices = [];
foreach ($q['parsed_answers'] as $i => $a) {
    if ($a['correct']) {
        $correctIndices[] = (string)$i;
    }
}
```
- **Lines 38-48:** Builds array of correct answer indices:
  - Iterates through parsed answers
  - Collects indices where `correct` is true
  - Stores as strings for comparison

> **Lines 40-44:** Comment showing expected data structure:
> ```php
> [
>   ['text' => 'Paris', 'correct' => true],
>   ['text' => 'London', 'correct' => false],
>   ...
> ]
> ```

```php
//na czas terazniejszy istnieje mozliwosc robienia pytan tylko z 1 odpowiedzia...
```
- **Line 49:** Polish comment: "Currently only single-answer questions are possible, but logic above is prepared for future expansion"

```php
sort($selected);
sort($correctIndices);
```
- **Lines 52-53:** Sorts both arrays for accurate comparison
- **Line 54-56:** Comment explains: without sorting, `["2", "0"] !== ["0", "2"]`

```php
if ($selected === $correctIndices) {
    $score++;
}
```
- **Lines 57-59:** Compares arrays with strict equality; increments score if exact match

```php
insertResult($_SESSION['username'], $quizId, $score);
```
- **Line 62:** Saves result to database

### Line 65: Percentage Calculation
```php
$percent = $totalQuestions > 0 ? round(($score / $totalQuestions) * 100) : 0;
```
- Calculates percentage score, handles division by zero

### Lines 67-186: HTML Document Structure

#### Lines 67-74: Document Head
```html
<head>
    <title>Solve: <?= htmlspecialchars($quiz['name']) ?></title>
    <link rel="stylesheet" href="../styles/solve.css">
</head>
```
- Dynamic title with quiz name (escaped)

#### Lines 76-83: Header and Description
```html
<h1 class="page-title"><?= htmlspecialchars($quiz['name']) ?></h1>
<a href="index.php" class="btn btn-primary">Back to Menu</a>
```
- **Lines 78-80:** Page title and navigation
- **Lines 81-83:** Conditional description display

### Lines 85-136: Submitted State (Results Display)

```php
<?php if ($submitted): ?>
    <div class="alert <?= $percent >= 70 ? 'alert-success' : 'alert-danger' ?>">
        <h2 class="score-title">Your Score: <?= $score ?> / <?= $totalQuestions ?> (<?= $percent ?>%)</h2>
    </div>
```
- **Lines 85-89:** Shows score banner with conditional coloring:
  - Green (`alert-success`) if ≥ 70%
  - Red (`alert-danger`) if < 70%

#### Lines 91-130: Question Review Cards
```php
<?php foreach ($questions as $qi => $q): ?>
    <?php
        $selected = $userAnswers[$q['id']] ?? [];
        // ... recalculate correctIndices and isCorrect
    ?>
    <div class="question-card <?= $isCorrect ? 'correct' : 'incorrect' ?>">
```
- **Lines 92-103:** For each question:
  - Recalculates correctness for display
  - Applies CSS class based on result

```html
<h6 class="question-header">
    <span class="question-number"><?= $qi + 1 ?>.</span>
    <?= htmlspecialchars($q['quiestion']) ?>
    <span class="question-status <?= $isCorrect ? 'correct' : 'incorrect' ?>">
        <?= $isCorrect ? '&#1003;' : '&#10007;' ?>
    </span>
</h6>
```
- **Lines 105-109:** Question header with:
  - Question number
  - Question text (note: column name `quiestion` is a typo)
  - Checkmark (✓) or X symbol

```html
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
```
- **Lines 111-125:** For each answer option:
  - Determines if user selected it
  - Determines if it's the correct answer
  - Applies CSS classes:
    - `correct` -正确答案 (green)
    - `incorrect` - Wrong selection (red)
    - `selected` - User's choice
  - Shows checkmark on correct answers

#### Lines 132-135: Post-Results Actions
```html
<a href="solve.php?id=<?= $quizId ?>" class="btn btn-success">Try Again</a>
<a href="index.php" class="btn btn-primary">Back to Menu</a>
```
- **Lines 133-135:** Buttons to retry or return to menu

### Lines 137-141: Empty Questions State
```php
<?php elseif (empty($questions)): ?>
    <div class="empty-state">
        <p>This quiz has no questions yet.</p>
        <a href="index.php" class="btn btn-primary">Back to Menu</a>
    </div>
```
- **Lines 137-141:** Shows when quiz exists but has no questions

### Lines 143-183: Question Form (Before Submission)

```php
<?php else: ?>
    <form method="POST" class="questions-form">
```
- **Lines 143-144:** Opens form for taking quiz

#### Lines 145-176: Question Cards
```php
<?php foreach ($questions as $qi => $q): ?>
    <?php
        $hasMultipleCorrect = 0;
        foreach ($q['parsed_answers'] as $a) {
            if ($a['correct']) $hasMultipleCorrect++;
        }
        $isMulti = $hasMultipleCorrect > 1;
    ?>
```
- **Lines 146-152:** Counts correct answers to determine input type:
  - `isMulti = true` → checkboxes (multiple select)
  - `isMulti = false` → radio buttons (single select)

```html
<h6 class="question-header">
    <span class="question-number"><?= $qi + 1 ?>.</span>
    <?= htmlspecialchars($q['quiestion']) ?>
</h6>
<?php if ($isMulti): ?>
    <span class="question-hint">Select all that apply</span>
<?php endif; ?>
```
- **Lines 155-161:** Question header with conditional hint

```html
<?php if ($isMulti): ?>
    <input type="checkbox" name="q_<?= $q['id'] ?>[]" value="<?= $ai ?>">
<?php else: ?>
    <input type="radio" name="q_<?= $q['id'] ?>[]" value="<?= $ai ?>" required>
<?php endif; ?>
```
- **Lines 165-169:** Input type based on question:
  - Checkbox for multiple-answer questions
  - Radio for single-answer (required)

#### Lines 178-182: Form Actions
```html
<button type="submit" class="btn btn-success">Submit Answers</button>
<a href="index.php" class="btn btn-secondary">Cancel</a>
```
- Submit and cancel buttons

## Scoring Logic

1. User selects answers for each question
2. On submit, selected indices are compared to correct indices
3. Arrays are sorted before comparison (order-independent)
4. Exact match = 1 point
5. Score saved to database, percentage calculated
6. Results displayed with visual feedback

## Security Features

- **Line 6:** Authentication required
- **Line 11, 56:** Integer casts prevent injection
- **Lines 78, 82, 107, 120:** `htmlspecialchars()` prevents XSS
- **Line 62:** Result saved with session username (user can't fake identity)
