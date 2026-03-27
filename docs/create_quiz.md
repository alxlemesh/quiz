# create_quiz.php

**Path:** `app/pages/create_quiz.php`
**Purpose:** Admin-only quiz creation page - provides form for creating quizzes with questions and answers.

## Line-by-Line Logic

### Lines 1-10: Session, Dependencies, and Authorization

```php
session_start();
require_once '../utils/database.php';
```
- **Line 1:** Starts or resumes PHP session
- **Line 2:** Imports database utility functions (insertQuiz, insertQuestion, etc.)

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
$quizzes = getAllQuizzes();
```
- **Line 10:** Fetches all quizzes (note: this line appears unused in the template)

### Lines 12-89: HTML Document Structure

#### Lines 12-20: Document Head
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz</title>
    <link rel="stylesheet" href="../styles/create_quiz.css">
</head>
```
- **Lines 12-20:** Standard HTML5 boilerplate with create quiz stylesheet

#### Lines 21-32: Header Section
```html
<div class="container">
    <div class="header">
        <h1 class="page-title">Create Quiz</h1>
        <a href="index.php" class="btn btn-primary btn-sm">Back to Menu</a>
    </div>
```
- **Lines 21-25:** Container with page title and back navigation

### Lines 27-30: Error Display
```php
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
```
- **Note:** Lines reference `$error` variable that is never set in the code (missing validation logic)

### Lines 31-53: Quiz Form
```html
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
```
- **Lines 31-43:** Form with two fields:
  - Quiz name (required, max 30 characters)
  - Description (optional textarea)

```html
<div id="questions-container"></div>
```
- **Line 45:** Empty container for dynamically added questions

```html
<button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addQuestion()">+ Add Question</button>
```
- **Line 47:** Button to add new question via JavaScript

```html
<div class="actions">
    <button type="submit" class="btn btn-success">Save Quiz</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
</div>
```
- **Lines 49-52:** Submit and cancel buttons

### Lines 56-103: JavaScript - Question Management

#### Lines 56-57: State Variable
```javascript
let questionIndex = 0;
```
- Tracks question counter for unique field names

#### Lines 59-81: addQuestion Function
```javascript
function addQuestion() {
    const container = document.getElementById('questions-container');
    const qi = questionIndex++;
    const block = document.createElement('div');
    block.className = 'card question-card';
    block.id = 'question-' + qi;
```
- **Lines 60-65:** Creates new question card element with unique ID

```javascript
block.innerHTML = `
    <button type="button" class="remove-question-btn" onclick="this.closest('.card').remove()">&#10005;</button>
    <div class="card-body">
        <h6 class="card-title">Question ${qi + 1}</h6>
```
- **Lines 66-69:** Inner HTML with remove button and question number

```javascript
<div class="form-group">
    <label class="form-label">Question Text</label>
    <input type="text" class="form-control" name="questions[${qi}][text]" required>
</div>
```
- **Lines 70-73:** Question text input with indexed name

```javascript
<div id="answers-${qi}">
    ${answerRow(qi, 0)}
    ${answerRow(qi, 1)}
</div>
<button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addAnswer(${qi})">+ Add Answer</button>
```
- **Lines 74-78:** Answers container with 2 default answers and "Add Answer" button

```javascript
container.appendChild(block);
```
- **Line 79:** Appends question card to container

#### Lines 83-94: answerRow Function
```javascript
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
```
- **Lines 84-93:** Generates HTML for one answer row:
  - Text input for answer
  - Radio button to mark as correct
  - Remove button

#### Lines 96-100: addAnswer Function
```javascript
function addAnswer(qi) {
    const cnt = document.querySelectorAll(`#answers-${qi} .answer-row`).length;
    const div = document.getElementById('answers-' + qi);
    div.insertAdjacentHTML('beforeend', answerRow(qi, cnt));
}
```
- **Lines 97-100:** Adds new answer row to specified question

#### Line 102: Initialize First Question
```javascript
addQuestion();
```
- **Line 102:** Automatically adds first question on page load

### Lines 104-127: POST Structure Documentation

```html
<!-- $_POST = [ ... ] -->
```
- **Lines 104-127:** HTML comment documenting expected POST data structure:
```php
[
    'name' => 'Quiz Name',
    'description' => 'Optional description',
    'questions' => [
        0 => [
            'text' => 'First question text',
            'answers' => [
                0 => ['text' => 'Answer 1'],
                1 => ['text' => 'Answer 2'],
            ],
            'correct' => 0  // Index of correct answer
        ],
        // ... more questions
    ]
]
```

### Lines 128-130: Empty Script Block
```html
<script>
  //!object structure for post request(b.b most cummon question)
</script>
```
- **Lines 128-130:** Empty script with comment (appears to be placeholder or debugging artifact)

## Form Data Structure

When submitted, POST data is structured as:

| Field | Type | Description |
|-------|------|-------------|
| `name` | string | Quiz name (max 30 chars) |
| `description` | string | Optional description |
| `questions[n][text]` | string | Question text |
| `questions[n][answers][m][text]` | string | Answer text |
| `questions[n][correct]` | integer | Index of correct answer |

## User Flow

1. Admin accesses create quiz page
2. Enters quiz name and optional description
3. Clicks "Add Question" (or uses auto-added first question)
4. For each question:
   - Enters question text
   - Adds 2+ answers (default: 2)
   - Marks one answer as correct via radio button
   - Can remove questions/answers with X buttons
5. Submits form to save quiz
6. Redirected to main menu

## Security Concerns

- **No admin check:** Page doesn't verify `$_SESSION['is_admin']` before showing form
- **No CSRF token:** Form vulnerable to cross-site request forgery
- **No server-side validation:** PHP processing code not visible in this file
- **XSS prevention:** Uses `htmlspecialchars()` in error display (line 28)

## Notes

- Line 10 (`$quizzes = getAllQuizzes()`) appears unused
- Error variable `$error` is referenced but never defined in this file
- Success handling and form processing logic may be in a separate file or missing
