<?php
declare(strict_types=1);

namespace Pardusmapper\Core;

use \Pardusmapper\Core\Settings;

class MySqlDB
{
    public \mysqli $db;  // Publicly accessible mysqli object
    private bool $isClosed = true;
    public ?\mysqli_result $queryID = null;
    public $a_record;
    public $o_record;

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

    public function __construct()
    {
        $this->connect();  // Open connection when the class is instantiated
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
        if (empty($this->db)) {
            $this->connect();  // Ensure connection is established
        }
        return mysqli_real_escape_string($this->db, $string);  // Escape the string using the mysqli connection
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

    public function __destruct()
    {
        $this->close(); // Automatically close the connection when the object is destroyed
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

    public function seek(int $seek): bool
    {
        if (empty($this->db)) {
            $this->connect();
        }
        return @mysqli_data_seek($this->queryID, $seek);
    }

    public function protect(string $string): string
    {
        if (empty($this->db)) {
            $this->connect(); // Make sure to establish the connection
        }
        // Escape the string using the established database connection
        return mysqli_real_escape_string($this->db, $string);
    }

    // Get Sector and Cluster Info
    public function getSector(?int $id, ?string $sector): ?object
    {
        if (empty($this->db)) {
            $this->connect();
        }
        if ($id) {
            $this->query('SELECT * FROM Pardus_Sectors WHERE s_id <= ' . $id . ' ORDER BY s_id DESC LIMIT 1');
        } else {
            $this->query('SELECT * FROM Pardus_Sectors WHERE name = \'' . $sector . '\'');
        }
        return $this->nextObject();
    }

    public function getCluster(?int $id, ?string $code): ?object
    {
        if (empty($this->db)) {
            $this->connect();
        }
        if ($id) {
            $this->query('SELECT * FROM Pardus_Clusters WHERE c_id = ' . $id);
        } else {
            $this->query('SELECT * FROM Pardus_Clusters WHERE code = \'' . $code . '\'');
        }
        return $this->nextObject();
    }

    // Coordinate Calculations
    public function getX($id, $s_id, $rows)
    {
        return floor(($id - $s_id) / $rows);
    }

    public function getY($id, $s_id, $rows, $x)
    {
        return $id - ($s_id + ($x * $rows));
    }

    public function getID($s_id, $rows, $x, $y)
    {
        return $s_id + ($rows * $x) + $y;
    }

    // NPC Management
    public function addNPC($uni, $image, $id, $sector, $x, $y, $nid = null)
    {
        if (empty($this->db)) {
            $this->connect();
        }

        if ($id) {
            $s = $this->getSector($id, "");  // Here, $id is passed as expected
            $c = $this->getCluster($s->c_id, "");  // Assuming this is correct
            $x = $this->getX($id, $s->s_id, $s->rows);
            $y = $this->getY($id, $s->s_id, $s->rows, $x);
        } else {
            // Pass null as the first argument when you are using the sector name
            $s = $this->getSector(null, $sector);  // $id is null here, so we fetch by sector
            $c = $this->getCluster($s->c_id, "");  // Assuming this is correct
            $id = $this->getID($s->s_id, $s->rows, $x, $y);
        }

        $this->query('SELECT * FROM Pardus_Npcs WHERE image = \'' . $image . '\'');
        $npc = $this->nextObject();

        $this->query('SELECT * FROM ' . $uni . '_Test_Npcs WHERE (deleted is null or deleted = 0) and id = ' . $id);
        if (!$n = $this->nextObject()) {
            $this->query('INSERT INTO ' . $uni . '_Test_Npcs (`id`) VALUES (' . $id . ')');
            if ($nid) {
                $this->query('UPDATE ' . $uni . '_Test_Npcs SET `nid` = \'' . $nid . '\' WHERE (deleted is null or deleted = 0) and id = ' . $id);
            }

            // Combine multiple updates into one query
            $this->query('UPDATE ' . $uni . '_Test_Npcs SET `cluster` = \'' . $c->name . '\', `sector` = \'' . $s->name . '\', `cloaked` = null, `x` = ' . $x . ', `y` = ' . $y . ', `name` = \'' . $npc->name . '\', `image` = \'' . $image . '\', `hull` = ' . $npc->hull . ', `armor` = ' . $npc->armor . ', `shield` = ' . $npc->shield . ', `spotted` = UTC_TIMESTAMP() WHERE (deleted is null or deleted = 0) and id = ' . $id);

            $this->query('UPDATE ' . $uni . '_Maps SET npc = \'' . $image . '\' , `npc_cloaked` = null, `npc_spotted` = UTC_TIMESTAMP() WHERE id = ' . $id);
        } else {
            if ($n->image != $image) {
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

    public function removeNPC($uni, $id = null)
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

    public function updateNPCHealth($uni, $id, $hull, $armor, $shield, $nid = null)
    {
        if (empty($this->db)) {
            $this->connect();
        }
        if ($id) {
            $this->query('UPDATE ' . $uni . '_Test_Npcs SET `hull` = ' . $hull . ', `armor` = ' . $armor . ', `shield` = ' . $shield . ', `updated` = UTC_TIMESTAMP() WHERE id = ' . $id);
            if ($nid) {
                $this->query('UPDATE ' . $uni . '_Test_Npcs SET `nid` = ' . $nid . ' WHERE id = ' . $id);
            }
            $this->query('UPDATE ' . $uni . '_Maps SET `npc_updated` = UTC_TIMESTAMP() WHERE id = ' . $id);
        }
    }
    public function addBuilding($uni, $image, $id, $sb)
    {
        if (empty($this->db)) {
            $this->connect2();
        }
        if ($id) {
            $this->query('SELECT * FROM ' . $uni . '_Buildings WHERE id = ' . $id);
            if (!$b = $this->nextObject()) {
                $this->query('INSERT INTO ' . $uni . '_Buildings (`id`,`security`) VALUES (' . $id . ', 0)');
            } else {
                $this->removeBuildingStock($uni, $id);
            }
            if ($sb) {
                $this->query('SELECT * FROM ' . $uni . '_Buildings WHERE id = ' . $id);
                $b = $this->nextObject();
                $x = $this->getX($id, $b->starbase, 13);
                $y = $this->getY($id, $b->starbase, 13, $x);
                $this->query('UPDATE ' . $uni . '_Buildings SET `cluster` = \'' . $b->cluster . '\' WHERE id = ' . $id);
                $this->query('UPDATE ' . $uni . '_Buildings SET `sector` = \'' . $b->sector . '\' WHERE id = ' . $id);
            } else {
                // Get Sector Info
                $s = $this->getSector($id, "");
                // Get Cluster Info
                $c = $this->getCluster($s->c_id, "");
                // Calculate X and Y of NPC
                $x = $this->getX($id, $s->s_id, $s->rows);
                $y = $this->getY($id, $s->s_id, $s->rows, $x);
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
        }
    }
    public function addBuildingStock($uni, $image, $id)
    {
        if (empty($this->db)) {
            $this->connect2();
        }
        if ($id) {
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
        }
    }
    public function removeBuildingStock($uni, $id)
    {
        if (empty($this->db)) {
            $this->connect2();
        }
        if ($id) {
            $this->query('DELETE FROM ' . $uni . '_New_Stock WHERE id = ' . $id);
        }
    }
    public function removeBuilding($uni, $id, $sb)
    {
        if (empty($this->db)) {
            $this->connect2();
        }
        if ($id) {
            $this->query('UPDATE ' . $uni . '_Maps SET `fg` = NULL , `fg_spotted` = UTC_TIMESTAMP(), `fg_updated` = UTC_TIMESTAMP() WHERE id = ' . $id);
            $this->query('DELETE FROM ' . $uni . '_Buildings WHERE id = ' . $id);
            $this->query('DELETE FROM ' . $uni . '_New_Stock WHERE id = ' . $id);
        }
        if ($sb) {
            $this->query('DELETE FROM ' . $uni . '_Test_Missions WHERE source_id = ' . $id);
            $this->query('DELETE FROM ' . $uni . '_Squadrons WHERE id = ' . $id);
            //$this->query('DELETE FROM ' . $uni . '_Equipment WHERE id = ' . $id);
        }
    }
    public function updateMapFG($uni, $image, $id)
    {
        if (empty($this->db)) {
            $this->connect2();
        }
        if ($id) {
            $this->query('UPDATE ' . $uni . '_Maps SET `fg` = \'' . $image . '\' , `fg_updated` = UTC_TIMESTAMP() WHERE id = ' . $id);
        }
    }
    public function updateMapBG($uni, $image, $id)
    {
        if (empty($this->db)) {
            $this->connect2();
        }
        if ($id) {
            $this->query('UPDATE ' . $uni . '_Maps SET `bg` = \'' . $image . '\' WHERE id = ' . $id);
        }
    }
    public function updateMapNPC($uni, $image, $id, $cloaked, $nid = null)
    {
        if (empty($this->db)) {
            $this->connect2();
        }

        if ($id) {
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
        }
    }

    public function removeMission($uni, $id)
    {
        if (empty($this->db)) {
            $this->connect2();
        }
        if ($id) {
            $this->query('DELETE FROM ' . $uni . '_Test_Missions WHERE id = ' . $id);
        } else {
            $this->query('DELETE FROM ' . $uni . '_Test_Missions WHERE source_id = ' . $id);
        }
    }
    public function removeWH($uni, $id)
    {
        if (empty($this->db)) {
            $this->connect2();
        }
        if ($id) {
            $this->query('UPDATE ' . $uni . '_Maps SET `fg` = NULL , `wormhole` = NULL WHERE id = ' . $id);
        }
    }
    public function pardusWHStatus($uni)
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
}
