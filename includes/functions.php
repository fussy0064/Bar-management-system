<?php

function formatCurrency(float $amount): string
{
    return APP_CURRENCY . ' ' . number_format($amount, 0);
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirectTo(string $path): never
{
    if (str_starts_with($path, '/') && defined('BASE_URL')) {
        $path = BASE_URL . $path;
    }
    header('Location: ' . $path);
    exit;
}
