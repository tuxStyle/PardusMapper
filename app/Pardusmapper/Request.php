<?php

declare(strict_types=1);

namespace Pardusmapper;

use Pardusmapper\Core\MySqlDB;
use Pardusmapper\Core\Settings;

class Request
{
    /**
     * validate required value as integer
     *
     * @param string|null $value
     * @param integer|null $default
     * @return integer|null
     */
    protected static function _int(?string $value, ?int $default = null): ?int
    {
        $value = vnull($value);

        if (is_null($value) || !preg_match('/^\d+$/', $value)) {
            return $default;
        }

        return (int)$value;
    }

    /**
     * validate required value as string
     *
     * @param string|null $value
     * @param string|null $default
     * @return string|null
     */
    protected static function _string(?string $value, ?string $default = null): ?string
    {
        $value = vnull($value);

        if (is_null($value)) {
            return $default;
        }

        return MySqlDB::instance()->protect($value);
    }

    /**
     * validate required value as boolean
     *
     * @param string|null $value
     * @param bool|null $default
     * @return bool|null
     */
    protected static function _bool(?string $value, ?bool $default = false): bool
    {
        $value = vnull($value);

        if (is_null($value) || !preg_match('/^(true|false)$/i', $value)) {
            return $default;
        }

        return 'true' === strtolower($value) ? true : false;
    }

    /**
     * validate required value as float
     *
     * @param string|null $value
     * @param float|null $default
     * @return float|null
     */
    protected static function _float(?string $value, float $default = 0): float
    {
        $value = vnull($value);

        if (is_null($value) || !preg_match('/^\d+(\.\d+)?$/', $value)) {
            return $default;
        }

        return (float)$value;
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
        $value = self::_string(($_REQUEST[$key] ?? null), $default);

        if (is_null($value) || !in_array($value, Settings::UNIVERSE)) {
            return $default;
        }
        
        if (Settings::$DEBUG) {
            echo 'Universe = ' . $value . '<br>';
        }

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
        $value = self::_string(($_REQUEST[$key] ?? null), $default);

        if (is_null($value) || !in_array(strtoupper($value), Settings::CLUSTERS)) {
            return $default;
        }

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
        return self::_string(($_REQUEST[$key] ?? null), $default);
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
        return self::_string(($_REQUEST[$key] ?? null), $default);
    }

    /**
     * Returns protected security from $_REQUEST
     *
     * @param string $key
     * @param integer $default
     * @return integer
     */
    public static function security(string $key = 'security', int $default = 0): int
    {
        return self::_int(($_REQUEST[$key] ?? null), $default);
    }

    /**
     * Returns protected dl from $_REQUEST
     *
     * @param string $key
     * @param integer|null $default
     * @return integer|null
     */
    public static function dl(string $key = 'dl', ?int $default = null): ?int
    {
        return self::_int(($_REQUEST[$key] ?? null), $default);
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
        $debug = self::_int(($_REQUEST[$key] ?? null), $default);

        // override Settings if the debug is forced to a new value
        Settings::$DEBUG = $debug;

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
        $version = self::_float(($_REQUEST[$key] ?? null), $default);

        if (Settings::$DEBUG) echo 'Version = ' . $version . '<br>';

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
        $loc = self::_int(($_REQUEST[$key] ?? null), $default);

        if (Settings::$DEBUG) {
            echo 'Location = ' . $loc . '<br>';
        }

        return $loc;
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
        return self::_string(($_REQUEST[$key] ?? null), $default);
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
        return self::_string(($_REQUEST[$key] ?? null), $default);
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
        return self::_string(($_REQUEST[$key] ?? null), $default);
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
        return self::_string(($_REQUEST[$key] ?? null), $default);
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
        return self::_string(($_REQUEST[$key] ?? null), $default);
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
        return self::_string(($_REQUEST[$key] ?? null), $default);
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
        return self::_int(($_REQUEST[$key] ?? null), $default);
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
        return self::_string(($_REQUEST[$key] ?? null), $default);
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
        return self::_int(($_REQUEST[$key] ?? null), $default);
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
        return self::_int(($_REQUEST[$key] ?? null), $default);
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
        return self::_int(($_REQUEST[$key] ?? null), $default);
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
        return self::_int(($_REQUEST[$key] ?? null), $default);
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
        return self::_bool(($_REQUEST[$key] ?? null), $default);
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
        $trade = self::_string(($_REQUEST[$key] ?? null));

        if (is_null($trade)) {
            return $default;
        }

        $sbt = explode('~', (string) $trade);

        if (Settings::$DEBUG) {
            print_r($sbt);
            echo '<br>';
        }

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
        return self::_int(($_REQUEST[$key] ?? null), $default);
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
        $squads = self::_string(($_REQUEST[$key] ?? null));

        if (is_null($squads)) {
            return $default;
        }

        if (Settings::$DEBUG) {
            echo 'Visited Squadrons<br>';
        }

        $ssq = explode('~', (string) $squads);

        if (Settings::$DEBUG) {
            print_r($ssq);
            echo '<br>';
        }

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
        return self::_bool(($_REQUEST[$key] ?? null), $default);
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
        return self::_int(($_REQUEST[$key] ?? null), $default);
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
        return self::_int(($_REQUEST[$key] ?? null), $default);
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
        return self::_int(($_REQUEST[$key] ?? null), $default);
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
        $mission = self::_string(($_REQUEST[$key] ?? null));

        if (is_null($mission)) {
            return $default;
        }

        $missionList = explode('~', (string) $mission);

        if (Settings::$DEBUG) {
            xp($missionList);
            echo '<br>';
        }

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
        return self::_string(($_REQUEST[$key] ?? null), $default);
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
        return self::_int(($_REQUEST[$key] ?? null), $default);
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
        return self::_string(($_REQUEST[$key] ?? null), $default);
    }

}
