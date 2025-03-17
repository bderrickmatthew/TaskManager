<?php

namespace Bdm\TaskManager\System;

class CSRF
{
    private const TOKEN_KEY = 'csrf_token';

    public static function generateToken(): string
    {
        if (!isset($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::TOKEN_KEY];
    }

    public static function validateToken(?string $token): bool
    {
        if (!isset($_SESSION[self::TOKEN_KEY]) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION[self::TOKEN_KEY], $token);
    }

    public static function removeToken(): void
    {
        unset($_SESSION[self::TOKEN_KEY]);
    }
}