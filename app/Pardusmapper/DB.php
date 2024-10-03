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

        $db->execute(sprintf('SELECT *, UTC_TIMESTAMP() AS today FROM %s_Maps WHERE id = ?', $universe), [
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

        $db->execute(sprintf('SELECT *, UTC_TIMESTAMP() AS today FROM %s_Buildings WHERE id = ?', $universe), [
            'i', $id
        ]);

        return $db->numRows() === 1 ? $db->fetchObject() : null;
    }

    /**
     * Get NPC by name
     *
     * @param string $name
     * @return object|null
     */
    public static function npc(string $name): object|null
    {
        $db = MySqlDB::instance();

        $db->execute('SELECT * FROM Pardus_Npcs WHERE name = ?', [
            's', $name
        ]);

        return $db->numRows() === 1 ? $db->fetchObject() : null;
    }

    /**
     * Get NPC by location
     *
     * @param int|null $id
     * @param string|null $universe
     * @return object|null
     */
    public static function npc_loc(?int $id,  ?string $universe): object|null
    {
        http_response(is_null($universe), ApiResponse::BADREQUEST, 'universe is required to load npc by location');

        if (is_null($id)) {
            return null;
        }

        $db = MySqlDB::instance();

        $db->execute(sprintf('SELECT *, UTC_TIMESTAMP() AS today FROM %s_Test_Npcs WHERE (deleted is null or deleted = 0) and id = ?', $universe), [
            'i', $id
        ]);

        return $db->numRows() === 1 ? $db->fetchObject() : null;
    }

    /**
     * Get user
     *
     * @param int|null $id
     * @param string $username
     * @param string|null $universe
     * @return object|null
     */
    public static function user(?int $id = null, ?string $username = null, ?string $universe = null): object|null
    {
        http_response(is_null($universe), ApiResponse::BADREQUEST, 'universe is required to load user');

        if (is_null($id) && is_null($username)) {
            return null;
        }

        $db = MySqlDB::instance();

        if ($username) {
            $db->execute(sprintf('SELECT *, UTC_TIMESTAMP() AS today FROM %s_Users WHERE LOWER(username) = ?', $universe), [
                's', $username
            ]);
        } else {
            $db->execute(sprintf('SELECT *, UTC_TIMESTAMP() AS today FROM %s_Users WHERE id = ?', $universe), [
                'i', $id
            ]);
        }


        return $db->numRows() === 1 ? $db->fetchObject() : null;
    }

    public static function static_locations(): array
    {
        $db = MySqlDB::instance();

        // Perform the SELECT query
        $db->execute("SELECT * FROM Pardus_Static_Locations");

        // Check if the query was successful
        http_response($db->numRows() < 1, ApiResponse::BADREQUEST, 'Missing static locations');

        // Initialize an array to hold the results
        $static = [];

        // Fetch each row as an object
        while ($c = $db->fetchObject()) {
            $static[] = $c->id;
        }

        return $static;
    }
}