<?php

declare(strict_types=1);

namespace Pardusmapper;

use Pardusmapper\Core\Settings;
use Pardusmapper\Core\MySqlDB;

class Request
{
    /**
     * Returns protected string from $_REQUEST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function pstring(string $key, ?string $default = null): ?string
    {
        $value = vstring(($_REQUEST[$key] ?? null), $default);

        if (!is_null($value)) {
            $value = MySqlDB::instance()->protect($value);
        }

        debug(sprintf('%s: %s', $key, $value));

        return $value;
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
     * Returns protected bool from $_REQUEST
     *
     * @param string $key
     * @param bool $default
     * @return bool
     */
    public static function pbool(string $key, bool $default = false): bool
    {
        $value = vbool(($_REQUEST[$key] ?? null), $default);

        debug(sprintf('%s: %s', $key, $value ? 'true' : 'false'));

        return $value;
    }

    /**
     * Returns protected float from $_REQUEST
     *
     * @param string $key
     * @param float|null $default
     * @return float|null
     */
    public static function pfloat(string $key, ?float $default = null): ?float
    {
        $value = vfloat(($_REQUEST[$key] ?? null), $default);

        debug(sprintf('%s: %s', $key, $value));

        return $value;
    }

    /**
     * Returns protected universe from $_REQUEST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function uni(string $key = 'uni', ?string $default = null): ?string
    {
        $value = vstring(($_REQUEST[$key] ?? null), $default);

        if (is_null($value) || !in_array($value, Settings::UNIVERSE)) {
            debug('Universe = ' . $default);
            return $default;
        }
        
        debug('Universe = ' . $value);

        return $value;
    }

    /**
     * Returns protected cluster from $_REQUEST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function cluster(string $key = 'cluster', ?string $default = null): ?string
    {
        $value = vstring(($_REQUEST[$key] ?? null), $default);

        if (is_null($value) || !in_array(strtoupper($value), Settings::CLUSTERS)) {
            debug('Cluster = ' . $default);
            return $default;
        }

        debug('Cluster = ' . $value);

        return $value;
    }

    /**
     * Returns protected debug from $_REQUEST
     *
     * @param string $key
     * @param int|null $default
     * @return int
     */
    public static function debug(string $key = 'debug', ?int $default = 0): int
    {
        $debug = vint(($_REQUEST[$key] ?? null), $default);

        // override Settings if the debug is forced to a new value
        Settings::$DEBUG = $debug;

        debug(sprintf('%s: %s', $key, $debug ? 'true' : 'false'));

        return $debug;
    }

    /**
     * Returns protected version from $_REQUEST
     *
     * @param string $key
     * @param float $default
     * @return float
     */
    public static function version(string $key = 'version', float $default = 0): float
    {
        return self::pfloat($key, $default);
    }

    /**
     * Returns protected starbase trade data from $_REQUEST
     *
     * @param string $key
     * @param array|null $default
     * @return array|null
     */
    public static function sbt(string $key = 'sbt', ?array $default = []): ?array
    {
        $trade = vstring(($_REQUEST[$key] ?? null));

        if (is_null($trade)) {
            debug($default);
            return $default;
        }

        $sbt = explode('~', (string) $trade);

        debug($sbt);

        return $sbt;
    }

    /**
     * Returns protected starbase squads data from $_REQUEST
     *
     * @param string $key
     * @param array|null $default
     * @return array|null
     */
    public static function squads(string $key = 'squads', ?array $default = []): ?array
    {
        $squads = vstring(($_REQUEST[$key] ?? null));

        if (is_null($squads)) {
            debug($default);
            return $default;
        }

        $squadsList = explode('~', (string) $squads);

        debug($squadsList);

        return $squadsList;
    }

    /**
     * Returns protected mission data from $_REQUEST
     *
     * @param string $key
     * @param array|null $default
     * @return array|null
     */
    public static function mission(string $key = 'mission', ?array $default = []): ?array
    {
        $mission = vstring(($_REQUEST[$key] ?? null));

        if (is_null($mission)) {
            debug($default);
            return $default;
        }

        $missionList = explode('~', (string) $mission);

        debug($missionList);

        return $missionList;
    }
}
