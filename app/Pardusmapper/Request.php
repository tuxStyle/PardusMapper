<?php
declare(strict_types=1);

namespace Pardusmapper;

use Pardusmapper\Core\MySqlDB;
use Pardusmapper\Core\Settings;

class Request
{
    /**
     * Returns protected universe from $_REQUEST
     *
     * @return string|null
     */
    public static function uni(): ?string
    {
        $uni = $_REQUEST['uni'] ?? null;

        if(is_null($uni) || !in_array($uni, Settings::UNIVERSE)) {
            return null;
        }

        return $uni;

        // if(is_null($uni)) {
        //     return null;
        // }

        // return MySqlDB::instance()->protect($uni);
    } 

    /**
     * Returns protected cluster from $_REQUEST
     *
     * @return string|null
     */
    public static function cluster(): ?string
    {
        $cluster = $_REQUEST['cluster'] ?? null;

        if(is_null($cluster) || !in_array(strtoupper($cluster), Settings::CLUSTERS)) {
            return null;
        }

        return $cluster;

        // if(is_null($cluster)) {
        //     return null;
        // }

        // return MySqlDB::instance()->protect($cluster);
    }

    /**
     * Returns protected sector from $_REQUEST
     *
     * @return string|null
     */
    public static function sector(): ?string
    {
        $sector = $_REQUEST['sector'] ?? null;

        if(is_null($sector)) {
            return null;
        }

        return MySqlDB::instance()->protect($sector);
    } 


    /**
     * Returns protected gems from $_REQUEST
     *
     * @return string|null
     */
    public static function gems(): ?string
    {
        $gems = $_REQUEST['gems'] ?? null;

        if(is_null($gems)) {
            return null;
        }

        return MySqlDB::instance()->protect($gems);
    } 


    /**
     * Returns protected security from $_REQUEST
     *
     * @return int
     */
    public static function security(): int
    {
        $security = $_REQUEST['security'] ?? null;

        if(is_null($security) || !preg_match('/\d+/', $security)) {
            return 0;
        }

        return (int)$security;
    }

    /**
     * Returns protected x2 from $_REQUEST
     *
     * @return int|null
     */
    public static function x2(): ?int
    {
        $x2 = $_REQUEST['x2'] ?? null;

        if(is_null($x2) || !preg_match('/\d+/', $x2)) {
            return null;
        }

        return (int)$x2;
    } 

    /**
     * Returns protected y2 from $_REQUEST
     *
     * @return int|null
     */
    public static function y2(): ?int
    {
        $y2 = $_REQUEST['y2'] ?? null;

        if(is_null($y2) || !preg_match('/\d+/', $y2)) {
            return null;
        }

        return (int)$y2;
    }

    /**
     * Returns protected dl from $_REQUEST
     *
     * @return int|null
     */
    public static function dl(): ?int
    {
        $dl = $_REQUEST['dl'] ?? null;

        if(is_null($dl) || !preg_match('/\d+/', $dl)) {
            return null;
        }

        return (int)$dl;
    }

}