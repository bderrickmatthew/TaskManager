<?php
$GLOBALS['configurations'] = [];

function loadConfigurations(): void
{
    $configFiles = \glob(__DIR__ . '/config/*.php');

    foreach ($configFiles as $file) {
        $configName = \basename($file, '.php');

        $GLOBALS['configurations'][$configName] = include $file;
    }
}

function config(string $key): mixed
{
    $keys = \explode('.', $key);

    $config = $GLOBALS['configurations'];

    foreach ($keys as $segment) {
        if (isset($config[$segment])) {
            $config = $config[$segment];
        } else {
            return null;
        }
    }
    return $config;
}

function loadEnv(): void
{
    $envFile = file_exists('.env_dev') ? '.env_dev' : '.env.github';
    if (!file_exists($envFile)) {
        error_log("Environment file not found: " . $envFile);
        return;
    }

    $env = \file_get_contents($envFile);
    $lines = \explode("\n", $env);

    foreach ($lines as $line) {
        $trimmedLine = trim($line);

        // ignore empty lines or lines starting with #

        if ($trimmedLine === '' || strpos($trimmedLine, '#') === 0) {
            continue;
        }

        list($name, $value) = \explode('=', $trimmedLine, 2);

        $_ENV[$name] = $value;
    }
}
function env(string $key, ?string $default = null): ?string
{
    return $_ENV[$key] ?? $default;
}



/// start
loadEnv();
loadConfigurations();