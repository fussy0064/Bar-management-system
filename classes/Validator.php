<?php

// Small helper used by each model's validate() method.
// Keeps validation rules short and reusable (form validation requirement).
class Validator
{
    public static function required(mixed $value, string $field, array &$errors): void
    {
        if ($value === null || trim((string) $value) === '') {
            $errors[] = "{$field} is required";
        }
    }

    public static function minLength(string $value, int $min, string $field, array &$errors): void
    {
        if (strlen($value) < $min) {
            $errors[] = "{$field} must be at least {$min} characters";
        }
    }

    public static function numeric(mixed $value, string $field, array &$errors): void
    {
        if (!is_numeric($value)) {
            $errors[] = "{$field} must be a number";
        }
    }

    public static function positive(mixed $value, string $field, array &$errors): void
    {
        if (!is_numeric($value) || (float) $value < 0) {
            $errors[] = "{$field} must be zero or greater";
        }
    }

    public static function email(string $value, string $field, array &$errors): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "{$field} must be a valid email address";
        }
    }
}
