# results.php

**Path:** `app/pages/results.php`
**Purpose:** User results page - displays quiz history with scores and percentages.

## Line-by-Line Logic

### Lines 1-10: Session, Dependencies, and Authorization

```php
session_start();
require_once '../utils/database.php';
```
- **Line 1:** Starts or resumes PHP session
- **Line 2:** Imports database utility functions (getResultsByUsername, etc.)

```php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
```
- **Line 5:** Checks if user is NOT logged in
- **Line 6:** Redirects unauthenticated users to login page
- **Line 7:** Stops script execution

```php
$results = getResultsByUsername($_SESSION['username']);
```
- **Line 10:** Fetches all results for the logged-in user from database

### Lines 12-66: HTML Document Structure

#### Lines 12-20: Document Head
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Results</title>
    <link rel="stylesheet" href="../styles/results.css">
</head>
```
- **Lines 12-20:** Standard HTML5 boilerplate with results page stylesheet

#### Lines 21-25: Header Section
```html
<div class="container">
    <div class="header">
        <h1 class="page-title">My Results</h1>
        <a href="index.php" class="btn btn-primary">Back to Menu</a>
    </div>
```
- **Lines 21-25:** Container with page title and navigation back to main menu

### Lines 27-31: Empty State (No Results)
```php
<?php if (empty($results)): ?>
    <div class="empty-state">
        <p>You haven't completed any quizzes yet.</p>
        <a href="index.php" class="btn btn-primary">Browse Quizzes</a>
    </div>
<?php endif; ?>
```
- **Line 27:** Checks if results array is empty
- **Lines 28-31:** Shows empty state with encouragement and link to browse quizzes

### Lines 32-63: Results Table (When Results Exist)

#### Lines 32-33: Results Card Container
```php
<?php else: ?>
    <div class="results-card">
```
- **Lines 32-33:** Opens results card for non-empty results

#### Lines 34-43: Table Structure and Headers
```html
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
```
- **Lines 34-43:** Table with 5 columns:
  - `#` - Attempt number
  - `Quiz` - Quiz name
  - `Score` - Correct answers / Total questions
  - `Percent` - Percentage score with badge
  - `Action` - Retake button

#### Lines 44-61: Table Body - Results Iteration
```php
<tbody>
    <?php foreach ($results as $i => $r): ?>
        <tr>
```
- **Lines 44-46:** Iterates through results, `$i` is index (0-based)

```html
<td><?= $i + 1 ?></td>
```
- **Line 47:** Displays attempt number (1-based, hence `+ 1`)

```html
<td><?= htmlspecialchars($r['quiz_name']) ?></td>
```
- **Line 48:** Displays quiz name with XSS protection

```html
<td><?= $r['score'] ?> / <?= $r['total_questions'] ?></td>
```
- **Line 49:** Displays score as fraction (e.g., "3 / 5")
- Values are integers from database, no escaping needed

```html
<td>
    <span class="badge <?= $r['percent'] >= 70 ? 'badge-success' : 'badge-danger' ?>">
        <?= $r['percent'] ?>%
    </span>
</td>
```
- **Lines 50-54:** Percentage display with conditional styling:
  - **Line 51:** Ternary operator assigns CSS class:
    - `badge-success` if percent ≥ 70 (passing)
    - `badge-danger` if percent < 70 (failing)
  - **Line 53:** Displays percentage value

```html
<td>
    <a href="solve.php?id=<?= (int)$r['quizId'] ?>" class="btn btn-success">Retake</a>
</td>
```
- **Lines 55-57:** Retake button:
  - Links to solve page with quiz ID
  - **Line 56:** Casts ID to integer for URL safety

```php
<?php endforeach; ?>
</tbody>
```
- **Line 59:** Closes foreach loop
- **Line 60:** Closes table body

### Lines 62-66: Closing Tags
```html
</div>
</body>
</html>
```

## Data Structure

Each result record contains:
- `result_id` - Unique result identifier
- `score` - Number of correct answers
- `quizId` - Reference to quiz
- `quiz_name` - Human-readable quiz name
- `total_questions` - Total questions in quiz
- `percent` - Calculated: `(score / total_questions) * 100`

## Display Logic

| Score Range | Badge Class | Visual |
|-------------|-------------|--------|
| ≥ 70% | `badge-success` | Green (pass) |
| < 70% | `badge-danger` | Red (fail) |

## Security Features

- **Line 5:** Authentication check protects results privacy
- **Line 48:** `htmlspecialchars()` prevents XSS in quiz names
- **Line 56:** Integer cast on quiz ID prevents URL injection
- **Line 10:** Results filtered by session username (users see only their own)

## UX Features

- Empty state provides clear call-to-action
- Attempt numbering helps users track progress
- Color-coded percentages give immediate visual feedback
- Retake button enables quick retry
