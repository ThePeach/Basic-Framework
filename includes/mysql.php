<?php

/**
 * This is the Mysql object used to connect and query the db.
 * Just a really simple level of abstraction above the db.
 */
class Mysql {
    private $_db   = 'test';
    private $_user = 'test';
    private $_pwd  = '';
    private $_host = 'localhost';
    private $_connection = null;

    /**
     * Will instantiate the db connection and the selection of the db
     *
     * @throws Exception in case the connection or the selection of the db fails
     */
    public function connect() {
        $this->_connection = mysql_connect(
            $this->_host,
            $this->_user,
            $this->_pwd
        );
        if (!$this->_connection) {
            throw new Exception(mysql_error(), mysql_errno());
        }
        if (!mysql_select_db($this->_db)) {
            throw new Exception(mysql_error(), mysql_errno());
        }
    }

    /**
     * Disconnects the current connection, currently not used since it will
     * be dropped automatically at the end of the script
     */
    public function disconnect() {
        mysql_close($this->_connection);
    }

    /**
     * Returns a single value out of a query from a db
     *
     * @param string $sql the SQL query to perform
     * 
     * @return false|mixed
     */
    public function queryScalar($sql) {
        $result = $this->_realQuery($sql);
        if (!$result) {
            return false;
        }
        $row = mysql_fetch_array($result, MYSQL_NUM);
        mysql_free_result($result);
        return $row[0];
    }

    /**
     * This will perform an SQL query to the db
     *
     * @param string $sql the SQL statement to execute
     *
     * @return mixed the array containing the results of a select or a boolean
     *               to indicate the status of the operation 
     */
    public function query($sql) {
        $result = $this->_realQuery($sql);
        if (!$result) {
            return false;
        }
        // in case the result is coming from a SELECT
        if ($result !== true) {
            $arrayResults = mysql_fetch_assoc($result);
            mysql_free_result($result);
        } else {
            $arrayResults = true;
        }
        return $arrayResults;
    }

    /**
     * This will query the db for real
     *
     * @param string $sql the SQL statement to execute
     * 
     * @return mixed the mysql resource object
     */
    private function _realQuery($sql) {
        return mysql_query($sql, $this->_connection);
    }
}
?>
