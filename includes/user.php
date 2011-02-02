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
	 * Authenticate against the db backend
	 */
	private function _authenticate() {
        return;
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
	 * Logs out the user unsetting all variables, disabling all sessions (if any)
	 */
	public function logout() {

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

    public function verifyUserEmail($email) {
        $sql = 'SELECT COUNT(email) FROM user';
        return (bool)$this->_db->queryScalar($sql);
    }

	public function getName() {
		return $this->_name;
	}

	public function getEmail() {
		return $this->_email;
	}

	public function getPassword() {
		return $this->_pwd;
	}

	public function getCrediCard() {
		return $this->_pwd;
	}
}

?>
