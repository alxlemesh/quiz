# register.php

**Path:** `app/pages/register.php`
**Purpose:** User registration page - handles new account creation with validation.

## Line-by-Line Logic

### Lines 1-3: Session and Dependencies
```php
session_start();
require_once '../utils/database.php';
```
- **Line 1:** Starts or resumes PHP session
- **Line 2:** Imports database utility functions (registerUser, etc.)

### Lines 5-8: Authenticated User Redirect
```php
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
```
- **Line 5:** Checks if user is already logged in
- **Line 6:** Redirects to main menu if already authenticated
- **Line 7:** Stops script execution

### Lines 10-12: Message Variables
```php
$error = '';
$success = '';
```
- **Lines 10-11:** Initializes empty error and success message variables

### Lines 13-33: POST Request Handler
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
```
- **Line 13:** Checks if form was submitted via POST

#### Lines 14-16: Input Extraction
```php
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
```
- **Line 14:** Gets username, trims whitespace
- **Line 15:** Gets raw password
- **Line 16:** Gets password confirmation

#### Lines 18-32: Validation Chain
```php
if (empty($username) || empty($password)) {
```
- **Lines 18-19:** Checks for empty fields → error

```php
} elseif (strlen($username) < 3) {
```
- **Lines 20-21:** Validates username length (minimum 3 characters) → error

```php
} elseif (strlen($password) < 4) {
```
- **Lines 22-23:** Validates password length (minimum 4 characters) → error

```php
} elseif ($password !== $confirmPassword) {
```
- **Lines 24-25:** Validates passwords match → error

```php
} else {
```
- **Lines 26-32:** All validations passed, attempt registration:
  - **Line 27:** Calls `registerUser()` function
  - **Lines 28-29:** On success: sets success message
  - **Lines 30-31:** On failure (username taken): sets error message

### Lines 35-74: HTML Form Rendering

#### Lines 35-42: Document Head
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../styles/register.css">
</head>
```
- **Lines 35-42:** Standard HTML5 boilerplate with registration stylesheet

#### Lines 44-72: Registration Form Body

##### Lines 44-45: Container and Title
```html
<div class="register-container">
    <h1 class="register-title">Register</h1>
```

##### Lines 46-51: Message Display
```php
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
```
- **Lines 46-48:** Displays error message (if set) with XSS protection
- **Lines 49-51:** Displays success message (if set) with XSS protection

##### Lines 52-68: Form Fields
```html
<form method="POST" action="register.php">
```
- **Line 52:** Form element posting to self

- **Lines 53-57:** Username field:
  - Required, minimum 3 characters
  - **Line 55:** Pre-fills with submitted value (UX for failed validation)
  - **Line 56:** Helper text showing requirement

- **Lines 58-62:** Password field:
  - Required, minimum 4 characters, type=password
  - **Line 61:** Helper text showing requirement

- **Lines 63-66:** Confirm password field:
  - Required, minimum 4 characters, type=password
  - No value pre-fill (security best practice)

- **Line 67:** Submit button

##### Lines 69-72: Login Link
```html
<div class="link-text">
    <p>Already have an account? <a href="login.php">Log in here</a></p>
</div>
```
- Provides navigation to login page

### Lines 73-74: Closing Tags
```html
</body>
</html>
```

## Validation Flow

1. Check both fields provided
2. Validate username length ≥ 3
3. Validate password length ≥ 4
4. Validate passwords match
5. Attempt database registration
6. Handle duplicate username

## Security Features

- **Line 14:** `trim()` removes accidental whitespace
- **Line 43, 50:** `htmlspecialchars()` prevents XSS in messages
- **Line 55:** Username pre-fill uses `htmlspecialchars()`
- **Line 7:** Prevents registered users from accessing registration

## Security Concerns

- **Line 27:** Passwords stored in plaintext (should use `password_hash()`)
- No CSRF token on form
- No email verification or account activation
- Weak password requirements (4 characters minimum)
