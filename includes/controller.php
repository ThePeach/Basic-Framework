<?php

/**
 * This is the controller.
 * Its basic usage is very simple and all the logic for routing the call
 * is living inside of the init() function.
 * Actions instead will contain the real code for expressing what is needed.
 * Actions must end with a call to one of the (private) render functions.
 * 
 * Although we can have an external Request and Response together with a Route
 * object as Zend does, this seems to be an overkill in this simple example.
 *
 * **EXAMPLE**
 * $controller = new Controller();
 * $controller->init();
 */
class Controller
{
    private $_user = null;
    private $_view = null;
    private $_jsonResponse = array(
        'result' => false,
        'error'  => null,
    );
    // we can override the main layout before outputting everything
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
                        $_POST['name']
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
            $this->_jsonResponse['result'] = $this->_user->userExists($email);
        }
        echo $this->renderJson();
    }

    /**
     * Perform various steps to identify the user and verify he's in the db
     *
     * @param string $email the user's email
     * @param string $pwd   the hash'd password
     */
    public function actionLogin($email, $pwd) {
        try {
            $result = $this->_user->login($email, $pwd);
            $this->_jsonResponse['result'] = $result;
        } catch (Exception $e) {
            $this->_jsonResponse['error'] = $e->getMessage();
        }
        echo $this->renderJson();
    }

    /**
     * Register the user within the database performing several additional
     * steps to ensure the data sent is valid
     *
     * @param string $email the user's email
     * @param string $pwd   the has'd password
     * @param string $user  the user's name
     * @param int    $cc    the credit card number as an integer
     */
    public function actionRegister($email, $pwd, $user) {
        $this->_jsonResponse['result'] = false;
        // we need to sanitise the input before sending it to the db
        if (filter_var((string)$email, FILTER_VALIDATE_EMAIL)
            || filter_var($pwd, FILTER_SANITIZE_STRING)
            || filter_var($user, FILTER_SANITIZE_STRING)
        ) {
            try {
                $this->_user->register($email, $pwd, $user, $cc);
                $this->_jsonResponse['result'] = true;
            } catch(Exception $e) {
                // we don't really care what has really happened at this point
                // since the user don't really want to be bothered
                $this->_jsonResponse['error'] = $e->getMessage();
            }
        } else {
            $this->_jsonResponse['error'] = 'Wrong parameters passed';
        }
        // once the user is registered in the db
        // we give him greenlight as he was logged in
        echo $this->renderJson();
    }

    /**
     * This function outputs the view and adds relevant headers to it
     *
     * @param string $template the contente template to be rendered
     */
    private function render($template) {
        $this->_view->assembleTemplate($template, self::$_layout);
        // add relevant headers in case anything's needed
        header('Content-Type', 'text/html, charset=UTF-8');
        // brutally display the page
        echo $this->_view;
    }

    /**
     * This will output the json response with the relevant headers
     *
     * @param array $response the response to be encoded
     */
    private function renderJson($response = null) {
        header('Content-Type', 'application/json, charset=UTF-8');
        if ($response) {
            echo json_encode($response);
        }
        echo json_encode($this->_jsonResponse);
    }

}


?>
