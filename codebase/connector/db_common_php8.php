<?php
/**
 * DHTMLX Connector - PHP 8 Compatible Version
 * 
 * This file contains the updated MySQLDBDataWrapper class for PHP 8 compatibility.
 * All deprecated mysql_* functions have been replaced with mysqli_* equivalents.
 * 
 * @author DHTMLX.com (Updated for PHP 8)
 * @license GPL, see license.txt
 * @version 2.0 (PHP 8 Compatible)
 */

declare(strict_types=1);

/**
 * Implementation of DataWrapper for MySQL using MySQLi
 * 
 * This class provides database operations using the MySQLi extension,
 * which is the recommended approach for PHP 8 compatibility.
 */
class MySQLDBDataWrapper extends DBDataWrapper
{
    protected $last_result;
    
    /**
     * Execute a SQL query
     * 
     * @param string $sql The SQL query to execute
     * @return mixed The query result
     * @throws Exception If the query fails
     */
    public function query(string $sql)
    {
        LogMaster::log($sql);
        
        if (!$this->connection) {
            throw new Exception("Database connection not established");
        }
        
        $result = mysqli_query($this->connection, $sql);
        
        if ($result === false) {
            $error = mysqli_error($this->connection);
            throw new Exception("MySQL operation failed: " . $error);
        }
        
        $this->last_result = $result;
        return $result;
    }
    
    /**
     * Get next row from result set
     * 
     * @param mixed $res The result set (optional, uses last result if not provided)
     * @return array|null The next row as an associative array, or null if no more rows
     */
    public function get_next($res = null)
    {
        if (!$res) {
            $res = $this->last_result;
        }
        
        if (!$res) {
            return null;
        }
        
        return mysqli_fetch_assoc($res);
    }
    
    /**
     * Get the ID of the last inserted row
     * 
     * @return int The last insert ID
     */
    protected function get_new_id(): int
    {
        if (!$this->connection) {
            return 0;
        }
        
        return (int) mysqli_insert_id($this->connection);
    }
    
    /**
     * Escape a string for use in SQL queries
     * 
     * @param string $data The string to escape
     * @return string The escaped string
     * 
     * @deprecated Use prepared statements instead for better security
     */
    public function escape(string $data): string
    {
        if (!$this->connection) {
            return $data;
        }
        
        return mysqli_real_escape_string($this->connection, $data);
    }
    
    /**
     * Get list of tables in the database
     * 
     * @return array Array of table names
     * @throws Exception If the query fails
     */
    public function tables_list(): array
    {
        $result = mysqli_query($this->connection, "SHOW TABLES");
        
        if ($result === false) {
            throw new Exception("MySQL operation failed: " . mysqli_error($this->connection));
        }
        
        $tables = [];
        while ($table = mysqli_fetch_array($result)) {
            $tables[] = $table[0];
        }
        
        return $tables;
    }
    
    /**
     * Get list of fields for a specific table
     * 
     * @param string $table The table name
     * @return array Array with 'fields' and 'key' (primary key)
     * @throws Exception If the query fails
     */
    public function fields_list(string $table): array
    {
        $escaped_table = mysqli_real_escape_string($this->connection, $table);
        $result = mysqli_query($this->connection, "SHOW COLUMNS FROM `" . $escaped_table . "`");
        
        if ($result === false) {
            throw new Exception("MySQL operation failed: " . mysqli_error($this->connection));
        }
        
        $fields = [];
        $id = "";
        
        while ($field = mysqli_fetch_assoc($result)) {
            if ($field['Key'] === "PRI") {
                $id = $field["Field"];
            } else {
                $fields[] = $field["Field"];
            }
        }
        
        return ["fields" => $fields, "key" => $id];
    }
    
    /**
     * Escape field name to prevent SQL reserved words conflict
     * 
     * @param mixed $data The field name to escape
     * @return string The escaped field name
     */
    public function escape_name($data): string
    {
        if ((strpos((string)$data, "`") !== false || is_int($data)) || (strpos((string)$data, ".") !== false)) {
            return (string)$data;
        }
        
        return '`' . $data . '`';
    }
}

/**
 * Implementation of DataWrapper for MySQL using Prepared Statements (Recommended)
 * 
 * This class provides database operations using prepared statements,
 * which is the most secure approach for preventing SQL injection.
 */
class MySQLPreparedDBDataWrapper extends DBDataWrapper
{
    protected $last_result;
    protected $last_stmt;
    
    /**
     * Execute a SQL query with prepared statement support
     * 
     * @param string $sql The SQL query to execute
     * @return mixed The query result
     * @throws Exception If the query fails
     */
    public function query(string $sql)
    {
        LogMaster::log($sql);
        
        if (!$this->connection) {
            throw new Exception("Database connection not established");
        }
        
        $result = mysqli_query($this->connection, $sql);
        
        if ($result === false) {
            $error = mysqli_error($this->connection);
            throw new Exception("MySQL operation failed: " . $error);
        }
        
        $this->last_result = $result;
        return $result;
    }
    
    /**
     * Execute a prepared statement
     * 
     * @param string $sql The SQL query with placeholders (?)
     * @param array $params The parameters to bind
     * @param string $types The types of parameters (e.g., 'ssi' for string, string, int)
     * @return mixed The query result
     * @throws Exception If the query fails
     */
    public function query_prepared(string $sql, array $params = [], string $types = ''): mixed
    {
        LogMaster::log($sql);
        
        if (!$this->connection) {
            throw new Exception("Database connection not established");
        }
        
        $stmt = mysqli_prepare($this->connection, $sql);
        
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . mysqli_error($this->connection));
        }
        
        if (!empty($params) && !empty($types)) {
            if (!mysqli_stmt_bind_param($stmt, $types, ...$params)) {
                throw new Exception("Bind param failed: " . mysqli_stmt_error($stmt));
            }
        }
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Execute failed: " . mysqli_stmt_error($stmt));
        }
        
        $this->last_stmt = $stmt;
        $result = mysqli_stmt_get_result($stmt);
        $this->last_result = $result;
        
        return $result;
    }
    
    /**
     * Get next row from result set
     * 
     * @param mixed $res The result set (optional, uses last result if not provided)
     * @return array|null The next row as an associative array, or null if no more rows
     */
    public function get_next($res = null)
    {
        if (!$res) {
            $res = $this->last_result;
        }
        
        if (!$res) {
            return null;
        }
        
        return mysqli_fetch_assoc($res);
    }
    
    /**
     * Get the ID of the last inserted row
     * 
     * @return int The last insert ID
     */
    protected function get_new_id(): int
    {
        if (!$this->connection) {
            return 0;
        }
        
        return (int) mysqli_insert_id($this->connection);
    }
    
    /**
     * Escape a string for use in SQL queries
     * 
     * @param string $data The string to escape
     * @return string The escaped string
     */
    public function escape(string $data): string
    {
        if (!$this->connection) {
            return $data;
        }
        
        return mysqli_real_escape_string($this->connection, $data);
    }
    
    /**
     * Get list of tables in the database
     * 
     * @return array Array of table names
     * @throws Exception If the query fails
     */
    public function tables_list(): array
    {
        $result = mysqli_query($this->connection, "SHOW TABLES");
        
        if ($result === false) {
            throw new Exception("MySQL operation failed: " . mysqli_error($this->connection));
        }
        
        $tables = [];
        while ($table = mysqli_fetch_array($result)) {
            $tables[] = $table[0];
        }
        
        return $tables;
    }
    
    /**
     * Get list of fields for a specific table
     * 
     * @param string $table The table name
     * @return array Array with 'fields' and 'key' (primary key)
     * @throws Exception If the query fails
     */
    public function fields_list(string $table): array
    {
        $escaped_table = mysqli_real_escape_string($this->connection, $table);
        $result = mysqli_query($this->connection, "SHOW COLUMNS FROM `" . $escaped_table . "`");
        
        if ($result === false) {
            throw new Exception("MySQL operation failed: " . mysqli_error($this->connection));
        }
        
        $fields = [];
        $id = "";
        
        while ($field = mysqli_fetch_assoc($result)) {
            if ($field['Key'] === "PRI") {
                $id = $field["Field"];
            } else {
                $fields[] = $field["Field"];
            }
        }
        
        return ["fields" => $fields, "key" => $id];
    }
    
    /**
     * Escape field name to prevent SQL reserved words conflict
     * 
     * @param mixed $data The field name to escape
     * @return string The escaped field name
     */
    public function escape_name($data): string
    {
        if ((strpos((string)$data, "`") !== false || is_int($data)) || (strpos((string)$data, ".") !== false)) {
            return (string)$data;
        }
        
        return '`' . $data . '`';
    }
    
    /**
     * Close the prepared statement
     */
    public function __destruct()
    {
        if ($this->last_stmt) {
            mysqli_stmt_close($this->last_stmt);
        }
    }
}
?>
