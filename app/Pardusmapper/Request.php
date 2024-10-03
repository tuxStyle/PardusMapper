<?php

declare(strict_types=1);

namespace Pardusmapper;

use Pardusmapper\Core\Settings;

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
            return $default;
        }

        debug('Cluster = ' . $value);

        return $value;
    }

    /**
     * Returns protected sector from $_REQUEST
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
     * Returns protected gems from $_REQUEST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function gems(string $key = 'gems', ?string $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }

    // /**
    //  * Returns protected security from $_REQUEST
    //  *
    //  * @param string $key
    //  * @param integer $default
    //  * @return integer
    //  */
    // public static function security(string $key = 'security', int $default = 0): int
    // {
    //     return vint(($_REQUEST[$key] ?? null), $default);
    // }

    /**
     * Returns protected dl from $_REQUEST
     *
     * @param string $key
     * @param integer|null $default
     * @return integer|null
     */
    public static function dl(string $key = 'dl', ?int $default = null): ?int
    {
        return self::pint(key: $key, default: $default);
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
        $version = vfloat(($_REQUEST[$key] ?? null), $default);

        debug(sprintf('%s: %s', $key, $version));

        return $version;
    }

    /**
     * Returns protected location from $_REQUEST
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
     * Returns protected name from $_REQUEST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function name(string $key = 'name', ?string $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }

    /**
     * Returns protected img from $_REQUEST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function img(string $key = 'img', ?string $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }

    /**
     * Returns protected faction from $_REQUEST
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
     * Returns protected syndicate from $_REQUEST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function syndicate(string $key = 'syndicate', ?string $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }

    /**
     * Returns protected owner from $_REQUEST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function owner(string $key = 'owner', ?string $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }

    /**
     * Returns protected alliance from $_REQUEST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function alliance(string $key = 'alliance', ?string $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }

    /**
     * Returns protected population from $_REQUEST
     *
     * @param string $key
     * @param integer|null $default
     * @return integer|null
     */
    public static function pop(string $key = 'pop', ?int $default = null): ?int
    {
        return self::pint(key: $key, default: $default);
    }

    /**
     * Returns protected crime from $_REQUEST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function crime(string $key = 'crime', ?string $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }

    /**
     * Returns protected credit from $_REQUEST
     *
     * @param string $key
     * @param integer|null $default
     * @return integer|null
     */
    public static function credit(string $key = 'credit', ?int $default = null): ?int
    {
        return self::pint(key: $key, default: $default);
    }

    /**
     * Returns protected condition from $_REQUEST
     *
     * @param string $key
     * @param integer|null $default
     * @return integer|null
     */
    public static function condition(string $key = 'condition', ?int $default = null): ?int
    {
        return self::pint(key: $key, default: $default);
    }

    /**
     * Returns protected x from $_REQUEST
     *
     * @param string $key
     * @param integer|null $default
     * @return integer|null
     */
    public static function x(string $key = 'x', ?int $default = null): ?int
    {
        return self::pint(key: $key, default: $default);
    }

    /**
     * Returns protected y from $_REQUEST
     *
     * @param string $key
     * @param integer|null $default
     * @return integer|null
     */
    public static function y(string $key = 'y', ?int $default = null): ?int
    {
        return self::pint(key: $key, default: $default);
    }

    /**
     * Returns protected visited SB from $_REQUEST
     *
     * @param string $key
     * @param boolean|null $default
     * @return boolean
     */
    public static function sb(string $key = 'sb', ?bool $default = false): bool
    {
        return vbool(($_REQUEST[$key] ?? null), $default);
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
     * Returns protected free space from $_REQUEST
     *
     * @param string $key
     * @param integer|null $default
     * @return integer|null
     */
    public static function fs(string $key = 'fs', ?int $default = null): ?int
    {
        return self::pint(key: $key, default: $default);
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
            return $default;
        }

        $ssq = explode('~', (string) $squads);

        debug($ssq);

        return $ssq;
    }

    /**
     * Returns protected visited SB building from $_REQUEST
     *
     * @param string $key
     * @param boolean|null $default
     * @return boolean
     */
    public static function sbb(string $key = 'sbb', ?bool $default = false): bool
    {
        return vbool(($_REQUEST[$key] ?? null), $default);
    }

    /**
     * Returns protected mission ID from $_REQUEST
     *
     * @param string $key
     * @param integer|null $default
     * @return integer|null
     */
    public static function mid(string $key = 'mid', ?int $default = null): ?int
    {
        return self::pint(key: $key, default: $default);
    }

    /**
     * Returns protected rank from $_REQUEST
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
     * Returns protected competency from $_REQUEST
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
            return $default;
        }

        $missionList = explode('~', (string) $mission);

        debug($missionList);

        return $missionList;
    }

    /**
     * Returns protected mapdata from $_REQUEST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function mapdata(string $key = 'mapdata', ?string $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }

    /**
     * Returns protected userID from $_REQUEST
     *
     * @param string $key
     * @param integer|null $default
     * @return integer|null
     */
    public static function uid(string $key = 'uid', ?int $default = null): ?int
    {
        return self::pint(key: $key, default: $default);
    }

    /**
     * Returns protected user name from $_REQUEST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function user(string $key = 'user', ?string $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }

    /**
     * Returns protected url from $_REQUEST
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function url(string $key = 'url', ?string $default = null): ?string
    {
        return self::pstring(key: $key, default: $default);
    }
}
