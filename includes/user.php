<?php

/**
 * The User model
 */
class User {
	private $_name  = 'visitor';
	private $_email = null;
	private $_pwd   = null;
	private $_cc    = null;

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
        $this->_email = $email;
        $this->_pwd   = $pwd;
        $this->_name  = $user;
        $this->_cc    = $cc;
        // we want to save the data into the db
    }

    public function verifyUserEmail($email) {
        // contact the db and check SELECT COUNT(email) FROM db == 1
        return false;
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
