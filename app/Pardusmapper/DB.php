<?php
declare(strict_types=1);

namespace Pardusmapper;

use Pardusmapper\Core\ApiResponse;
use Pardusmapper\Core\MySqlDB;

class DB
{
    /**
     * Get cluster by sector
     *
     * @param integer|null $id
     * @param string|null $code
     * @param string|null $sector
     * @return object|null
     */
    public static function cluster(?int $id = null, ?string $code = null, ?string $sector = null): object|null
    {
        if (is_null($id) && is_null($code) && is_null($sector)) {
            return null;
        }

        $db = MySqlDB::instance();

        if($sector) {
            $db->execute('SELECT * FROM Pardus_Clusters WHERE c_id = (SELECT c_id FROM Pardus_Sectors WHERE name = ?)', [
                's', $sector
            ]);
        } else if($code) {
            $db->execute('SELECT * FROM Pardus_Clusters WHERE code = ?', [
                's', $code
            ]);
        } else {
            $db->execute('SELECT * FROM Pardus_Clusters WHERE c_id = ?', [
                'i', $id
            ]);
        }

        return $db->numRows() === 1 ? $db->fetchObject() : null;
    }

    /**
     * Get sector by sector id or name
     *
     * @param integer|null $id
     * @param string|null $sector
     * @return object|null
     */
    public static function sector(?int $id = null, ?string $sector = null): object|null
    {
        if (is_null($id) && is_null($sector)) {
            return null;
        }

        $db = MySqlDB::instance();

        if($sector) {
            $db->execute('SELECT * FROM Pardus_Sectors WHERE name = ?', [
                's', $sector
            ]);
        } else {
            // TODO: check the code but this should probably be
            // SELECT * FROM Pardus_Sectors WHERE s_id = ?
            // so mysql will not have to scan that many rows

            $db->execute('SELECT * FROM Pardus_Sectors WHERE s_id <= ? ORDER BY s_id DESC LIMIT 1', [
                'i', $id
            ]);
        }

        return $db->numRows() === 1 ? $db->fetchObject() : null;
    }

    /**
     * Get map by location id
     *
     * @param integer|null $id
     * @param string|null $universe
     * @return object|null
     */
    public static function map(?int $id, ?string $universe): object|null
    {
        http_response(is_null($universe), ApiResponse::BADREQUEST, 'universe is required to load map by location');

        if (is_null($id)) {
            return null;
        }

        $db = MySqlDB::instance();

        $db->execute(sprintf('SELECT * FROM %_Maps WHERE id = ?', $universe), [
            'i', $id
        ]);

        if ($db->numRows() != 1) {
            return null;
        }

        return $db->numRows() === 1 ? $db->fetchObject() : null;
    }

    /**
     * Get building by location id
     *
     * @param integer|null $id
     * @param string|null $universe
     * @return object|null
     */
    public static function building(?int $id, ?string $universe): object|null
    {
        http_response(is_null($universe), ApiResponse::BADREQUEST, 'universe is required to load map by location');

        if (is_null($id)) {
            return null;
        }

        $db = MySqlDB::instance();

        $db->execute(sprintf('SELECT * FROM %_Buildings WHERE id = ?', $universe), [
            'i', $id
        ]);

        return $db->numRows() === 1 ? $db->fetchObject() : null;
    }
}