<?php

/**
 * The User model
 */
class User {
	private $_name  = 'visitor';
	private $_email = null;
	private $_pwd   = null;
	private $_cc    = null;
    private $_db;

    /**
     * Public overridden constructor
     */
    public function __construct() {
        $this->_db = new Mysql();
        // here we can have an exception and we should handle it properly
        $this->_db->connect();
    }
    
	/**
	 * Login the user with login/pwd
	 *
	 * @param string $login the user login
	 * @param string $pwd	the user password MD5 has'd
	 *
	 * @return true|false whether the login has been succesfull or not
	 */
	public function login($login, $pwd) {
        $sql = 'SELECT name, ccnumber FROM user WHERE email="' . $login . '" AND password="' . $pwd . '"';
        $results = $this->_db->query($sql);
        // failed login
        if (!$results) {
            throw new Exception('Wrong email or password', 500);
        }
        // save everything locally in case we want to do something else;
        $this->_email = $login;
        $this->_pwd = $pwd;
        $this->_name = $results['name'];
        $this->_cc = $results['ccnumber'];
        return $results;
	}

    /**
     * Saves the user data into the db.
     *
     * @param string $email the email
     * @param string $pwd   the already hashed password
     * @param string $user  the user name
     * @param int    $cc    the credit card number without dashes
     *
     * @throws Exception in case something wrong has happened
     */
    public function register($email, $pwd, $user, $cc) {
        // verify email is not already present in the db
        if (!$this->verifyUserEmail($email)) {
           throw new Exception('Duplicated email found', 500); 
        }
        $this->_email = $email;
        $this->_pwd   = $pwd;
        $this->_name  = $user;
        $this->_cc    = $cc;
        // we want to save the data into the db
        $sql = 'INSERT INTO user VALUES (';
        $sql .= '"' . $email . '", ';
        $sql .= '"' . $user . '", ';
        $sql .= '"' . $pwd . '", ';
        $sql .= $cc . ')';
        // insert the values and espect everything's fine
        if (!$this->_db->query($sql)) {
            throw new Exception('Unable to register user', 500);
        }
    }

    /**
     * Checks if the email sent is already in place to avoid errors on INSERT
     *
     * @param string $email the email to be verified
     * 
     * @return bool whether the user is already present in the db or not
     */
    public function verifyUserEmail($email) {
        $sql = 'SELECT COUNT(email) FROM user WHERE email="' . $email . '"';
        return ($this->_db->queryScalar($sql) == 1) ? false : true;
    }

    /**
     * getter for $this->_name
     *
     * @return string
     */
	public function getName() {
		return $this->_name;
	}

    /**
     * getter for $this->_email
     *
     * @return string
     */
	public function getEmail() {
		return $this->_email;
	}

    /**
     * getter for $this->_pwd
     *
     * @return string
     */
	public function getPassword() {
		return $this->_pwd;
	}

    /**
     * getter for $this->_pwd
     *
     * @return string
     */
	public function getCrediCard() {
		return $this->_pwd;
	}
}

?>
