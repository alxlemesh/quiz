# composers.php

**Path:** `app/utils/composers.php`
**Purpose:** Utility functions for composing and parsing answer data structures as JSON.

## Line-by-Line Logic

### Lines 1-5: Import Documentation
```php
/**
 * @import {Answer} from './types.ts';
 */
```
- **Lines 3-5:** PHPDoc comment indicating type imports (note: references TypeScript syntax, which is unusual for PHP)
- Serves as documentation only; has no runtime effect in PHP

### Lines 7-10: Function Documentation
```php
/**
 * @param {Answer[]} $answers
 * @return string
 */
```
- **Lines 7-10:** PHPDoc block documenting the `composeAnswers` function
- Indicates function accepts an array of Answer objects and returns a string

### Lines 13-25: Compose Answers Function
```php
function composeAnswers(array $answers): string
```
- **Line 13:** Function declaration accepting array, returning string

- **Line 15:** Initializes empty array `$composed` for building result

- **Lines 17-22:** Foreach loop iterating through input answers:
  - **Line 18-21:** Builds normalized answer array:
    - `'text'` - Casts to string with null coalescing (defaults to empty string)
    - `'correct'` - Casts to boolean with null coalescing (defaults to false)
  - **Line 18:** Appends normalized answer to `$composed` array

- **Line 24:** Encodes array to JSON string with `JSON_UNESCAPED_UNICODE` flag
  - Preserves Unicode characters without escaping
  - Returns JSON-encoded string

- **Line 25:** Function closing brace

### Lines 27-30: Function Documentation
```php
/**
 * @param string $json
 * @return array
 */
```
- **Lines 27-30:** PHPDoc block documenting the `parseAnswers` function
- Indicates function accepts a JSON string and returns an array

### Lines 31-49: Parse Answers Function
```php
function parseAnswers(string $json): array
```
- **Line 31:** Function declaration accepting string, returning array

- **Line 33:** Decodes JSON string to PHP array using `json_decode($json, true)`
  - `true` parameter returns associative array instead of object

- **Lines 35-37:** Validation check:
  - If decoded value is not an array, returns empty array
  - Handles invalid JSON or non-array JSON values

- **Line 39:** Initializes empty `$answers` array for result

- **Lines 41-46:** Foreach loop iterating through decoded items:
  - **Line 42-45:** Builds normalized answer array (same structure as `composeAnswers`):
    - `'text'` - Casts to string with null coalescing
    - `'correct'` - Casts to boolean with null coalescing
  - **Line 42:** Appends to `$answers` array

- **Line 48:** Returns parsed and normalized answers array

- **Line 49:** Function closing brace

### Line 51: PHP Closing Tag
```php
?>
```
- Closes PHP code block (optional in pure-PHP files, but included here)

## Data Flow

### composeAnswers (PHP → JSON)
```
Input:  [['text' => 'Paris', 'correct' => true], ...]
Output: '[{"text":"Paris","correct":true},...]'
```

### parseAnswers (JSON → PHP)
```
Input:  '[{"text":"Paris","correct":true},...]'
Output: [['text' => 'Paris', 'correct' => true], ...]
```

## Usage Context

- `composeAnswers` is called by `create_quiz.php` when saving quiz answers to database
- `parseAnswers` is called by `database.php` in `insertQuestion()` to process stored JSON

## Notes

- Functions provide symmetry for serialization/deserialization
- Type casting ensures data consistency even with malformed input
- `JSON_UNESCAPED_UNICODE` makes stored JSON more human-readable
