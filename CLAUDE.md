# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PHP-based quiz application running on XAMPP with MySQL backend. Users can create quizzes with multiple-choice questions, solve quizzes, and view their results. Uses session-based authentication and Bootstrap 5 for UI.

## Database Setup

Import the database schema:
```bash
mysql -u root -p quiz < "app/quiz (1).sql"
```

Or via phpMyAdmin: Import `app/quiz (1).sql` into a database named `quiz`.

Database connection configured in `app/utils/database.php`:
- Host: localhost
- User: root
- Password: (empty)
- Database: quiz

## Running the Application

1. Start XAMPP (Apache + MySQL)
2. Navigate to `http://localhost/quiz/app/pages/login.php`
3. Default credentials: username `root`, password `lbhtrnjh`

## Architecture

### Directory Structure

```
app/
├── pages/          # PHP page controllers and views
│   ├── login.php
│   ├── index.php        # Quiz menu (main page)
│   ├── create_quiz.php  # Quiz creation form
│   ├── solve.php        # Quiz solving interface
│   └── results.php      # User results history
├── utils/          # Shared utilities
│   ├── database.php     # All database operations
│   ├── composers.php    # JSON encode/decode for answers
│   └── logout.php       # Session cleanup
├── styles/         # CSS files (Bootstrap used via CDN)
└── quiz (1).sql    # Database schema dump
```

### Authentication Flow

All pages except `login.php` require session authentication:
- Session variables: `$_SESSION['user_id']`, `$_SESSION['username']`
- Unauthenticated users redirected to `login.php`
- Logout handled by `utils/logout.php` (destroys session)

### Database Layer

All database operations centralized in `app/utils/database.php`:
- `getConnection()` - Returns mysqli connection
- `authenticateUser($username, $password)` - Login validation
- `getAllQuizzes()` - Fetch all quizzes with question counts
- `insertQuiz($name, $description)` - Create new quiz, returns quiz ID
- `insertQuestion($quizId, $questionText, $answersJson)` - Add question to quiz
- `getQuizById($quizId)` - Fetch single quiz
- `getQuestionsByQuizId($quizId)` - Fetch questions with parsed answers
- `insertResult($username, $quizId, $score)` - Save quiz attempt
- `getResultsByUsername($username)` - Fetch user's quiz history

### Answer Storage Format

Answers stored as JSON in `questions.answers` column. Format:
```json
[
  {"text": "Answer 1", "correct": false},
  {"text": "Answer 2", "correct": true}
]
```

Helper functions in `app/utils/composers.php`:
- `composeAnswers(array $answers): string` - PHP array → JSON
- `parseAnswers(string $json): array` - JSON → PHP array

### Quiz Solving Logic

In `solve.php`:
- Questions with multiple correct answers show checkboxes
- Questions with single correct answer show radio buttons
- Scoring: User must select ALL correct answers and NO incorrect answers to get point
- Results saved to database after submission

### Database Schema Notes

**Important**: The `questions` table has a typo in column name: `quiestion` (not "question"). This is used throughout the codebase - do not "fix" it without migrating the database.

Tables:
- `users`: id, username, password
- `quiziz`: id, name, description, date_added
- `questions`: id, quiz_id, quiestion, answers (JSON)
- `results`: id, username, quizId, result (score as integer)

### Frontend

- Bootstrap 5.3.8 loaded via CDN
- Vanilla JavaScript for dynamic form building in `create_quiz.php`
- No build process or package manager
- CSS files in `app/styles/` (mostly unused, Bootstrap handles styling)

## Development Notes

- This is a traditional PHP application with no framework
- Each page handles its own routing, validation, and rendering
- Database queries use `mysqli` with `real_escape_string()`
- Sessions managed with PHP's built-in session functions
- No API layer - pages directly query database and render HTML
