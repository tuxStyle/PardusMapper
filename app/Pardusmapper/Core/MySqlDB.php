<?php
declare(strict_types=1);

namespace Pardusmapper\Core;

use Pardusmapper\CORS;
use \Pardusmapper\Core\Instance;
use \Pardusmapper\Core\Settings;

class MySqlDB
{
    public \mysqli $db;  // Publicly accessible mysqli object
    public $source = null;
    private bool $isClosed = true;
    public ?\mysqli_result $queryID = null;
    public $a_record;
    public $o_record;

    use Instance {
        getInstance as private _getInstance;
    }

    public static function instance(array $args = []): MySqlDB
    {
        return self::_getInstance($args);
    }

    public function __construct(array $args = [])
    {
        $this->source = $args['source'] ?? null;
        // debug(__METHOD__, $this->source);

        // Open connection when the class is instantiated
        if (CORS::PARDUS === $this->source) {
            $this->connect2();  
        } else {
            $this->connect();
        }
    }

    public function __destruct()
    {
        $this->close(); // Automatically close the connection when the object is destroyed
    }

    public function connect(): \mysqli
    {
        // debug(__METHOD__, debug_backtrace());
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
        // debug(__METHOD__, debug_backtrace());
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
        
        if (Settings::$DEBUG_SQL) {
            debug(self::extractTrace(backtrace: debug_backtrace()));
        }

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

    private function extractTrace(array $backtrace) {
        $debugTrace = [];

        $prefix = '';
        foreach($backtrace as $trace) {
            $debugTrace[] = sprintf('%s::%s %s(%s)', $trace['class'], $trace['function'], $trace['file'], $trace['line']);
        }
        $debugTrace[] = $backtrace[0]['args'];

        return $debugTrace;
    }
}
