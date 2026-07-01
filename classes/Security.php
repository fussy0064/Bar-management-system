<?php

// Handles password hashing and AES-256 row-level encryption
// for sensitive fields before they are saved to the database

class Security
{
    private const CIPHER = 'AES-256-CBC';

    private static function getKey(): string
    {
        return hash('sha256', ENCRYPTION_KEY, true);
    }

    public static function encrypt(string $plainText): string
    {
        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encrypted = openssl_encrypt($plainText, self::CIPHER, self::getKey(), OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $encrypted);
    }

    public static function decrypt(?string $cipherText): ?string
    {
        if ($cipherText === null || $cipherText === '') {
            return null;
        }
        $raw = base64_decode($cipherText);
        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $iv = substr($raw, 0, $ivLength);
        $encrypted = substr($raw, $ivLength);
        $result = openssl_decrypt($encrypted, self::CIPHER, self::getKey(), OPENSSL_RAW_DATA, $iv);
        return $result === false ? null : $result;
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
