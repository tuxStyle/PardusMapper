<?php
declare(strict_types=1);

namespace Pardusmapper;

use Pardusmapper\Core\MySqlDB;
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
        $value = vstring(($_POST[$key] ?? null), $default);

        if (!is_null($value)) {
            $value = MySqlDB::instance()->protect($value);
        }

        debug(sprintf('%s: %s', $key, $value));

        return $value;
    }

    /**
     * Returns protected int from $_POST
     *
     * @param string $key
     * @param int|null $default
     * @return int|null
     */
    public static function pint(string $key, ?int $default = null): ?int
    {
        $value = vint(($_POST[$key] ?? null), $default);

        debug(sprintf('%s: %s', $key, $value));

        return $value;
    }

    /**
     * Returns protected bool from $_POST
     *
     * @param string $key
     * @param bool $default
     * @return bool
     */
    public static function pbool(string $key, bool $default = false): bool
    {
        $value = vbool(($_POST[$key] ?? null), $default);

        debug(sprintf('%s: %s', $key, $value ? 'true' : 'false'));

        return $value;
    }
    
    /**
     * Returns protected float from $_POST
     *
     * @param string $key
     * @param float|null $default
     * @return float|null
     */
    public static function pfloat(string $key, ?float $default = null): ?float
    {
        $value = vfloat(($_POST[$key] ?? null), $default);

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
            debug('Universe = ' . $default);
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
            debug('Cluster = ' . $default);
            return $default;
        }

        debug('Cluster = ' . $value);

        return $value;
    }
}