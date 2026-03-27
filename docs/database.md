# database.php

**Path:** `app/utils/database.php`
**Purpose:** Core database utility file providing all database operations for the quiz application.

## Line-by-Line Logic

### Lines 1-11: Database Connection
```php
function getConnection(): mysqli
```
- **Line 6:** Creates new MySQLi connection to localhost with credentials (`root`, empty password, database `quiz`)
- **Line 7-9:** Checks for connection error and terminates with error message if failed
- **Line 10:** Returns the established connection object

### Lines 14-21: User Authentication
```php
function authenticateUser(string $username, string $password): ?array
```
- **Line 16:** Establishes database connection
- **Line 17:** Executes SQL query to find user matching username and password (uses `real_escape_string` for basic sanitization)
- **Line 18:** Fetches user data if exactly one row found, otherwise null
- **Line 19:** Closes database connection
- **Line 20:** Returns user array (with id, username, is_admin) or null

### Lines 24-43: Fetch All Quizzes
```php
function getAllQuizzes(): array
```
- **Line 26:** Establishes database connection
- **Lines 27-34:** Executes SQL JOIN query to get quizzes with their question counts from `quiziz` and `questions` tables
- **Lines 35-40:** Iterates through result set, building array of quiz data
- **Line 41:** Closes connection
- **Line 42:** Returns array of all quizzes with metadata

### Lines 46-53: Insert New Quiz
```php
function insertQuiz(string $name, string $description): int
```
- **Line 48:** Establishes database connection
- **Line 49:** Inserts new quiz record into `quiziz` table with escaped name and description
- **Line 50:** Captures the auto-generated insert ID
- **Line 51:** Closes connection
- **Line 52:** Returns the new quiz ID

### Lines 56-74: Insert Question with Answers
```php
function insertQuestion(int $quizId, string $questionText, string $answersJson): void
```
- **Line 60:** Establishes database connection
- **Line 62:** Escapes question text for SQL safety
- **Line 63:** Inserts question into `questions` table (note: column name `quiestion` appears to be a typo)
- **Line 64:** Gets the inserted question ID
- **Line 66:** Parses the JSON answers string into array
- **Lines 67-71:** Iterates through answers, inserting each into `answers` table with `is_correct` flag (1 or 0)
- **Line 73:** Closes connection

### Lines 77-84: Get Quiz by ID
```php
function getQuizById(int $quizId): ?array
```
- **Line 79:** Establishes database connection
- **Line 80:** Queries `quiziz` table for quiz with matching ID
- **Line 81:** Fetches associative array result
- **Line 82:** Closes connection
- **Line 83:** Returns quiz data or null if not found

### Lines 87-115: Get Questions by Quiz ID
```php
function getQuestionsByQuizId(int $quizId): array
```
- **Line 89:** Establishes database connection
- **Line 90:** Queries all questions for the given quiz ID
- **Lines 92-110:** For each question:
  - **Lines 95-105:** Nested query fetches all answers from `answers` table
  - **Line 108:** Attaches parsed answers to question data
- **Line 113:** Closes connection
- **Line 114:** Returns array of questions with their answers

### Lines 118-123: Insert Result
```php
function insertResult(string $username, int $quizId, int $score): void
```
- **Line 120:** Establishes database connection
- **Line 121:** Inserts score record into `results` table
- **Line 122:** Closes connection

### Lines 126-153: Get Results by Username
```php
function getResultsByUsername(string $username): array
```
- **Line 128:** Establishes database connection
- **Lines 129-139:** Complex JOIN query fetching results with quiz names and total question counts
- **Lines 140-148:** Processes each result, calculating percentage score
- **Line 151:** Closes connection
- **Line 152:** Returns array of result records

### Lines 156-168: Register User
```php
function registerUser(string $username, string $password): bool
```
- **Line 158:** Establishes database connection
- **Lines 159-163:** Checks if username already exists; returns false if taken
- **Line 164:** Inserts new user with username, password, and `is_admin = FALSE`
- **Line 165:** Checks if insert affected rows (success)
- **Line 166:** Closes connection
- **Line 167:** Returns boolean success status

## Security Notes

- Uses `real_escape_string()` for SQL injection prevention (prepared statements would be more secure)
- Passwords stored in plaintext (should use hashing like `password_hash()`)
- No CSRF protection on forms
