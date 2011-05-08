<?php

/**
 * The User model represents the database table for the user
 * The password for logging in/registering is expected to be already hashed
 * (at the moment we are using sha256).
 * We are not doing any salting or scrambling of the password.
 *
 * **EXAMPLE**
 * $user = new User();
 * $successful = $user->login($email, $password);
 * if ($successful) {
 *   // do something
 * } else {
 *   // register user
 *   try {
 *      $user->register($email, $password, $name, $cc);
 *   } catch (Exception $e) {
 *      // behave accordingly
 *   }
 * }
 *
 */
class User extends Model
{
	private $_name  = 'visitor';
	private $_email = null;
	private $_pwd   = null;
	private $_cc    = null;

    /**
     * returns the name of table represented by the model
     *
     * @return string
     */
    public function tableName() {
        return 'user';
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
        $sql = 'SELECT name, ccnumber FROM '.$this->tableName();
        $sql .= ' WHERE email="' . $login . '" AND password="' . $pwd . '"';
        $results = $this->query($sql);
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
    public function register($email, $pwd, $user, $cc=null) {
        // verify email is not already present in the db
        if (!$this->userExists($email)) {
           throw new Exception('User already existing', 500);
        }
        $this->_email = $email;
        $this->_pwd   = $pwd;
        $this->_name  = $user;
        $this->_cc    = $cc;
        // we want to save the data into the db
        $sql = 'INSERT INTO '.$this->tableName().' VALUES (';
        $sql .= '"' . $email . '", ';
        $sql .= '"' . $user . '", ';
        $sql .= '"' . $pwd . '", ';
        $sql .= (($cc) ? $cc : 'NULL') . ')';
        // insert the values and espect everything's fine
        if (!$this->query($sql)) {
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
    public function userExists($email) {
        $sql = 'SELECT COUNT(email) FROM '.$this->tableName();
        $sql .= ' WHERE email="' . $email . '"';
        return ($this->queryScalar($sql) == 1) ? false : true;
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
