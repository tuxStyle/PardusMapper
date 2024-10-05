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
     * @param string|null $name
     * @param string $universe
     * @return object|null
     */
    public static function building(string $universe, ?int $id = null, ?string $name = null,): object|null
    {
        if (empty($id) && empty($name)) {
            return null;
        }

        $query = sprintf('SELECT *, UTC_TIMESTAMP() AS today FROM %s_Buildings WHERE 1 = 1', $universe);
        $conditions = [];
        $bindType = [];
        $bindValues = [];

        if (!empty($name)) {
            $conditions[] = 'name = ?';
            $bindType[] = 's';
            $bindValues[] = $name;
        }

        if (!empty($id)) {
            $conditions[] = 'id = ?';
            $bindType[] = 'i';
            $bindValues[] = $id;
        }

        $query .= ' AND ' . implode(' AND ', $conditions);

        $params = [];
        $params[] = implode('', $bindType);
        $params = array_merge($params, $bindValues);

        $db = MySqlDB::instance();
        $db->execute($query, $params);

        return $db->numRows() === 1 ? $db->fetchObject() : null;
    }

    /**
     * Get static building data
     *
     * @param string|null $name
     * @param string|null $image
     * @return object|array|null
     */
    public static function building_static(?string $name = null, ?string $image = null, bool $limit = true): object|array|null
    {
        $db = MySqlDB::instance();

        if (is_null($name) && is_null($image)) {
            return null;
        }
        
        $query = 'SELECT * FROM Pardus_Buildings_Data WHERE 1 = 1';
        $conditions = [];
        $bindType = [];
        $bindValues = [];

        if (!empty($name)) {
            $conditions[] = 'name = ?';
            $bindType[] = 's';
            $bindValues[] = $name;
        }

        if (!empty($image)) {
            $conditions[] = 'image = ?';
            $bindType[] = 's';
            $bindValues[] = $image;
        }

        $query .= ' AND ' . implode(' AND ', $conditions);

        if ($limit) {
            $query .= ' ORDER BY image LIMIT 1';
        }

        $params = [];
        $params[] = implode('', $bindType);
        $params = array_merge($params, $bindValues);

        $db->execute($query, $params);

        if ($db->numRows() === 1) {
            return $db->fetchObject();
        }

        $return = [];
        while($q = $db->nextObject()) { $return[] = $q; }

        return $return;
    }

    /**
     * Get static upkeep data
     *
     * @param string|null $name
     * @param string|null $res
     * @param int|null $upkeep
     * @return object|array|null
     */
    public static function upkeep_static(?string $name = null, ?string $res = null, ?int $upkeep = null): object|array|null
    {
        $db = MySqlDB::instance();

        if (is_null($name) && is_null($res) && is_null($upkeep)) {
            return null;
        }
        
        $query = 'SELECT * FROM Pardus_Upkeep_Data WHERE 1 = 1';
        $conditions = [];
        $bindType = [];
        $bindValues = [];

        if (!empty($name)) {
            $conditions[] = 'name = ?';
            $bindType[] = 's';
            $bindValues[] = $name;
        }

        if (!empty($res)) {
            $conditions[] = 'res = ?';
            $bindType[] = 's';
            $bindValues[] = $res;
        }

        if (!is_null($upkeep)) {
            $conditions[] = 'upkeep = ?';
            $bindType[] = 'i';
            $bindValues[] = $upkeep;
        }

        $query .= ' AND ' . implode(' AND ', $conditions);

        $params = [];
        $params[] = implode('', $bindType);
        $params = array_merge($params, $bindValues);

        $db->execute($query, $params);

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
     * @param int $id
     * @param string|null $name
     * @param string $universe
     * @param bool $warStatus
     * @param bool $nonZero
     * @return array
     */
    public static function stocks(int $id, string $universe, ?string $name = null, bool $warStatus = false, bool $nonZero = false): array
    {
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
     * Create stocks row
     *
     * @param integer $id
     * @param string $name
     * @param string $universe
     * @return boolean
     */
    public static function stock_create(int $id, string $name, string $universe): bool
    {
        debug('Create stock', func_get_args());
        $db = MySqlDB::instance();

        $query = sprintf('INSERT INTO %s_New_Stock (id, name) VALUES (?, ?)', $universe);
        $params = ['is', $id, $name];

        debug($query, $params);
        $db->execute($query, $params);

        return true;
    }

    /**
     * Update stock fields
     *
     * @param integer $id
     * @param string $name
     * @param array $params
     * @param string $universe
     * @return boolean
     */
    public static function stock_update(int $id, string $name, array $params, string $universe): bool
    {
        debug('Updating stock fields', func_get_args());

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
        $bindValues[] = $name;
        $bindType[] = 's';

        $binds = [];
        $binds[] = implode('', $bindType);
        $binds = array_merge($binds, $bindValues);
        $query = sprintf('UPDATE %s_New_Stock SET %s WHERE id = ? AND name = ?', $universe, implode(', ', $fields));

        $db = MySqlDB::instance();

        debug($query, $binds);
        $db->execute($query, $binds);

        return true;
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
     * Update building fields
     *
     * @param integer $id
     * @param array $params
     * @param string $universe
     * @return boolean
     */
    public static function building_update(int $id, array $params, string $universe): bool
    {
        if (0 === count($params)) {
            return false;
        }

        debug('Updating building fields', $id, $params);

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
     * Update building stock fields
     *
     * @param integer $id
     * @param array $params
     * @param string $universe
     * @return boolean
     */
    public static function building_stock_update(int $id, array $params, string $universe): bool
    {
        if (0 === count($params)) {
            return false;
        }

        debug('Updating building stock fields', $id, $params);

        $bindType = [];
        $bindValues = [];
        $fields = [];
        $date = null;

        if (in_array('date', array_keys($params))) {
            $date = $params['date'];
            unset($params['date']);
            $bindType[] = 's';
            $bindValues[] = $date;
        }

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
        $query = sprintf(
            'UPDATE %s_Buildings SET stock_updated = %s %s WHERE id = ?'
            , $universe
            , (is_null($date) ? 'UTC_TIMESTAMP()' : 'STR_TO_DATE(?, \'%a %b %e %T GMT %Y\')')
            , (count($fields) === 0 ? '' : ', ' . implode(', ', $fields))
        );

        debug($query, $binds);
        $db = MySqlDB::instance();
        $db->execute($query, $binds);

        return true;
    }

    /**
     * Update building equipment fields
     *
     * @param integer $id
     * @param array $params
     * @param string $universe
     * @return boolean
     */
    public static function building_equipment_update(int $id, array $params, string $universe): bool
    {
        if (0 === count($params)) {
            return false;
        }

        debug('Updating building equipment fields');
        debug($id, $params);

        $bindType = [];
        $bindValues = [];
        $fields = [];
        $date = null;

        if (in_array('date', array_keys($params))) {
            $date = $params['date'];
            unset($params['date']);
            $bindType[] = 's';
            $bindValues[] = $date;
        }

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

        $db->execute(sprintf(
            'UPDATE %s_Buildings SET eq_updated = %s %s WHERE id = ?'
            , (is_null($date) ? 'UTC_TIMESTAMP()' : 'STR_TO_DATE(?, \'%a %b %e %T GMT %Y\')')
            , $universe
            , (count($fields) === 0 ? '' : ', ' . implode(', ', $fields))
        ), $binds);

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

    /**
     * Update crew fields
     *
     * @param string $name
     * @param array $params
     * @param string $universe
     * @return boolean
     */
    public static function crew_update(string $name, array $params, string $universe): bool
    {
        debug('Updating crew fields');
        debug($name, $params);

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

        $bindValues[] = $name;
        $bindType[] = 's';

        $binds = [];
        $binds[] = implode('', $bindType);
        $binds = array_merge($binds, $bindValues);

        $db = MySqlDB::instance();

        debug(sprintf('UPDATE %s_Crew SET updated = UTC_TIMESTAMP(), %s WHERE name = ?', $universe, implode(', ', $fields)), $binds);
        $db->execute(sprintf('UPDATE %s_Crew SET updated = UTC_TIMESTAMP(), %s WHERE name = ?', $universe, implode(', ', $fields)), $binds);

        return true;
    }

    /**
     * Create crew row
     *
     * @param string $name
     * @param integer $location
     * @param string $universe
     * @return boolean
     */
    public static function crew_create(string $name, int $location, string $universe): bool
    {
        debug('Create crew');
        debug(func_get_args());

        $db = MySqlDB::instance();

        $query = sprintf('INSERT INTO %s_Crew (name,loc) VALUES (?, ?)', $universe);
        $params = ['si', $name, $location];
        debug($query, $params);
        $db->execute($query, $params);

        return true;
    }

    /**
     * Get equipment
     *
     * @param string $name
     * @param integer $location
     * @param string $universe
     * @return object|null
     */
    public static function equipment(string $name, int $location, string $universe): object|null
    {
        if (empty($name)) {
            return null;
        }

        $db = MySqlDB::instance();

        $query = sprintf('SELECT *, UTC_TIMESTAMP() AS today FROM %s_Equipment WHERE loc = ? AND name = ?', $universe);
        $db->execute($query, [
            'is', $location, $name
        ]);

        return $db->numRows() === 1 ? $db->fetchObject() : null;
    }

    /**
     * Create equipment row
     *
     * @param string $name
     * @param integer $location
     * @param string $universe
     * @return boolean
     */
    public static function equipment_create(string $name, int $location, string $universe): bool
    {
        debug('Create equpment', func_get_args());
        $db = MySqlDB::instance();

        $query = sprintf('INSERT INTO %s_Equipment (name,loc) VALUES (?, ?)', $universe);
        $params = ['si', $name, $location];

        debug($query, $params);
        $db->execute($query, $params);

        return true;
    }

    /**
     * Update equipment fields
     *
     * @param string $name
     * @param integer $location
     * @param array $params
     * @param string $universe
     * @return boolean
     */
    public static function equipment_update(string $name, int $location, array $params, string $universe): bool
    {
        debug('Updating equipment fields', func_get_args());

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

        $bindValues[] = $location;
        $bindType[] = 'i';
        $bindValues[] = $name;
        $bindType[] = 's';

        $binds = [];
        $binds[] = implode('', $bindType);
        $binds = array_merge($binds, $bindValues);
        $query = sprintf('UPDATE %s_Equipment SET %s WHERE loc = ? AND name = ?', $universe, implode(', ', $fields));

        $db = MySqlDB::instance();

        debug($query, $binds);
        $db->execute($query, $binds);

        return true;
    }

}