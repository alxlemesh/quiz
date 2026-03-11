<?php


function getConnection(): mysqli
{
    $conn = new mysqli('localhost', 'root', '', 'quiz');
    if ($conn->connect_error) {
        die('Database connection failed: ' . $conn->connect_error);
    }
    return $conn;
}


function authenticateUser(string $username, string $password): ?array
{
    $conn = getConnection();
    $result = $conn->query("SELECT id, username FROM users WHERE username = '" . $conn->real_escape_string($username) . "' AND password = '" . $conn->real_escape_string($password) . "'");
    $user = $result && $result->num_rows === 1 ? $result->fetch_assoc() : null;
    $conn->close();
    return $user;
}


function getAllQuizzes(): array
{
    $conn = getConnection();
    $result = $conn->query("
        SELECT q.id, q.name, q.description, q.date_added,
               COUNT(qu.id) AS question_count
        FROM quiziz q
        LEFT JOIN questions qu ON qu.quiz_id = q.id
        GROUP BY q.id
        ORDER BY q.date_added DESC
    ");
    $quizzes = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $quizzes[] = $row;
        }
    }
    $conn->close();
    return $quizzes;
}


function insertQuiz(string $name, string $description): int
{
    $conn = getConnection();
    $conn->query("INSERT INTO quiziz (name, description) VALUES ('" . $conn->real_escape_string($name) . "', '" . $conn->real_escape_string($description) . "')");
    $quizId = $conn->insert_id;
    $conn->close();
    return $quizId;
}


function insertQuestion(int $quizId, string $questionText, string $answersJson): void
{
    $conn = getConnection();
    $conn->query("INSERT INTO questions (quiz_id, quiestion, answers) VALUES ($quizId, '" . $conn->real_escape_string($questionText) . "', '" . $conn->real_escape_string($answersJson) . "')");
    $conn->close();
}


function getQuizById(int $quizId): ?array
{
    $conn = getConnection();
    $result = $conn->query("SELECT id, name, description FROM quiziz WHERE id = $quizId");
    $quiz = $result ? $result->fetch_assoc() : null;
    $conn->close();
    return $quiz ?: null;
}


function getQuestionsByQuizId(int $quizId): array
{
    $conn = getConnection();
    $result = $conn->query("SELECT id, quiestion, answers FROM questions WHERE quiz_id = $quizId ORDER BY id");
    $questions = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $row['parsed_answers'] = parseAnswers($row['answers']);
            $questions[] = $row;
        }
    }
    $conn->close();
    return $questions;
}


function insertResult(string $username, int $quizId, int $score): void
{
    $conn = getConnection();
    $conn->query("INSERT INTO results (username, quizId, result) VALUES ('" . $conn->real_escape_string($username) . "', $quizId, $score)");
    $conn->close();
}


function getResultsByUsername(string $username): array
{
    $conn = getConnection();
    $result = $conn->query("
        SELECT r.id AS result_id, r.result AS score, r.quizId,
               q.name AS quiz_name,
               COUNT(qu.id) AS total_questions
        FROM results r
        JOIN quiziz q ON q.id = r.quizId
        LEFT JOIN questions qu ON qu.quiz_id = q.id
        WHERE r.username = '" . $conn->real_escape_string($username) . "'
        GROUP BY r.id
        ORDER BY r.id ASC
    ");
    $results = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $total = (int)$row['total_questions'];
            $score = (int)$row['score'];
            $row['total_questions'] = $total;
            $row['score'] = $score;
            $row['percent'] = $total > 0 ? round(($score / $total) * 100) : 0;
            $results[] = $row;
        }
    }
    $conn->close();
    return $results;
}
