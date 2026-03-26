# apache quiz

Pseudocode of main app logic:

register:

ON POST request:

1. Get username, email, password from request
2. Validate all fields are not empty
3. Check if username exists: SELECT id FROM users WHERE username = ?
4. Check if email exists: SELECT id FROM users WHERE email = ?
5. Hash password: password_hash(password, PASSWORD_BCRYPT)
6. Insert user: INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)
7. Redirect to login page

login:

ON POST request:

1. Get username/email and password from request
2. Find user: SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?
3. If user not found: show error "Invalid credentials"
4. Verify password: password_verify(password, password_hash)
5. If invalid: show error "Invalid credentials"
6. If valid:
   - Start session
   - Set $SESSION['user_id'] = user.id
   - Set $SESSION['username'] = user.username
   - Redirect to dashboard
   - if isAdmin=true -> add SESSION['is_admin'] = true flag

solve.php:
1.Get the quiz ID from the URL query parameter (e.g., solve.php?id=5)
2.Validate that the quiz ID is a positive integer (validation failes -> redirect to index.php) 3. 3. Fetch quiz data fom database 4. parse questions on the page

on sumbit:
1.for each question, get answer array sequence, after sort it and compare with correct answers in DB, if coorect -> score++ 2. compose and send results to database

results.php
1.fetching quiz results for user using sql sequence builder from daatabase.php (require_once usage)

create_quiz.php
1.JS functions to add/delete questions and answers for them
2.form them in object using php potential vulnerabilities and send post request 3. Parse and create quiz by inserting questions and answers in different tables(handling again in database.php)
