# logout.php

**Path:** `app/utils/logout.php`
**Purpose:** Handles user logout by destroying the session and redirecting to login page.

## Line-by-Line Logic

### Line 1: PHP Opening Tag
```php
<?php
```
- Starts PHP code block

### Line 2: Start Session
```php
session_start();
```
- Initializes or resumes the current session
- Required to access session data before destroying it

### Line 3: Destroy Session
```php
session_destroy();
```
- Completely destroys all session data
- Removes all session variables and the session cookie
- User is now fully logged out

### Line 4: Redirect to Login
```php
header('Location: ../pages/login.php');
```
- Sends HTTP redirect header to the login page
- Uses relative path to navigate to login page

### Line 5: Terminate Script
```php
exit;
```
- Stops further script execution
- Ensures no additional code runs after redirect

## Flow Summary

1. Start session (to access it)
2. Destroy all session data
3. Redirect user to login page
4. Exit immediately

## Security Notes

- Does not call `session_unset()` before `session_destroy()` (though `session_destroy()` handles cleanup)
- Does not clear the session cookie explicitly (browser may still hold it)
- No CSRF token invalidation (relies on full session destruction)
