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
     * @param string $universe
     * @param integer|null $id
     * @return object|null
     */
    public static function map(string $universe, ?int $id): object|null
    {
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
     * @param string $universe
     * @param integer|null $id
     * @param string|null $name
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
     * @param string $universe
     * @param int|null $id
     * @param bool|null $excludeDeleted
     * @return object|null
     */
    public static function npc(string $universe, ?int $id, ?bool $excludeDeleted = true): object|null
    {
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
     * @param string $universe
     * @param int|null $id
     * @param string|null $username
     * @return object|null
     */
    public static function user(string $universe, ?int $id = null, ?string $username = null): object|null
    {
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
     * @param string $universe
     * @param integer|null $id
     * @param string|null $name
     * @param bool $warStatus
     * @param bool $nonZero
     * @return array
     */
    public static function stocks(string $universe, ?int $id, ?string $name = null, bool $warStatus = false, bool $nonZero = false): array
    {
        if (is_null($id)) {
            return null;
        }

        $bindType = [];
        $bindValues = [];

        $query = sprintf('SELECT *, UTC_TIMESTAMP() AS today FROM %s_New_Stock WHERE id = ?', $universe);
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
     * @param string $universe
     * @param integer $id
     * @param string $name
     * @return boolean
     */
    public static function stock_create(string $universe, int $id, string $name): bool
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
     * @param string $universe
     * @param integer $id
     * @param string $name
     * @param array $params
     * @return boolean
     */
    public static function stock_update(string $universe, int $id, string $name, array $params): bool
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
     * @param string $universe
     * @param integer|null $id
     * @param string|null $name
     * @return object|array|null
     */
    public static function building_stock(string $universe, ?int $id, ?string $name = null): object|array|null
    {
        if (is_null($id)) {
            return null;
        }

        $stocks = self::stocks(universe: $universe, id: $id, name: $name);

        switch(count($stocks)) {
            case 0: return null;
            case 1: return array_shift($stocks);
            default: return $stocks;
        }
    }

    /**
     * Update building fields
     *
     * @param string $universe
     * @param integer $id
     * @param array $params
     * @return boolean
     */
    public static function building_update(string $universe, int $id, array $params): bool
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
     * @param string $universe
     * @param integer $id
     * @param array $params
     * @return boolean
     */
    public static function building_stock_update(string $universe, int $id, array $params): bool
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
     * @param string $universe
     * @param integer $id
     * @param array $params
     * @return boolean
     */
    public static function building_equipment_update(string $universe, int $id, array $params): bool
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
     * Returns startc pardus res data
     */
    public static function res_data_static(): array
    {
        $db = MySqlDB::instance();

        $res_img = [];
        $res_id = [];

        $db->execute('SELECT * FROM Pardus_Res_Data');
        while ($q = $db->nextObject()) {
            $res_img[$q->name] = $q->image;
            $res_id[$q->name] = $q->r_id;
        }

        return [$res_img, $res_id];
    }

    /**
     * Update crew fields
     *
     * @param string $universe
     * @param string $name
     * @param array $params
     * @return boolean
     */
    public static function crew_update(string $universe, string $name, array $params): bool
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
     * @param string $universe
     * @param string $name
     * @param integer $location
     * @return boolean
     */
    public static function crew_create(string $universe, string $name, int $location): bool
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
     * @param string $universe
     * @param string $name
     * @param integer $location
     * @return object|null
     */
    public static function equipment(string $universe, string $name, int $location): object|null
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
     * @param string $universe
     * @param string $name
     * @param integer $location
     * @return boolean
     */
    public static function equipment_create(string $universe, string $name, int $location): bool
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
     * @param string $universe
     * @param string $name
     * @param integer $location
     * @param array $params
     * @return boolean
     */
    public static function equipment_update(string $universe, string $name, int $location, array $params): bool
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

    /**
     * Returns startc sectors list
     */
    public static function sectors_static(): array
    {
        $db = MySqlDB::instance();

        $sector = [];
        $db->execute('SELECT * from Pardus_Sectors order by name');
        while ($s = $db->nextObject()) { $sector[] = $s; }

        return $sector;
    }

    /**
     * Save map info from pardus
     *
     * @param string $universe
     * @param string $image
     * @param integer $id
     * @param integer $sb
     * @return boolean
     */
    public static function add_map(string $universe, string $image, int $id, int $sb = 0): bool
    {
        $db = MySqlDB::instance();

        // if (preg_match('/^\d+$/', $image)) {
        //     debug(__FILE__, $image);
        //     $this->execute('SELECT image FROM background WHERE id = ?', [
        //         'i', $image
        //     ]);
        //     $dbImg = $this->fetchObject();
        //     $image = $dbImg->image;
        // }

        $db->execute(sprintf('INSERT INTO %s_Maps (`id`, `bg`, `security`) VALUES (?, ?, 0)', $universe), [
            'is', $id, $image
        ]);

        $params = [];

        if ($sb) {
            debug(__METHOD__, __LINE__, 'we have building');

            $b = self::building(universe: $universe, id: $sb);
            $x = Coordinates::getX($id,$b->starbase,13);
            $y = Coordinates::getY($id,$b->starbase,13,$x);

            $params['cluster'] = $b->cluster;
            $params['sector'] = $b->sector;
            $params['x'] = $x;
            $params['y'] = $y;            
        } else {
            debug(__METHOD__, __LINE__, 'no building');

            $s = self::sector(id: $id);  // Here, $id is passed as expected
            $c = self::cluster(id: $s->c_id);  // Assuming this is correct
            $x = Coordinates::getX($id,$s->s_id,$s->rows);
            $y = Coordinates::getY($id,$s->s_id,$s->rows,$x);

            $params['cluster'] = $c->name;
            $params['sector'] = $s->name;
            $params['x'] = $x;
            $params['y'] = $y;            
        }

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
        $query = sprintf('UPDATE %s_Maps SET %s WHERE id = ?', $universe, implode(', ', $fields));

        $db = MySqlDB::instance();

        debug(__METHOD__, $query, $binds);
        $db->execute($query, $binds);

        return true;
    }

    public static function update_map_fg(string $universe, string $image, int $id): bool
    {
        $db = MySqlDB::instance();

        $query = sprintf('UPDATE %s_Maps SET `fg` = ? , `fg_updated` = UTC_TIMESTAMP() WHERE id = ?', $universe);
        $params = ['si', $image, $id];

        debug(__METHOD__, $query, $params);
        $db->execute($query, $params);
    
        return true;
    }

    public static function update_map_bg(string $universe, string $image, ?int $id): bool
    {
        $db = MySqlDB::instance();

        $query = sprintf('UPDATE %s_Maps SET `bg` = ? WHERE id = ?', $universe);
        $params = ['si', $image, $id];

        debug(__METHOD__, $query, $params);
        $db->execute($query, $params);
        
        return true;
    }

}