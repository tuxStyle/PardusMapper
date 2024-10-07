<?php
declare(strict_types=1);

namespace Pardusmapper;

class Coordinates
{
    public static function getX($id, $s_id, $rows): int
    {
        return (int)floor(($id - $s_id) / $rows);
    }

    public static function getY($id, $s_id, $rows, $x)
    {
        return $id - ($s_id + ($x * $rows));
    }

    public static function getID($s_id, $rows, $x, $y)
    {
        return $s_id + ($rows * $x) + $y;
    }
}
