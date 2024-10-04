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
     * Get static building data
     *
     * @param string $name
     * @return object|array|null
     */
    public static function building_static(string $name, bool $limit = true): object|array|null
    {
        $db = MySqlDB::instance();

        $query = 'SELECT * FROM Pardus_Buildings_Data WHERE name = ?';
        if ($limit) {
            $query .= ' ORDER BY image LIMIT 1';
        }
        $db->execute($query, [
            's', $name
        ]);

        if ($db->numRows() === 1) {
            return $db->fetchObject();
        }

        $return = [];
        while($q = $db->nextObject()) { $return[] = $q; }

        return $return;
    }


    /**
     * Get NPC by name
     *
     * @param string $name
     * @return object|null
     */
    public static function npc_static(string $name): object|null
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
     * @param bool|null $excludeDeleted
     * @return object|null
     */
    public static function npc(?int $id,  ?string $universe, ?bool $excludeDeleted = true): object|null
    {
        http_response(is_null($universe), ApiResponse::BADREQUEST, 'universe is required to load npc by location');

        if (is_null($id)) {
            return null;
        }

        $db = MySqlDB::instance();

        if ($excludeDeleted) {
            $db->execute(sprintf('SELECT *, UTC_TIMESTAMP() AS today FROM %s_Test_Npcs WHERE (deleted is null or deleted = 0) and id = ?', $universe), [
                'i', $id
            ]);
        } else {
            $db->execute(sprintf('SELECT *, UTC_TIMESTAMP() AS today FROM %s_Test_Npcs WHERE id = ?', $universe), [
                'i', $id
            ]);
        }

        return $db->numRows() === 1 ? $db->fetchObject() : null;
    }

    /**
     * Get user
     *
     * @param int|null $id
     * @param string|null $username
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

    /**
     * Get static locations
     *
     * @return array
     */
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

    /**
     * Get stocks
     *
     * @param int|null $id
     * @param string|null $name
     * @param string|null $universe
     * @param bool|null $warStatus
     * @return array
     */
    public static function stocks(?int $id = null, ?string $name = null, ?string $universe = null, ?bool $warStatus = false, ?bool $nonZero = false): array
    {
        http_response(is_null($universe), ApiResponse::BADREQUEST, 'universe is required to load user');

        if (is_null($id)) {
            return null;
        }

        $bindType = [];
        $bindValues = [];

        $query = sprintf('SELECT * FROM %s_New_Stock WHERE id = ?', $universe);
        $bindType[] = 'i';
        $bindValues[] = $id;

        if ($name) {
            $query .= ' AND name = ?';
            $bindType[] = 's';
            $bindValues[] = $name;
        }

        if ($nonZero) {
            $query .= ' AND (amount > 0 OR max > 0)';
        }

        if ($warStatus) {
            $query .= ' AND (SELECT WarStatus FROM War_Status where Universe = ?) = 0';
            $bindType[] = 's';
            $bindValues[] = $universe;
        }

        $params = [];
        $params[] = implode('', $bindType);
        $params = array_merge($params, $bindValues);

        $db = MySqlDB::instance();
        $db->execute($query, $params);

        $stocks = [];
        while($q = $db->nextObject()) { $stocks[] = $q; }

        return $stocks;
    }

    /**
     * Get stock by location id
     *
     * @param integer|null $id
     * @param string|null $name
     * @param string|null $universe
     * @return object|array|null
     */
    public static function building_stock(?int $id = null, ?string $name = null, ?string $universe = null): object|array|null
    {
        http_response(is_null($universe), ApiResponse::BADREQUEST, 'universe is required to load map by location');

        if (is_null($id)) {
            return null;
        }

        $db = MySqlDB::instance();

        if ($id && $name) {
            $db->execute(sprintf('SELECT *, UTC_TIMESTAMP() AS today FROM %s_New_Stock WHERE name = ? AND id = ?', $universe), [
                'si', $name, $id
            ]);

            return $db->numRows() === 1 ? $db->fetchObject() : null;
        } elseif ($id) {
            $db->execute(sprintf('SELECT *, UTC_TIMESTAMP() AS today FROM %s_New_Stock WHERE id = ?', $universe), [
                'i', $id
            ]);

            $stocks = [];
            while($q = $db->nextObject()) { $stocks[] = $q; }
    
            return $stocks;
        }
    }

    /**
     * Update building field
     *
     * @param integer $id
     * @param array $params
     * @param string|null $universe
     * @return boolean
     */
    public static function building_update(int $id, ?array $params, ?string $universe): bool
    {
        http_response(is_null($universe), ApiResponse::BADREQUEST, 'universe is required to load map by location');

        debug('Updating building fields');
        debug($id, $params);

        $bindType = [];
        $bindValues = [];
        $fields = [];
        foreach($params as $key => $value) {
            $fields[] = sprintf('`%s` = ?', $key);
            $bindValues[] = $value;
            if (is_int($value)) {
                $bindType[] = 'i';
            } elseif (is_float($value)) {
                $bindType[] = 'd';
            } else {
                $bindType[] = 's';
            }
        }

        $bindValues[] = $id;
        $bindType[] = 'i';

        $binds = [];
        $binds[] = implode('', $bindType);
        $binds = array_merge($binds, $bindValues);

        $db = MySqlDB::instance();

        $db->execute(sprintf('UPDATE %s_Buildings SET updated = UTC_TIMESTAMP(), %s WHERE id = ?', $universe, implode(', ', $fields)), $binds);

        return true;
    }


    /**
     * Update building cluster
     *
     * @param integer|null $id
     * @param string|null $cluster
     * @param string|null $universe
     * @return boolean
     */
    public static function building_update_cluster(?int $id, ?string $cluster, ?string $universe): bool
    {
        http_response(is_null($universe), ApiResponse::BADREQUEST, 'universe is required to load map by location');

        debug('Updating building cluster');

        $db = MySqlDB::instance();

        $db->execute(sprintf('UPDATE %s_Buildings SET cluster = ? WHERE id = ?', $universe), [
            'si', $cluster, $id
        ]);

        return true;
    }


    /**
     * Update building sector
     *
     * @param integer|null $id
     * @param string|null $sector
     * @param string|null $universe
     * @return boolean
     */
    public static function building_update_sector(?int $id, ?string $sector, ?string $universe): bool
    {
        http_response(is_null($universe), ApiResponse::BADREQUEST, 'universe is required to load map by location');

        debug('Updating building sector');

        $db = MySqlDB::instance();

        $db->execute(sprintf('UPDATE %s_Buildings SET sector = ? WHERE id = ?', $universe), [
            'si', $sector, $id
        ]);

        return true;
    }

    /**
     * Update building X, Y
     *
     * @param integer|null $id
     * @param integer|null $x
     * @param integer|null $y
     * @param string|null $universe
     * @return boolean
     */
    public static function building_update_xy(?int $id, ?int $x, ?int $y, ?string $universe): bool
    {
        http_response(is_null($universe), ApiResponse::BADREQUEST, 'universe is required to load map by location');

        debug('Updating building x,y');

        $db = MySqlDB::instance();

        $db->execute(sprintf('UPDATE %s_Buildings SET x = ?, y = ? WHERE id = ?', $universe), [
            'iii', $x, $y, $id
        ]);

        return true;
    }

    /**
     * Get resource data
     *
     * @param string $image
     * @return object|null
     */
    public static function res_data(string $image): object|null
    {
        $db = MySqlDB::instance();

        $db->execute('SELECT * FROM Pardus_Res_Data WHERE image = ?', [
            's', $image
        ]);

        return $db->numRows() === 1 ? $db->fetchObject() : null;
    }
}