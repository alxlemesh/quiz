# SQL Pseudocode (queries used in this project)

This file intentionally contains **only SQL** derived from the project’s existing queries/schema.

---

## 1) Database schema (DDL) — `app/quiz.sql`

```sql
CREATE TABLE quiziz (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(30) NOT NULL,
  description TEXT NOT NULL,
  date_added DATE NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  quiz_id INT NOT NULL,
  quiestion TEXT NOT NULL,
  FOREIGN KEY (quiz_id) REFERENCES quiziz(id) ON DELETE CASCADE
);

CREATE TABLE answers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  question_id INT NOT NULL,
  answer_text TEXT NOT NULL,
  is_correct BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);

CREATE TABLE results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username TEXT NOT NULL,
  quizId INT NOT NULL,
  result INT NOT NULL,
  date_completed DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (quizId) REFERENCES quiziz(id) ON DELETE CASCADE
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username TEXT NOT NULL,
  password TEXT NOT NULL,
  is_admin BOOLEAN NOT NULL DEFAULT FALSE
);
```

Seed user (from dump):
```sql
INSERT INTO users (id, username, password, is_admin)
VALUES (1, 'root', 'lbhtrnjh', TRUE);
```

---

## 2) Authentication — `app/utils/database.php`

Login:
```sql
SELECT id, username, is_admin
FROM users
WHERE username = :username
  AND password = :password;
```

Registration (uniqueness check):
```sql
SELECT id
FROM users
WHERE username = :username;
```

Registration (insert):
```sql
INSERT INTO users (username, password, is_admin)
VALUES (:username, :password, FALSE);
```

---

## 3) Quizzes

Get all quizzes + question count:
```sql
SELECT q.id, q.name, q.description, q.date_added,
       COUNT(qu.id) AS question_count
FROM quiziz q
LEFT JOIN questions qu ON qu.quiz_id = q.id
GROUP BY q.id
ORDER BY q.date_added DESC;
```

Get quiz by id:
```sql
SELECT id, name, description
FROM quiziz
WHERE id = :quizId;
```

---

## 4) Questions (as implemented in code)

Insert quiz:
```sql
INSERT INTO quiziz (name, description)
VALUES (:name, :description);
```

Insert question (note: code expects an `answers` column on `questions`):
```sql
INSERT INTO questions (quiz_id, quiestion, answers)
VALUES (:quizId, :questionText, :answersJson);
```

Get questions by quiz id (note: code selects `answers` column from `questions`):
```sql
SELECT id, quiestion, answers
FROM questions
WHERE quiz_id = :quizId
ORDER BY id;
```

---

## 5) Results

Insert result:
```sql
INSERT INTO results (username, quizId, result)
VALUES (:username, :quizId, :score);
```

Get results by username:
```sql
SELECT r.id AS result_id,
       r.result AS score,
       r.quizId,
       q.name AS quiz_name,
       COUNT(qu.id) AS total_questions
FROM results r
JOIN quiziz q ON q.id = r.quizId
LEFT JOIN questions qu ON qu.quiz_id = q.id
WHERE r.username = :username
GROUP BY r.id
ORDER BY r.id ASC;
```
