# Quiz Application - Pseudocode and Architecture

## 1. Database Structure

### Tables and Relationships

```
TABLE users
    PRIMARY KEY: id (INT, AUTO_INCREMENT)
    username (TEXT, NOT NULL)
    password (TEXT, NOT NULL)
    is_admin (BOOLEAN, DEFAULT FALSE)

TABLE quiziz
    PRIMARY KEY: id (INT, AUTO_INCREMENT)
    name (VARCHAR(30), NOT NULL)
    description (TEXT, NOT NULL)
    date_added (DATE, DEFAULT CURRENT_TIMESTAMP)

TABLE questions
    PRIMARY KEY: id (INT, AUTO_INCREMENT)
    FOREIGN KEY: quiz_id REFERENCES quiziz(id)
    quiestion (TEXT, NOT NULL)

TABLE answers
    PRIMARY KEY: id (INT, AUTO_INCREMENT)
    FOREIGN KEY: question_id REFERENCES questions(id) ON DELETE CASCADE
    answer_text (TEXT, NOT NULL)
    is_correct (BOOLEAN, DEFAULT FALSE)

TABLE results
    PRIMARY KEY: id (INT, AUTO_INCREMENT)
    username (TEXT, NOT NULL)
    FOREIGN KEY: quizId REFERENCES quiziz(id)
    result (INT, NOT NULL)
    date_completed (DATETIME, DEFAULT CURRENT_TIMESTAMP)
```

### Relationships
- One quiz has many questions (1:N)
- One question has many answers (1:N)
- One quiz has many results (1:N)
- One user can have many results (1:N)

---

## 2. Application Flow Pseudocode

### 2.1 User Authentication Module

#### Login Process
```
FUNCTION authenticateUser(username, password)
    PREPARE statement: "SELECT id, username, is_admin FROM users WHERE username = ? AND password = ?"
    BIND parameters: username, password
    EXECUTE statement

    IF user found THEN
        RETURN user data (id, username, is_admin)
    ELSE
        RETURN null
    END IF
END FUNCTION

PROCEDURE handleLogin()
    IF request method is POST THEN
        GET username from POST data
        GET password from POST data

        user = authenticateUser(username, password)

        IF user exists THEN
            REGENERATE session ID
            SET session['user_id'] = user.id
            SET session['username'] = user.username
            SET session['is_admin'] = user.is_admin
            REDIRECT to index.php
        ELSE
            SET error = "Invalid credentials"
        END IF
    END IF

    DISPLAY login form
END PROCEDURE
```

#### Registration Process
```
FUNCTION registerUser(username, password)
    PREPARE statement: "SELECT id FROM users WHERE username = ?"
    BIND parameter: username
    EXECUTE statement

    IF user already exists THEN
        RETURN false
    END IF

    PREPARE statement: "INSERT INTO users (username, password, is_admin) VALUES (?, ?, FALSE)"
    BIND parameters: username, password
    EXECUTE statement

    RETURN true if successful
END FUNCTION

PROCEDURE handleRegistration()
    IF request method is POST THEN
        GET username from POST data (trim whitespace)
        GET password from POST data
        GET confirmPassword from POST data

        IF username is empty OR password is empty THEN
            SET error = "Username and password required"
        ELSE IF username length < 3 THEN
            SET error = "Username must be at least 3 characters"
        ELSE IF password length < 4 THEN
            SET error = "Password must be at least 4 characters"
        ELSE IF password != confirmPassword THEN
            SET error = "Passwords do not match"
        ELSE
            IF registerUser(username, password) THEN
                SET success = "Registration successful"
            ELSE
                SET error = "Username already exists"
            END IF
        END IF
    END IF

    DISPLAY registration form
END PROCEDURE
```

---

### 2.2 Quiz Management Module

#### Get All Quizzes
```
FUNCTION getAllQuizzes()
    PREPARE statement: "
        SELECT q.id, q.name, q.description, q.date_added, COUNT(qu.id) as question_count
        FROM quiziz q
        LEFT JOIN questions qu ON qu.quiz_id = q.id
        GROUP BY q.id
        ORDER BY q.date_added DESC
    "
    EXECUTE statement

    INITIALIZE empty array quizzes
    FOR EACH row in result THEN
        ADD row to quizzes array
    END FOR

    RETURN quizzes
END FUNCTION
```

#### Create New Quiz
```
FUNCTION insertQuiz(name, description)
    PREPARE statement: "INSERT INTO quiziz (name, description) VALUES (?, ?)"
    BIND parameters: name, description
    EXECUTE statement

    RETURN last inserted ID
END FUNCTION

FUNCTION insertQuestion(quizId, questionText)
    PREPARE statement: "INSERT INTO questions (quiz_id, quiestion) VALUES (?, ?)"
    BIND parameters: quizId, questionText
    EXECUTE statement

    RETURN last inserted question ID
END FUNCTION

FUNCTION insertAnswer(questionId, answerText, isCorrect)
    PREPARE statement: "INSERT INTO answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)"
    BIND parameters: questionId, answerText, isCorrect
    EXECUTE statement

    RETURN success status
END FUNCTION

PROCEDURE handleQuizCreation()
    CHECK if user is authenticated
    CHECK if user is admin

    IF request method is POST THEN
        GET quizName from POST data
        GET quizDescription from POST data
        GET questions array from POST data

        BEGIN TRANSACTION

        quizId = insertQuiz(quizName, quizDescription)

        FOR EACH question in questions THEN
            questionId = insertQuestion(quizId, question.text)

            FOR EACH answer in question.answers THEN
                insertAnswer(questionId, answer.text, answer.isCorrect)
            END FOR
        END FOR

        COMMIT TRANSACTION

        REDIRECT to index.php
    END IF

    DISPLAY quiz creation form
END PROCEDURE
```

---

### 2.3 Quiz Solving Module

#### Get Quiz with Questions and Answers
```
FUNCTION getQuizById(quizId)
    PREPARE statement: "SELECT id, name, description FROM quiziz WHERE id = ?"
    BIND parameter: quizId
    EXECUTE statement

    RETURN quiz data or null
END FUNCTION

FUNCTION getQuestionsByQuizId(quizId)
    PREPARE statement: "
        SELECT q.id, q.quiestion, a.id as answer_id, a.answer_text, a.is_correct
        FROM questions q
        LEFT JOIN answers a ON a.question_id = q.id
        WHERE q.quiz_id = ?
        ORDER BY q.id, a.id
    "
    BIND parameter: quizId
    EXECUTE statement

    INITIALIZE empty array questions
    INITIALIZE currentQuestion = null

    FOR EACH row in result THEN
        IF currentQuestion is null OR currentQuestion.id != row.id THEN
            IF currentQuestion is not null THEN
                ADD currentQuestion to questions array
            END IF

            currentQuestion = {
                id: row.id,
                text: row.quiestion,
                answers: []
            }
        END IF

        ADD {
            id: row.answer_id,
            text: row.answer_text,
            isCorrect: row.is_correct
        } to currentQuestion.answers
    END FOR

    IF currentQuestion is not null THEN
        ADD currentQuestion to questions array
    END IF

    RETURN questions
END FUNCTION
```

#### Calculate Score and Save Result
```
FUNCTION calculateScore(questions, userAnswers)
    INITIALIZE score = 0

    FOR EACH question in questions THEN
        GET correctAnswerIds for this question
        GET userSelectedIds from userAnswers for this question

        IF userSelectedIds matches exactly correctAnswerIds THEN
            INCREMENT score by 1
        END IF
    END FOR

    RETURN score
END FUNCTION

FUNCTION insertResult(username, quizId, score)
    PREPARE statement: "INSERT INTO results (username, quizId, result, date_completed) VALUES (?, ?, ?, NOW())"
    BIND parameters: username, quizId, score
    EXECUTE statement

    RETURN success status
END FUNCTION

PROCEDURE handleQuizSubmission()
    CHECK if user is authenticated

    IF request method is POST THEN
        GET quizId from POST data
        GET userAnswers from POST data

        quiz = getQuizById(quizId)
        questions = getQuestionsByQuizId(quizId)

        score = calculateScore(questions, userAnswers)
        totalQuestions = COUNT(questions)
        percentage = (score / totalQuestions) * 100

        insertResult(session['username'], quizId, score)

        DISPLAY results page with:
            - Score: score / totalQuestions
            - Percentage: percentage%
            - Summary message based on percentage
    END IF
END PROCEDURE
```

---

### 2.4 Results Display Module

#### Get User Results
```
FUNCTION getResultsByUsername(username)
    PREPARE statement: "
        SELECT r.id, r.result, r.quizId, r.date_completed,
               q.name as quiz_name,
               COUNT(qu.id) as total_questions
        FROM results r
        JOIN quiziz q ON q.id = r.quizId
        LEFT JOIN questions qu ON qu.quiz_id = q.id
        WHERE r.username = ?
        GROUP BY r.id
        ORDER BY r.date_completed DESC
    "
    BIND parameter: username
    EXECUTE statement

    INITIALIZE empty array results
    FOR EACH row in result THEN
        percentage = (row.result / row.total_questions) * 100

        ADD {
            id: row.id,
            quizName: row.quiz_name,
            score: row.result,
            totalQuestions: row.total_questions,
            percentage: percentage,
            dateCompleted: row.date_completed
        } to results array
    END FOR

    RETURN results
END FUNCTION

PROCEDURE displayResults()
    CHECK if user is authenticated

    results = getResultsByUsername(session['username'])

    DISPLAY results table with:
        - Quiz name
        - Score (X / Y)
        - Percentage
        - Date completed
        - Retake button
END PROCEDURE
```

---

## 3. Security Measures

### SQL Injection Prevention
```
ALWAYS use prepared statements with parameter binding:
    PREPARE statement with placeholders (?)
    BIND parameters separately
    EXECUTE statement

NEVER concatenate user input directly into SQL queries
```

### XSS Prevention
```
ALWAYS use htmlspecialchars() when outputting user data:
    <?= htmlspecialchars($userInput) ?>

APPLY to:
    - Usernames
    - Quiz names and descriptions
    - Question text
    - Answer text
    - Any user-generated content
```

### Session Security
```
ON successful login:
    REGENERATE session ID to prevent session fixation

ON logout:
    DESTROY session completely

CHECK authentication on every protected page:
    IF session['user_id'] is not set THEN
        REDIRECT to login.php
    END IF
```

---

## 4. User Interface Flow

```
START
    |
    v
[Login/Register Page]
    |
    |-- User not registered --> [Register] --> [Login]
    |
    v
[Main Menu / Quiz List]
    |
    |-- View all available quizzes
    |-- Admin: Create new quiz button
    |-- View my results button
    |
    |-- Select quiz --> [Quiz Solving Page]
    |                       |
    |                       |-- Display questions with 4 answers each
    |                       |-- User selects answers
    |                       |-- Submit quiz
    |                       v
    |                   [Results Page]
    |                       |
    |                       |-- Show score and percentage
    |                       |-- Show summary message
    |                       |-- Save to database
    |                       |-- Option to retake or go back
    |
    |-- View Results --> [Results History Page]
                            |
                            |-- List all previous attempts
                            |-- Show username, score, date, percentage
                            |-- Option to retake any quiz
```

---

## 5. Data Validation Rules

### User Input Validation
```
Username:
    - Minimum 3 characters
    - Required field
    - Must be unique

Password:
    - Minimum 4 characters
    - Required field
    - Must match confirmation on registration

Quiz Name:
    - Maximum 30 characters
    - Required field

Quiz Description:
    - Required field

Questions:
    - Minimum 10 questions per quiz
    - Each question must have exactly 4 answers
    - Exactly one answer must be marked as correct

Answers:
    - Required field
    - Cannot be empty
```

---

## 6. Error Handling

```
DATABASE ERRORS:
    TRY
        Execute database operation
    CATCH exception
        LOG error details
        DISPLAY user-friendly error message
        ROLLBACK transaction if applicable
    END TRY

VALIDATION ERRORS:
    COLLECT all validation errors
    DISPLAY errors to user
    PRESERVE user input in form
    DO NOT proceed with operation

AUTHENTICATION ERRORS:
    DISPLAY generic error message (don't reveal if username or password is wrong)
    LOG failed login attempts
    REDIRECT to login page if session expired
```
