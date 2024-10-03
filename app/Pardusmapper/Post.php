<?php
declare(strict_types=1);

namespace Pardusmapper;

use Pardusmapper\Core\Settings;

class Post {
    /**
     * Returns protected string from $_POST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function pstring(string $key, ?string $default = null): ?string
    {
        return vstring(($_REQUEST[$key] ?? null), $default);
    }


    /**
     * Returns protected int from $_REQUEST
     *
     * @param string $key
     * @param int|null $default
     * @return int|null
     */
    public static function pint(string $key, ?int $default = null): ?int
    {
        $value = vint(($_REQUEST[$key] ?? null), $default);

        debug(sprintf('%s: %s', $key, $value));

        return $value;
    }
    
    /**
     * Returns protected universe from $_POST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function uni(string $key = 'uni', ?string $default = null): ?string
    {
        $value = vstring(($_POST[$key] ?? null), $default);

        if (is_null($value) || !in_array($value, Settings::UNIVERSE)) {
            return $default;
        }
        
        debug('Universe = ' . $value);

        return $value;
    }

    /**
     * Returns protected cluster from $_POST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function cluster(string $key = 'cluster', ?string $default = null): ?string
    {
        $value = vstring(($_POST[$key] ?? null), $default);

        if (is_null($value) || !in_array(strtoupper($value), Settings::CLUSTERS)) {
            return $default;
        }

        debug('Cluster = ' . $value);

        return $value;
    }

    /**
     * Returns protected sector from $_POST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function sector(string $key = 'sector', ?string $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }

    /**
     * Returns protected img_url from $_POST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function img_url(string $key = 'img_url', ?string $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }

    /**
     * Returns protected mode from $_POST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function mode(string $key = 'mode', ?string $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }

    /**
     * Returns protected shownpc from $_POST
     *
     * @param string $key
     * @param boolean|null $default
     * @return boolean
     */
    public static function shownpc(string $key = 'shownpc', ?bool $default = false): bool
    {
        $value = vbool(($_POST[$key] ?? null), $default);
        $value = '1' === $value ? true : false;

        debug('ShowNPC = ' . ($value ? 'true' : 'false'));

        return $value;
    }

    /**
     * Returns protected whole from $_POST
     *
     * @param string $key
     * @param boolean|null $default
     * @return boolean
     */
    public static function whole(string $key = 'whole', ?bool $default = false): bool
    {
        $value = vbool(($_POST[$key] ?? null), $default);
        $value = '1' === $value ? true : false;

        debug('WHole = ' . ($value ? 'true' : 'false'));

        return $value;
    }

    /**
     * Returns protected grid from $_POST
     *
     * @param string $key
     * @param boolean|null $default
     * @return boolean
     */
    public static function grid(string $key = 'grid', ?bool $default = false): bool
    {
        $value = vbool(($_POST[$key] ?? null), $default);
        $value = '1' === $value ? true : false;

        debug('Grid = ' . ($value ? 'true' : 'false'));

        return $value;
    }

    /**
     * Returns protected location from $_POST
     *
     * @param string $key
     * @param integer|null $default
     * @return integer|null
     */
    public static function loc(string $key = 'loc', ?int $default = null): ?int
    {
        return self::pint(key: $key, default: $default);
    }

    /**
     * Returns protected username from $_POST
     *
     * @param string $key
     * @param integer|null $default
     * @return string|null
     */
    public static function username(string $key = 'username', ?int $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }

    /**
     * Returns protected password from $_POST
     *
     * @param string $key
     * @param integer|null $default
     * @return string|null
     */
    public static function password(string $key = 'password', ?int $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }
}