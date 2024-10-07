<?php
declare(strict_types=1);

namespace Pardusmapper;

use Pardusmapper\Core\MySqlDB;
use Pardusmapper\Core\Settings;

class Session
{
    /**
     * Returns protected string from $_SESSION
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function pstring(string $key, ?string $default = null): ?string
    {
        $value = vstring(($_SESSION[$key] ?? null), $default);

        if (!is_null($value)) {
            $value = MySqlDB::instance()->protect($value);
        }

        debug(sprintf('%s: %s', $key, $value));

        return $value;
    }

    /**
     * Returns protected int from $_SESSION
     *
     * @param string $key
     * @param int|null $default
     * @return int|null
     */
    public static function pint(string $key, ?int $default = null): ?int
    {
        $value = vint(($_SESSION[$key] ?? null), $default);

        debug(sprintf('%s: %s', $key, $value));

        return $value;
    }

    /**
     * Returns protected bool from $_SESSION
     *
     * @param string $key
     * @param bool $default
     * @return bool
     */
    public static function pbool(string $key, bool $default = false): bool
    {
        $value = vbool(($_SESSION[$key] ?? null), $default);

        debug(sprintf('%s: %s', $key, $value ? 'true' : 'false'));

        return $value;
    }

    /**
     * Returns protected float from $_SESSION
     *
     * @param string $key
     * @param float|null $default
     * @return float|null
     */
    public static function pfloat(string $key, ?float $default = null): ?float
    {
        $value = vfloat(($_SESSION[$key] ?? null), $default);

        debug(sprintf('%s: %s', $key, $value));

        return $value;
    }
}