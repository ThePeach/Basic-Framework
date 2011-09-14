<?php

/**
 * The model class provides a little bit of abstraction over the database.
 * It's possible to add further abstraction to provide a more flexible and
 * indipendent access to the sql backend, by doing so we can support whichever
 * database we want.
 *
 * At the moment it's using mysql database backend via it's abstraction class.
 */
Class Model
{
    /**
     * @var resource representing the db connection
     */
    protected $_db;

    /**
     * internal connect function, sets up the connection to the db
     */
    private function _connect() {
        $this->_db = new Mysql();
        // here we can have an exception and we should handle it properly
        $this->_db->connect();
    }

    /**
     *
     * @param string $sql the query
     *
     * @return false|mixed
     */
    protected function queryScalar($sql) {
        $this->_connect();
        return $this->_db->queryScalar($sql);
    }

    /**
     *
     * @param string $sql the query to be executed
     * 
     * @return bool|mixed the result of the query
     */
    protected function query($sql) {
        $this->_connect();
        return $this->_db->query($sql);
    }

}

?>
