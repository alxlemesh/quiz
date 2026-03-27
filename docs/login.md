# login.php

**Path:** `app/pages/login.php`
**Purpose:** User authentication page - handles login form display and credential verification.

## Line-by-Line Logic

### Lines 1-3: Session and Dependencies
```php
session_start();
require_once '../utils/database.php';
```
- **Line 1:** Starts or resumes PHP session
- **Line 2:** Imports database utility functions (authenticateUser, etc.)

### Lines 5-8: Authenticated User Redirect
```php
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
```
- **Line 5:** Checks if user is already logged in (session has user_id)
- **Line 6:** Redirects to main menu if already authenticated
- **Line 7:** Stops script execution after redirect

### Lines 10-11: Error Variable Initialization
```php
$error = '';
```
- **Line 10:** Initializes empty error message variable

### Lines 12-28: POST Request Handler
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
```
- **Line 12:** Checks if form was submitted via POST method

- **Lines 13-14:** Extract and sanitize input:
  - `$username` - Gets username from POST (defaults to empty string)
  - `$password` - Gets password from POST (defaults to empty string)

- **Line 16:** Calls `authenticateUser()` function with credentials
  - Returns user array on success, null on failure

- **Lines 18-24:** Successful authentication block:
  - **Line 19:** Regenerates session ID (prevents session fixation attacks)
  - **Line 20:** Stores user ID in session
  - **Line 21:** Stores username in session
  - **Line 22:** Stores admin status in session (cast to boolean)
  - **Line 23:** Redirects to main menu (index.php)
  - **Line 24:** Exits script

- **Lines 25-27:** Failed authentication block:
  - **Line 26:** Sets generic error message (doesn't reveal if username exists)

### Lines 30-59: HTML Form Rendering

#### Lines 30-38: Document Head
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../styles/login.css">
</head>
```
- **Lines 30-38:** Standard HTML5 boilerplate with login stylesheet

#### Lines 39-58: Login Form Body
```html
<body>
    <div class="login-container">
        <h1 class="login-title">Login</h1>
```
- **Lines 39-41:** Container div with page title

- **Lines 42-43:** Conditional error display:
  ```php
  <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  ```
  - Shows error message if authentication failed
  - Uses `htmlspecialchars()` to prevent XSS

- **Lines 44-54:** Login form:
  - **Line 44:** Form element with POST method
  - **Lines 45-48:** Username input field (required)
  - **Lines 49-52:** Password input field (required, type=password)
  - **Line 53:** Submit button

- **Lines 55-57:** Registration link:
  ```html
  <div class="link-text">
      <p>Don't have an account? <a href="register.php">Register here</a></p>
  </div>
  ```
  - Provides navigation to registration page

### Lines 59-60: Closing Tags
```html
</body>
</html>
```

## Authentication Flow

1. User accesses login page
2. If already logged in → redirect to index
3. User submits credentials via POST
4. System validates against database
5. On success: create session, redirect to menu
6. On failure: display error, show form again

## Security Features

- **Line 19:** Session regeneration prevents session fixation
- **Line 22:** Boolean cast prevents type confusion
- **Line 43:** HTML escaping prevents XSS in error messages
- **Line 26:** Generic error message doesn't reveal if username exists

## Security Concerns

- Passwords compared in plaintext (should use `password_verify()`)
- No rate limiting on login attempts
- No CSRF token on form
