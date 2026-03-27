# index.php

**Path:** `app/pages/index.php`
**Purpose:** Main menu/dashboard page - displays all available quizzes for authenticated users.

## Line-by-Line Logic

### Lines 1-10: Session, Dependencies, and Authorization

```php
session_start();
require_once '../utils/database.php';
```
- **Line 1:** Starts or resumes PHP session
- **Line 2:** Imports database utility functions (getAllQuizzes, etc.)

```php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
```
- **Line 5:** Checks if user is NOT logged in (no user_id in session)
- **Line 6:** Redirects unauthenticated users to login page
- **Line 7:** Stops script execution

```php
$quizzes = getAllQuizzes();
```
- **Line 10:** Fetches all quizzes from database using utility function

### Lines 12-62: HTML Document Structure

#### Lines 12-20: Document Head
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QUIZ MENU</title>
    <link rel="stylesheet" href="../styles/index.css">
</head>
```
- **Lines 12-20:** Standard HTML5 boilerplate with index page stylesheet

#### Lines 20-32: Header Section
```html
<div class="container">
    <div class="header">
        <h1 class="page-title">Quiz Menu</h1>
```
- **Lines 21-23:** Main container and page title

```html
<div class="user-info">
    <span class="user-greeting">Logged in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
```
- **Lines 24-25:** Displays greeting with logged-in username
- Uses `htmlspecialchars()` to prevent XSS attacks

```php
<a href="results.php" class="btn btn-results">My Results</a>
```
- **Line 26:** Link to user's results page

```php
<?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
    <a href="create_quiz.php" class="btn btn-primary">+ Create Quiz</a>
<?php endif; ?>
```
- **Lines 27-29:** Admin-only button:
  - Checks if `is_admin` session variable exists and is `true`
  - Shows "Create Quiz" button only for administrators

```php
<a href="../utils/logout.php" class="btn btn-danger">Logout</a>
```
- **Line 30:** Logout link pointing to logout utility

### Lines 34-59: Quiz List Content

#### Lines 34-40: Empty State (No Quizzes)
```php
<?php if (empty($quizzes)): ?>
    <div class="empty-state">
        <p>No quizzes yet</p>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
            <a href="create_quiz.php" class="btn btn-primary">+ Create Quiz</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
```
- **Line 34:** Checks if quizzes array is empty
- **Lines 35-40:** Shows empty state message
- **Lines 37-39:** Admins see "Create Quiz" button in empty state

#### Lines 41-59: Quiz Grid (When Quizzes Exist)
```php
<?php else: ?>
    <div class="quiz-grid">
```
- **Lines 41-42:** Opens quiz grid container for non-empty quiz list

```php
<?php foreach ($quizzes as $quiz): ?>
    <div class="quiz-card">
        <div class="quiz-card-body">
```
- **Lines 43-45:** Iterates through each quiz, creating a card

```html
<div class="quiz-header">
    <h5 class="quiz-title"><?= htmlspecialchars($quiz['name']) ?></h5>
    <span class="quiz-badge"><?= (int)$quiz['question_count'] ?> question<?= $quiz['question_count'] != 1 ? 's' : '' ?></span>
</div>
```
- **Lines 46-49:** Quiz header with:
  - Quiz name (escaped for XSS prevention)
  - Question count badge with pluralization logic (adds "s" if count ≠ 1)

```html
<p class="quiz-description"><?= htmlspecialchars($quiz['description']) ?></p>
```
- **Line 50:** Quiz description (escaped)

```html
<div class="quiz-footer">
    <span class="quiz-date"><?= htmlspecialchars($quiz['date_added']) ?></span>
    <a href="solve.php?id=<?= (int)$quiz['id'] ?>" class="btn btn-success">Solve</a>
</div>
```
- **Lines 51-55:** Footer with:
  - Date added (escaped)
  - "Solve" button linking to solve page with quiz ID
  - **Line 54:** Casts ID to integer for URL safety

### Lines 60-62: Closing Tags
```html
</div>
</body>
</html>
```

## Page Flow

1. Verify user is authenticated
2. Fetch all quizzes from database
3. Render header with user info and admin controls
4. If no quizzes: show empty state
5. If quizzes exist: render card for each quiz
6. Each card shows metadata and "Solve" button

## Security Features

- **Line 5:** Authentication check redirects unauthenticated users
- **Line 25, 50, 52:** `htmlspecialchars()` prevents XSS
- **Line 27, 37:** Strict boolean check for admin status (`=== true`)
- **Line 54:** Integer cast on quiz ID in URL

## Access Control

| Feature | User | Admin |
|---------|------|-------|
| View quizzes | ✓ | ✓ |
| Solve quizzes | ✓ | ✓ |
| View results | ✓ | ✓ |
| Create quiz | ✗ | ✓ |
