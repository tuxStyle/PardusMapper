<?php
declare(strict_types=1);

use Pardusmapper\Core\MySqlDB;
use Pardusmapper\Core\Settings;

if (!function_exists('vnull')) {
    function vnull(mixed $value): mixed
    {
        return $value === null || $value === '' || strtolower($value) === 'null' ? null : $value;
    }
}

if (!function_exists('vint')) {
    /**
     * validate required value as integer
     *
     * @param string|null $value
     * @param integer|null $default
     * @return integer|null
     */
    function vint(?string $value, ?int $default = null): ?int
    {
        $value = vnull($value);

        if (is_null($value) || !preg_match('/^\d+$/', $value)) {
            return $default;
        }

        return (int)$value;
    }
}

if (!function_exists('vstring')) {
    /**
     * validate and protect required value as string
     *
     * @param string|null $value
     * @param string|null $default
     * @return string|null
     */
    function vstring(?string $value, ?string $default = null): ?string
    {
        $value = vnull($value);

        if (is_null($value)) {
            return $default;
        }

        return MySqlDB::instance()->protect($value);
    }
}

if (!function_exists('vbool')) {
    /**
     * validate required value as boolean
     *
     * @param string|null $value
     * @param bool|null $default
     * @return bool|null
     */
    function vbool(?string $value, ?bool $default = false): bool
    {
        $value = vnull($value);

        if (is_null($value) || !preg_match('/^(0|1|true|false)$/i', $value)) {
            return $default;
        }

        return 'true' === strtolower($value) || '1' === $value ? true : false;
    }
}

if (!function_exists('vfloat')) {
    /**
     * validate required value as float
     *
     * @param string|null $value
     * @param float|null $default
     * @return float|null
     */
    function vfloat(?string $value, float $default = 0): float
    {
        $value = vnull($value);

        if (is_null($value) || !preg_match('/^\d+(\.\d+)?$/', $value)) {
            return $default;
        }

        return (float)$value;
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return dirname(__DIR__) . "/{$path}";
    }
}

if (!function_exists('http_response')) {
    /**
     * Undocumented function
     *
     * @param boolean $fails
     * @param integer $code
     * @param string $message
     * @return void
     */
    function http_response(bool $fails, int $code, string $message = ''): void
    {
        if (!$fails) {
            return;
        }

        http_response_code($code);
        echo $message;
        exit();
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

        if (is_object($what)) {
            $what = [$what];
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

        if (is_object($what)) {
            $what = [$what];
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

        if (is_object($what)) {
            $what = [$what];
        }
        // if (is_array($what)) {
        //     $what = print_r($what, true);
        // }

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

        if (is_object($what)) {
            $what = [$what];
        }
        // if (is_array($what)) {
        //     $what = print_r($what, true);
        // }

        echo "\n<xmp>\n";
        var_dump ($what);
        echo "\n</xmp>\n";
    }
}

function mapper_exception_handler($exception) {
    // Custom output for uncaught exceptions
    echo '<div style="font-family: Arial, sans-serif; margin: 20px;">';
    echo '<h2 style="color: #d9534f;">Uncaught Exception: ' . htmlspecialchars((string) $exception->getMessage()) . '</h2>';
    echo '<div style="border: 1px solid #ccc; padding: 15px; background-color: #f9f9f9;">';
    echo '<h3 style="color: #5bc0de;">Stack trace:</h3>';
    echo '<ul style="list-style: none; padding-left: 0;">';

    foreach ($exception->getTrace() as $key => $trace) {
        echo '<li style="margin-bottom: 10px;">';
        echo '<strong>[' . $key . ']</strong> ' . ($trace['file'] ?? '[internal function]') . ' ';
        echo '<strong>Line:</strong> ' . ($trace['line'] ?? 'N/A') . '<br />';
        echo '<strong>Function:</strong> ' . $trace['function'] . '()';
        echo '</li>';
    }

    echo '</ul>';
    echo '</div>';
    echo '</div>';

    // Log the exception message (optional)
    error_log($exception->getMessage());
}

if (!function_exists('debug')) {
    function debug(): void
    {
        if (!Settings::$DEBUG) {
            return;
        }

        $dump = false;
        $args = func_get_args();

        // one element only, use it
        $what = 1 === count($args) ? array_shift($args) : $args;

        // if object, convert to array 
        if (is_object($what)) {$what = [$what]; $dump = true;}
        
        // // if array convert to string
        // $what = is_array($what) ? print_r($what, true) : $what;

        echo "\n<pre>\n";

        if ($dump) {
            var_dump($what);
        } else {
            echo print_r($what, true);
        }

        echo "\n</pre>\n";
        echo "<br />\n";
    }
}

function templates(string $file): string
{
    return sprintf('%s/templates/%s.php', ROOT, $file);
}

function clusters(string $file): string
{
    return sprintf('%s/templates/clusters/%s.php', ROOT, $file);
}