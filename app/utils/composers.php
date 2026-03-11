<?php

/**
 * @import {Answer} from './types.ts';
 */

/**
 * @param {Answer[]} $answers
 * @return string 
 */


function composeAnswers(array $answers): string
{
    $composed = [];

    foreach ($answers as $answer) {
        $composed[] = [
            'text'    => (string) ($answer['text'] ?? ''),
            'correct' => (bool)   ($answer['correct'] ?? false),
        ];
    }

    return json_encode($composed, JSON_UNESCAPED_UNICODE);
}

/**
 * @param string $json
 * @return array    
 */
function parseAnswers(string $json): array
{
    $decoded = json_decode($json, true);

    if (!is_array($decoded)) {
        return [];
    }

    $answers = [];

    foreach ($decoded as $item) {
        $answers[] = [
            'text'    => (string) ($item['text'] ?? ''),
            'correct' => (bool)   ($item['correct'] ?? false),
        ];
    }

    return $answers;
}

?>