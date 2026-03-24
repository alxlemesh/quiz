# Pseudocode of queries
---

## 1) DDL

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

Seed user:
```sql
insert INTO users (id, username, password, is_admin)
VALUES (1, 'root', 'lbhtrnjh', TRUE);
```

---

## 2) Authentication — `app/utils/database.php`

login:
```sql
SELECT id, username, is_admin
FROM users
WHERE username = :username
  AND password = :password;
```

registration
```sql
SELECT id
FROM users
WHERE username = :username;


insert INTO users (username, password, is_admin)
VALUES (:username, :password, FALSE);
```

---

## 3) Quizzes

get all quizzes + question count:
```sql
SELECT q.id, q.name, q.description, q.date_added,
       COUNT(qu.id) AS question_count
FROM quiziz q
LEFT JOIN questions qu ON qu.quiz_id = q.id
GROUP BY q.id
ORDER BY q.date_added DESC;
```

get quiz by id:
```sql
SELECT id, name, description
FROM quiziz
WHERE id = :quizId;
```

---

## 4) Questions (as implemented in code)

insert quiz:
```sql
insert INTO quiziz (name, description)
VALUES (:name, :description);
```

insert question
```sql
insert INTO questions (quiz_id, quiestion, answers)
VALUES (:quizId, :questionText, :answersJson);
```

get questions by quiz id
```sql
SELECT id, quiestion, answers
FROM questions
WHERE quiz_id = :quizId
ORDER BY id;
```

---

## 5) Results

insert result:
```sql
insert INTO results (username, quizId, result)
VALUES (:username, :quizId, :score);
```

get results by username:
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
