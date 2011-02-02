<?php

/**
 * This is the controller
 */
class Controller {
    private $_user = null;
    private $_view = null;
    private $_jsonResponse = array(
        'result' => false,
        'error'  => null,
    );
    public static $_layout = 'page';

    /**
     * initialization function: this function has two basic roles:
     * 1) initializes the basic stuff it needs (user/view)
     * 2) forward to the right action to be taken
     */
    public function init() {
        // load the view
        $this->_view = new View();
        $this->_user = new User();
        // see if the request is a GET or a POST and take further action
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            // this is the place the user ends up when filling a form
            if (isset($_POST['req'])) {
                if ('login' == $_POST['req']) {
                    $this->actionLogin(
                        $_POST['login'],
                        $_POST['pwd']
                    );
                } else if ('register' == $_POST['req']) {
                    $this->actionRegister(
                        $_POST['login'],
                        $_POST['pwd'],
                        $_POST['user'],
                        $_POST['cc']
                    );
                }
            }
        } else if ('GET' === $_SERVER['REQUEST_METHOD']) {
            if (isset($_GET['js'])) {
                // this is the place where ajax calls are intercepted,
                // if not, bad things will happen
                if (isset($_GET['verifyEmail'])) {
                    $this->actionVerifyEmail($_GET['verifyEmail']);
                }
            } else {
                // default action
                $this->actionDefault();
            }
        }

    }

    /**
     * Default action to be invoked when the user is not logged in.
     */
    public function actionDefault() {
        // we just want to show the page
        $this->_view->assign(View::TITLE, 'Test Login Page');
        // dispatch the page
        $this->render('default');
    }

    /**
     * This function verifies the email is not already in the db
     *
     * @param string $email the email to be verified
     */
    public function actionVerifyEmail($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->_jsonResponse['result'] = $this->_user->verifyUserEmail($email);
        }
        echo json_encode($this->_jsonResponse);
        exit();
    }

    /**
     * Perform various steps to identify the user and verify he's in the db
     *
     * @param string $email the user's email
     * @param string $pwd   the has'd password
     */
    public function actionLogin($email, $pwd) {
        try {
            $result = $this->_user->login($email, $pwd);
            $this->_jsonResponse['result'] = $result;
        } catch (Exception $e) {
            $this->_jsonResponse['error'] = $e->getMessage();
        }
        echo json_encode($this->_jsonResponse);
    }

    /**
     *
     * @param <type> $email
     * @param <type> $pwd
     * @param <type> $user
     * @param <type> $cc
     */
    public function actionRegister($email, $pwd, $user, $cc) {
        // we need to sanitise the input before sending it to the db
        if (filter_var($email, FILTER_VALIDATE_EMAIL)
            && filter_var($pwd, FILTER_SANITIZE_STRING)
            && filter_var($user, FILTER_SANITIZE_STRING)
            && filter_var($cc, FILTER_VALIDATE_INT)
        ) {
            try {
                $this->_user->register($email, $pwd, $user, $cc);
            } catch(Exception $e) {
                // we don't really care what has really happened at this point
                // since the user don't really want to be bothered
                $this->_jsonResponse['error'] = $e->getMessage();
            }
            $this->_jsonResponse['result'] = true;
        }
        // once the user is registered in the db
        // we give him greenlight as he was logged in
        echo json_encode($this->_jsonResponse);
    }

    /**
     * This function outputs the view
     */
    private function render($template) {
        $this->_view->assembleTemplate($template, self::$_layout);
        // add relevant headers in case anything's needed
        header('Content-Type', 'text/html, charset=UTF-8');
        // brutally display the page
        echo $this->_view;
    }

}


?>
