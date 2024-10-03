<?php
declare(strict_types=1);

namespace Pardusmapper;

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
        return vstring(($_SESSION[$key] ?? null), $default);
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
        $svalue = ($_SESSION[$key] ?? null);
        $value = vint((is_numeric($svalue) ? $svalue . '' : $svalue), $default);

        debug(sprintf('%s: %s', $key, $value));

        return $value;
    }
    
    /**
     * Returns protected security from $_SESSION
     *
     * @param string $key
     * @param integer $default
     * @return integer
     */
    public static function security(string $key = 'security', int $default = 0): int
    {
        return self::pint(key: $key, default: $default);
    }

    /**
     * Returns protected rank from $_SESSION
     *
     * @param string $key
     * @param integer|null $default
     * @return integer|null
     */
    public static function rank(string $key = 'rank', ?int $default = null): ?int
    {
        return self::pint(key: $key, default: $default);
    }

    /**
     * Returns protected competency from $_SESSION
     *
     * @param string $key
     * @param integer|null $default
     * @return integer|null
     */
    public static function comp(string $key = 'comp', ?int $default = null): ?int
    {
        return self::pint(key: $key, default: $default);
    }

    /**
     * Returns protected faction from $_SESSION
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function faction(string $key = 'faction', ?string $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }

    /**
     * Returns protected syndicate from $_SESSION
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function syndicate(string $key = 'syndicate', ?string $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }
}