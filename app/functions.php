<?php

declare(strict_types=1);

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return dirname(__DIR__) . "/{$path}";
    }
}

if (!function_exists('env')) {
    /**
     * @param string $key
     * @param string|int|bool $default
     *
     * @return mixed
     */
    function env(string $key, string|int|bool $default = false): mixed
    {
        if (!array_key_exists($key, $_ENV)) {
            throw_when(true, "{$key} is not a defined .env variable and has not default value");
        }

        $value = $_ENV[$key];

        return !is_null($value) ? $value : $default;
    }
}

if (!function_exists('throw_when')) {
    /**
     * Undocumented function.
     *
     * @param bool $fails
     * @param string $message
     * @param string $exception
     */
    function throw_when(bool $fails, string $message, string $exception = \Exception::class)
    {
        if (!$fails) {
            return;
        }

        throw new $exception($message);
    }
}

if (!function_exists('pp')) {
    function pp(): void
    {
        $what = func_get_args();
        if (count($what) == 1) {
            $what = $what[0];
        }

        if (is_array($what)) {
            $what = print_r($what, true);
        }

        echo "\n<pre>\n";
        echo ($what);
        echo "\n</pre>\n";
    }
}

if (!function_exists('xp')) {
    function xp(): void
    {
        $what = func_get_args();
        if (count($what) == 1) {
            $what = $what[0];
        }

        if (is_array($what)) {
            $what = print_r($what, true);
        }

        echo "\n<xmp>\n";
        echo ($what);
        echo "\n</xmp>\n";
    }
}

if (!function_exists('pd')) {
    function pd(): void
    {
        $what = func_get_args();
        if (count($what) == 1) {
            $what = $what[0];
        }

        if (is_array($what)) {
            $what = print_r($what, true);
        }

        echo "\n<pre>\n";
        var_dump ($what);
        echo "\n</pre>\n";
    }
}

if (!function_exists('xd')) {
    function xd(): void
    {
        $what = func_get_args();
        if (count($what) == 1) {
            $what = $what[0];
        }

        if (is_array($what)) {
            $what = print_r($what, true);
        }

        echo "\n<xmp>\n";
        var_dump ($what);
        echo "\n</xmp>\n";
    }
}

function mapper_exception_handler($exception) {
    // Custom output for uncaught exceptions
    echo '<div style="font-family: Arial, sans-serif; margin: 20px;">';
    echo '<h2 style="color: #d9534f;">Uncaught Exception: ' . htmlspecialchars($exception->getMessage()) . '</h2>';
    echo '<div style="border: 1px solid #ccc; padding: 15px; background-color: #f9f9f9;">';
    echo '<h3 style="color: #5bc0de;">Stack trace:</h3>';
    echo '<ul style="list-style: none; padding-left: 0;">';

    foreach ($exception->getTrace() as $key => $trace) {
        echo '<li style="margin-bottom: 10px;">';
        echo '<strong>[' . $key . ']</strong> ' . (isset($trace['file']) ? $trace['file'] : '[internal function]') . ' ';
        echo '<strong>Line:</strong> ' . (isset($trace['line']) ? $trace['line'] : 'N/A') . '<br />';
        echo '<strong>Function:</strong> ' . $trace['function'] . '()';
        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';
    echo '</div>';

    // Log the exception message (optional)
    error_log($exception->getMessage());
}
