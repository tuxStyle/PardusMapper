<?php
declare(strict_types=1);

namespace Pardusmapper\Core;

use Pardusmapper\Coordinates;
use \Pardusmapper\Core\Instance;
use \Pardusmapper\Core\Settings;
use Pardusmapper\DB;

class MySqlDB
{
    public \mysqli $db;  // Publicly accessible mysqli object
    private bool $isClosed = true;
    public ?\mysqli_result $queryID = null;
    public $a_record;
    public $o_record;

    use Instance {
        getInstance as private _getInstance;
    }

    public static function instance(): MySqlDB
    {
        return self::_getInstance();
    }

    public function __construct()
    {
        $this->connect();  // Open connection when the class is instantiated
    }

    public function __destruct()
    {
        $this->close(); // Automatically close the connection when the object is destroyed
    }

    public function connect(): \mysqli
    {
        $dbRandy = Settings::$DB_USER;
        $dbRandy .= random_int(1, Settings::$DB_TOTAL_USERS); // Randomly append 1 or 2 to the DB_USER (or as many as you create), this was done when there was a limit to the number of queries the single user could perform

        try {
            // Connect to the database using mysqli_connect
            $this->db = mysqli_connect(
                Settings::$DB_SERVER,  // Server
                $dbRandy,             // User (with appended random value)
                Settings::$DB_PWD,     // Password
                Settings::$DB_NAME     // Database name
            );
        } catch (\Exception $e) {
            // preprint($e);
            die("Failed to connect to MySQL: " . $e->getMessage());
        }

        // Check for connection errors
        if (mysqli_connect_errno()) {
            die("Failed to connect to MySQL: " . mysqli_connect_error());
        }
        $this->isClosed = false;
        return $this->db; // Return the connection
    }

    public function connect2(): \mysqli
    { //this is/was used to split the DB connection between inbound and site usage
        $dbRandy = Settings::$DB_USER;
        $dbRandy .= random_int(1, Settings::$DB_TOTAL_USERS); // Randomly append 1 or 2 to the DB_USER (or as many as you create), this was done when there was a limit to the number of queries the single user could perform

        try {
            // Connect to the database using mysqli_connect
            $this->db = mysqli_connect(
                Settings::$DB_SERVER,  // Server
                $dbRandy,             // User (with appended random value)
                Settings::$DB_PWD,     // Password
                Settings::$DB_NAME     // Database name
            );
        } catch (\Exception $e) {
            // preprint($e);
            die("Failed to connect to MySQL: " . $e->getMessage());
        }

        // Check for connection errors
        if (mysqli_connect_errno()) {
            die("Failed to connect to MySQL: " . mysqli_connect_error());
        }
        $this->isClosed = false;
        return $this->db; // Return the connection
    }

    public function getDb(): \mysqli
    {
        // Ensure the database is connected
        if (!$this->db) {
            $this->connect();
        }
        return $this->db;
    }


    public function prepare(string $sql): \mysqli_stmt
    {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new \Exception("Failed to prepare statement: " . $this->db->error);
        }
        return $stmt;
    }

    public function query(string $sql): \mysqli_result|bool
    {
        if (!$this->db) {
            $this->connect();
        }

        // Execute the query
        $result = $this->db->query($sql);

        // Handle query errors
        if ($result === false) {
            error_log($this->db->errno . " : " . $this->db->error);
            throw new \Exception("Database query error: " . $this->db->error);
        }

        // Assign only if the result is a mysqli_result, otherwise leave it null
        if ($result instanceof \mysqli_result) {
            $this->queryID = $result;
        } else {
            $this->queryID = null;
        }

        return $result;
    }

    public function real_escape_string(string $string): string
    {
        // if (empty($this->db)) {
        //     $this->connect();  // Ensure connection is established
        // }
        // return mysqli_real_escape_string($this->db, $string);  // Escape the string using the mysqli connection

        return $this->protect($string);
    }

    public function protect(string $string): string
    {
        if (empty($this->db)) {
            $this->connect(); // Make sure to establish the connection
        }
        // Escape the string using the established database connection
        return mysqli_real_escape_string($this->db, $string);
    }


    public function fetchObject(): ?object
    {
        if ($this->queryID) {
            return mysqli_fetch_object($this->queryID);
        }
        return null;
    }

    public function close(): void
    {
        // debug_print_backtrace();
        if (!$this->isClosed && $this->db) {
            $this->db->close(); // Close the database connection
            $this->isClosed = true;
        }
    }

    public function nextArray(): ?array
    {
        if (empty($this->db)) {
            $this->connect();
        }
        return @mysqli_fetch_assoc($this->queryID);
    }

    public function nextObject(): ?object
    {
        if (empty($this->db)) {
            $this->connect();
        }
        return @mysqli_fetch_object($this->queryID);
    }

    public function nextRow(): ?array
    {
        if (empty($this->db)) {
            $this->connect();
        }
        return @mysqli_fetch_row($this->queryID);
    }

    public function numRows(): ?int
    {
        if (empty($this->db)) {
            $this->connect();
        }
        return @mysqli_num_rows($this->queryID);
    }

    public function affectedRow(): ?int
    {
        if (empty($this->db)) {
            $this->connect();
        }
        return $this->db->affected_rows;
    }

    public function seek(int $seek): bool
    {
        if (empty($this->db)) {
            $this->connect();
        }
        return @mysqli_data_seek($this->queryID, $seek);
    }

    public function free(): bool
    {
        if (empty($this->db)) {
            $this->connect();
        }

        if ($this->queryID instanceof \mysqli_result) {
            $this->queryID->free();
            $this->queryID = null;
            return true;
        } 

        return false;
    }

    // // Get Sector and Cluster Info
    // public function getSector(?int $id, ?string $sector): ?object
    // {
    //     if (empty($this->db)) {
    //         $this->connect();
    //     }
    //     if ($id) {
    //         $this->query('SELECT * FROM Pardus_Sectors WHERE s_id <= ' . $id . ' ORDER BY s_id DESC LIMIT 1');
    //     } else {
    //         $this->query('SELECT * FROM Pardus_Sectors WHERE name = \'' . $sector . '\'');
    //     }
    //     return $this->nextObject();
    // }

    // public function getCluster(?int $id, ?string $code): ?object
    // {
    //     if (empty($this->db)) {
    //         $this->connect();
    //     }
    //     if ($id) {
    //         $this->query('SELECT * FROM Pardus_Clusters WHERE c_id = ' . $id);
    //     } else {
    //         $this->query('SELECT * FROM Pardus_Clusters WHERE code = \'' . $code . '\'');
    //     }
    //     return $this->nextObject();
    // }

    // // Coordinate Calculations
    // public function getX($id, $s_id, $rows)
    // {
    //     return floor(($id - $s_id) / $rows);
    // }

    // public function getY($id, $s_id, $rows, $x)
    // {
    //     return $id - ($s_id + ($x * $rows));
    // }

    // public function getID($s_id, $rows, $x, $y)
    // {
    //     return $s_id + ($rows * $x) + $y;
    // }

    // NPC Management
    public function addNPC(string $uni, string $image, ?int $id, ?string $sector, int $x, int $y, ?int $nid = null)
    {
        if (Settings::$DEBUG) xp(__METHOD__, __LINE__, func_get_args());
        if (empty($this->db)) {
            $this->connect();
        }

        if ($id) {
            $s = DB::sector(id: $id);  // Here, $id is passed as expected
            $c = DB::cluster(id: $s->c_id);  // Assuming this is correct
            $x = Coordinates::getX($id, $s->s_id, $s->rows);
            $y = Coordinates::getY($id, $s->s_id, $s->rows, $x);
        } else {
            // Pass null as the first argument when you are using the sector name
            $s = DB::sector(sector: $sector);  // $id is null here, so we fetch by sector
            $c = DB::cluster(id: $s->c_id);  // Assuming this is correct
            $id = Coordinates::getID($s->s_id, $s->rows, $x, $y);
        }

        $this->query('SELECT * FROM Pardus_Npcs WHERE image = \'' . $image . '\'');
        $npc = $this->nextObject();
        if (Settings::$DEBUG) xp(__METHOD__, __LINE__, $npc);

        $this->query('SELECT * FROM ' . $uni . '_Test_Npcs WHERE (deleted is null or deleted = 0) and id = ' . $id);
        $n = $this->nextObject();
        if (Settings::$DEBUG) xp(__METHOD__, __LINE__, $n);

        if (!$n) {
            $this->query('INSERT INTO ' . $uni . '_Test_Npcs (`id`) VALUES (' . $id . ')');
            if ($nid) {
                $this->query('UPDATE ' . $uni . '_Test_Npcs SET `nid` = \'' . $nid . '\' WHERE (deleted is null or deleted = 0) and id = ' . $id);
            }

            // Combine multiple updates into one query
            $this->query('UPDATE ' . $uni . '_Test_Npcs SET `cluster` = \'' . $c->name . '\', `sector` = \'' . $s->name . '\', `cloaked` = null, `x` = ' . $x . ', `y` = ' . $y . ', `name` = \'' . $npc->name . '\', `image` = \'' . $image . '\', `hull` = ' . $npc->hull . ', `armor` = ' . $npc->armor . ', `shield` = ' . $npc->shield . ', `spotted` = UTC_TIMESTAMP() WHERE (deleted is null or deleted = 0) and id = ' . $id);

            $this->query('UPDATE ' . $uni . '_Maps SET npc = \'' . $image . '\' , `npc_cloaked` = null, `npc_spotted` = UTC_TIMESTAMP() WHERE id = ' . $id);
        } else {
            if (Settings::$DEBUG) xp(__METHOD__, __LINE__, $n->image, $image);

            if ($n->image != $image) {
                if (Settings::$DEBUG) xp(__METHOD__, __LINE__, 'remove and add');

                $this->removeNPC($uni, $id);
                $this->addNPC($uni, $image, $id, $sector, $x, $y, $nid); // Adding $nid
                return;
            }
        }
        $this->query('UPDATE ' . $uni . '_Test_Npcs SET `updated` = UTC_TIMESTAMP() WHERE (deleted is null or deleted = 0) and id = ' . $id);
        if ($nid) {
            $this->query('UPDATE ' . $uni . '_Test_Npcs SET `nid` = ' . $nid . ' WHERE (deleted is null or deleted = 0) and id = ' . $id);
        }
        $this->query('UPDATE ' . $uni . '_Maps SET npc_updated = UTC_TIMESTAMP() WHERE id = ' . $id);
    }

    public function removeNPC(string $uni, ?int $id = null)
    {
        if (empty($this->db)) {
            $this->connect();
        }
        if ($id) {
            // Combine the updates into one query
            $this->query('UPDATE ' . $uni . '_Maps SET `npc` = null , `npc_cloaked` = null, `npc_updated` = UTC_TIMESTAMP() WHERE id = ' . $id);
            $this->query('UPDATE ' . $uni . '_Test_Npcs SET `deleted` = 1, `cloaked` = null, `updated` = UTC_TIMESTAMP() WHERE id = ' . $id);
        }
    }

    public function updateNPCHealth(string $uni, ?int $id, int $hull, int $armor, int $shield, ?int $nid = null): bool
    {
        if (is_null($id)) {
            return false;
        }

        if (empty($this->db)) {
            $this->connect();
        }

        $this->query('UPDATE ' . $uni . '_Test_Npcs SET `hull` = ' . $hull . ', `armor` = ' . $armor . ', `shield` = ' . $shield . ', `updated` = UTC_TIMESTAMP() WHERE id = ' . $id);
        
        if ($nid) {
            $this->query('UPDATE ' . $uni . '_Test_Npcs SET `nid` = ' . $nid . ' WHERE id = ' . $id);
        }
        
        $this->query('UPDATE ' . $uni . '_Maps SET `npc_updated` = UTC_TIMESTAMP() WHERE id = ' . $id);

        return true;
    }

    public function addBuilding(string $uni, string $image, ?int $id, int $sb): bool
    {
        if (is_null($id)) {
            return false;
        }

        if (empty($this->db)) {
            $this->connect2();
        }

        $this->query('SELECT * FROM ' . $uni . '_Buildings WHERE id = ' . $id);
        if (!$b = $this->nextObject()) {
            $this->query('INSERT INTO ' . $uni . '_Buildings (`id`,`security`) VALUES (' . $id . ', 0)');
        } else {
            $this->removeBuildingStock($uni, $id, $sb);
        }
        if ($sb) {
            $this->query('SELECT * FROM ' . $uni . '_Buildings WHERE id = ' . $id);
            $b = $this->nextObject();
            $x = Coordinates::getX($id, $b->starbase, 13);
            $y = Coordinates::getY($id, $b->starbase, 13, $x);
            $this->query('UPDATE ' . $uni . '_Buildings SET `cluster` = \'' . $b->cluster . '\' WHERE id = ' . $id);
            $this->query('UPDATE ' . $uni . '_Buildings SET `sector` = \'' . $b->sector . '\' WHERE id = ' . $id);
        } else {
            // Get Sector Info
            $s = DB::sector(id: $id);  // Here, $id is passed as expected
            // Get Cluster Info
            $c = DB::cluster(id: $s->c_id);  // Assuming this is correct
            // Calculate X and Y of NPC
            $x = Coordinates::getX($id, $s->s_id, $s->rows);
            $y = Coordinates::getY($id, $s->s_id, $s->rows, $x);
            $this->query('UPDATE ' . $uni . '_Buildings SET `cluster` = \'' . $c->name . '\' WHERE id = ' . $id);
            $this->query('UPDATE ' . $uni . '_Buildings SET `sector` = \'' . $s->name . '\' WHERE id = ' . $id);
            $this->addBuildingStock($uni, $image, $id);
        }
        $this->query('UPDATE ' . $uni . '_Buildings SET `x` = ' . $x . ' WHERE id = ' . $id);
        $this->query('UPDATE ' . $uni . '_Buildings SET `y` = ' . $y . ' WHERE id = ' . $id);
        $this->query('UPDATE ' . $uni . '_Buildings SET `image` = \'' . $image . '\' WHERE id = ' . $id);
        $this->query('UPDATE ' . $uni . '_Buildings SET `spotted` = UTC_TIMESTAMP() WHERE id = ' . $id);
        $this->query('UPDATE ' . $uni . '_Buildings SET `updated` = UTC_TIMESTAMP() WHERE id = ' . $id);


        $this->query('UPDATE ' . $uni . '_Maps SET `fg` = \'' . $image . '\', `fg_spotted` = UTC_TIMESTAMP(), `fg_updated` = UTC_TIMESTAMP() WHERE id = ' . $id);

        return true;
    }

    public function addBuildingStock(string $uni, string $image, ?int $id): bool
    {
        if (is_null($id)) {
            return false;
        }

        if (empty($this->db)) {
            $this->connect2();
        }

        $this->query('SELECT res,upkeep FROM Pardus_Buildings_Data b, Pardus_Upkeep_Data u WHERE b.name = u.name AND b.image = \'' . $image . '\'');
        while ($r = $this->nextObject()) {
            $res[] = $r;
        }
        if ($res) {
            foreach ($res as $r) {
                $this->query('INSERT INTO ' . $uni . '_New_Stock (id,name) VALUES (' . $id . ',\'' . $r->res . '\')');
            }
        }
        $this->query('UPDATE ' . $uni . '_Buildings SET stock_updated = UTC_TIMESTAMP() WHERE id = ' . $id);
        
        return false;
    }

    public function removeBuildingStock(string $uni, ?int $id): bool
    {
        if (is_null($id)) {
            return false;
        }

        if (empty($this->db)) {
            $this->connect2();
        }

        $this->query('DELETE FROM ' . $uni . '_New_Stock WHERE id = ' . $id);

        return false;
    }

    public function removeBuilding(string $uni, ?int $id, int $sb)
    {
        if (is_null($id)) {
            return false;
        }

        if (empty($this->db)) {
            $this->connect2();
        }

        $this->query('UPDATE ' . $uni . '_Maps SET `fg` = NULL , `fg_spotted` = UTC_TIMESTAMP(), `fg_updated` = UTC_TIMESTAMP() WHERE id = ' . $id);
        $this->query('DELETE FROM ' . $uni . '_Buildings WHERE id = ' . $id);
        $this->query('DELETE FROM ' . $uni . '_New_Stock WHERE id = ' . $id);

        if ($sb) {
            $this->query('DELETE FROM ' . $uni . '_Test_Missions WHERE source_id = ' . $id);
            $this->query('DELETE FROM ' . $uni . '_Squadrons WHERE id = ' . $id);
            //$this->query('DELETE FROM ' . $uni . '_Equipment WHERE id = ' . $id);
        }

        return true;
    }

    public function updateMapFG(string $uni, string $image, ?int $id): bool
    {
        if (is_null($id)) {
            return false;
        }

        if (empty($this->db)) {
            $this->connect2();
        }

        $this->query('UPDATE ' . $uni . '_Maps SET `fg` = \'' . $image . '\' , `fg_updated` = UTC_TIMESTAMP() WHERE id = ' . $id);
        if (Settings::$DEBUG) xp(__METHOD__, 'UPDATE ' . $uni . '_Maps SET `fg` = \'' . $image . '\' , `fg_updated` = UTC_TIMESTAMP() WHERE id = ' . $id);
        return true;
    }

    public function updateMapBG(string $uni, string $image, ?int $id)
    {
        if (is_null($id)) {
            return false;
        }

        if (empty($this->db)) {
            $this->connect2();
        }

        if (Settings::$DEBUG) xp('UPDATE ' . $uni . '_Maps SET `bg` = \'' . $image . '\' WHERE id = ' . $id);
        $this->query('UPDATE ' . $uni . '_Maps SET `bg` = \'' . $image . '\' WHERE id = ' . $id);
        
        return true;
    }

    public function updateMapNPC(string $uni, string $image, ?int $id, int $cloaked, ?int $nid = null): bool
    {
        if (is_null($id)) {
            return false;
        }

        if (empty($this->db)) {
            $this->connect2();
        }

        if ($cloaked) {
            $stmt = $this->db->prepare("UPDATE {$uni}_Maps SET `npc_cloaked` = 1, `npc_updated` = UTC_TIMESTAMP() WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();

            $stmt = $this->db->prepare("UPDATE {$uni}_Test_Npcs SET cloaked = 1, updated = UTC_TIMESTAMP() WHERE (deleted IS NULL OR deleted = 0) AND id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();

            if ($nid) {
                $stmt = $this->db->prepare("UPDATE {$uni}_Test_Npcs SET `nid` = ? WHERE (deleted IS NULL OR deleted = 0) AND id = ?");
                $stmt->bind_param('ii', $nid, $id);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            $stmt = $this->db->prepare("UPDATE {$uni}_Maps SET npc = ?, `npc_cloaked` = NULL, `npc_updated` = UTC_TIMESTAMP() WHERE id = ?");
            $stmt->bind_param('si', $image, $id);
            $stmt->execute();
            $stmt->close();

            $stmt = $this->db->prepare("UPDATE {$uni}_Test_Npcs SET cloaked = NULL, updated = UTC_TIMESTAMP() WHERE (deleted IS NULL OR deleted = 0) AND id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();

            if ($nid) {
                $stmt = $this->db->prepare("UPDATE {$uni}_Test_Npcs SET `nid` = ? WHERE (deleted IS NULL OR deleted = 0) AND id = ?");
                $stmt->bind_param('ii', $nid, $id);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        return true;
    }

    public function removeMission(string $uni, int $id): bool
    {
        if (is_null($id)) {
            return false;
        }

        if (empty($this->db)) {
            $this->connect2();
        }
        if ($id) {
            $this->query('DELETE FROM ' . $uni . '_Test_Missions WHERE id = ' . $id);
        } else {
            $this->query('DELETE FROM ' . $uni . '_Test_Missions WHERE source_id = ' . $id);
        }

        return true;
    }
    public function removeWH(string $uni, int $id)
    {
        if (empty($this->db)) {
            $this->connect2();
        }
        if ($id) {
            $this->query('UPDATE ' . $uni . '_Maps SET `fg` = NULL , `wormhole` = NULL WHERE id = ' . $id);
        }
    }
    public function pardusWHStatus(string $uni)
    {
        if (empty($this->db)) {
            $this->connect2();
        }
        if ($uni) {
            $START_TIME = 1449120361000; //December 3, 2015 05:26:01 GMT
            //determine closed WH
            $deltaT = round(microtime(true) * 1000) - $START_TIME;
            $days = $deltaT / 1000 / 60 / 60 / 24; //milliseconds to days
            if ($uni == 'Pegasus') {
                $days = $days + 3;
            } //Pegasus is 3 days out of sync with Orion/Artemis
            $shift =  floor($days / 2) % 4; //closed WH switches every two days, new cycle every four WHs
            //var nextDay = 2 - Math.floor(days) % 2; //days until closed WH changes
            $WHs = [162194, 160536, 139222, 163055];
            $PWHs = [159205, 159794, 151630, 156058];
            $closedWH = $WHs[$shift]; //163055;//
            $closedPWH = $PWHs[$shift]; //156058; //
            //var_dump($shift);
            //var nextWH = WHs[(shift+1) % 4];
            $this->query('UPDATE ' . $uni . '_Maps SET `fg` = \'foregrounds/wormholeseal_open.png\' , `fg_updated` = UTC_TIMESTAMP() WHERE id != ' . $closedWH . ' and id != ' . $closedPWH . ' and id in (162194,160536,139222,163055,159205,159794,151630,156058)');
            $this->query('UPDATE ' . $uni . '_Maps SET `fg` = \'foregrounds/wormholeseal_closed.png\' , `fg_updated` = UTC_TIMESTAMP() WHERE (id = ' . $closedWH . ' or id = ' . $closedPWH . ') and id in (162194,160536,139222,163055,159205,159794,151630,156058)');
        }
    }

    public function addMap(string $uni, string $image, int $id, int $sb) {
        if (empty($this->db)) { $this->connect(); }

        // if (preg_match('/^\d+$/', $image)) {
        //     if (Settings::$DEBUG) xp(__FILE__, $image);
        //     $this->execute('SELECT image FROM background WHERE id = ?', [
        //         'i', $image
        //     ]);
        //     $dbImg = $this->fetchObject();
        //     $image = $dbImg->image;
        // }
        if ($id) {
            $this->query('INSERT INTO ' . $uni . '_Maps (`id`, `bg`, `security`) VALUES (' . $id . ',\'' . $image . '\' , 0)');
            if (Settings::$DEBUG) xp(__METHOD__, __LINE__, 'INSERT INTO ' . $uni . '_Maps (`id`, `bg`, `security`) VALUES (' . $id . ',\'' . $image . '\' , 0)');

            if ($sb) {
                if (Settings::$DEBUG) xp(__METHOD__, __LINE__, 'we have building');
                $this->query('SELECT * FROM ' . $uni . '_Buildings WHERE id = ' . $sb);
                $b = $this->nextObject();
                $this->query('UPDATE ' . $uni . '_Maps SET cluster = \'' . $b->cluster . '\' WHERE id = ' . $id);
                $this->query('UPDATE ' . $uni . '_Maps SET sector = \'' . $b->sector . '\' WHERE id = ' . $id);
                $x = Coordinates::getX($id,$b->starbase,13);
                $y = Coordinates::getY($id,$b->starbase,13,$x);
                $this->query('UPDATE ' . $uni . '_Maps SET x = ' . $x . ' WHERE id = ' . $id);
                $this->query('UPDATE ' . $uni . '_Maps SET y = ' . $y . ' WHERE id = ' . $id);					
            } else {
                if (Settings::$DEBUG) xp(__METHOD__, __LINE__, 'no building');
                $s = DB::sector(id: $id);  // Here, $id is passed as expected
                $c = DB::cluster(id: $s->c_id);  // Assuming this is correct
                $this->query('UPDATE ' . $uni . '_Maps SET cluster = \'' . $c->name . '\' WHERE id = ' . $id);
                $this->query('UPDATE ' . $uni . '_Maps SET sector = \'' . $s->name . '\' WHERE id = ' . $id);
                $x = Coordinates::getX($id,$s->s_id,$s->rows);
                $y = Coordinates::getY($id,$s->s_id,$s->rows,$x);
                $this->query('UPDATE ' . $uni . '_Maps SET x = ' . $x . ' WHERE id = ' . $id);
                $this->query('UPDATE ' . $uni . '_Maps SET y = ' . $y . ' WHERE id = ' . $id);
            }
        }
    }

    /**
     * Execute prepared query and return data
     *
     * @param string $sql
     * @param array|null $params
     * @return object|false|null
     */
    public function execute(string $sql, ?array $params = null): object|false|null
    {
        if (!$this->db) {
            $this->connect();
        }

        $this->free();
        
        // prepare query
        $stmt = $this->prepare($sql);
        
        // bind params 
        if (is_array($params) && count($params) >= 2) {
            $types = array_shift($params); // First element is the types string
            $values = $params; // Remaining elements are the values
            $stmt->bind_param($types, ...$values);    
        }

        // Execute the query
        if (!$stmt->execute()) {
            // Handle query errors
            error_log($this->db->errno . " : " . $this->db->error);
            return false;
        }

        // Get the result if it's a SELECT query
        $result = $stmt->get_result();

        // Close the statement after execution
        $stmt->close();

        // Check if the result is a mysqli_result for SELECT queries
        if ($result instanceof \mysqli_result) {
            $this->queryID = $result;

            // For SELECT queries, return the result object
            return $result; // Return the mysqli_result object
        } else {
            $this->queryID = null;

            // For INSERT, UPDATE, DELETE queries
            return ($this->db->affected_rows > 0); // Return true if rows were affected, false otherwise
        }
    }
}
