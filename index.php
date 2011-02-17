<?php
// This is the bootstrap file, it just includes the needed files
// and launches the main controller.
//session_start();
//error_reporting("NONE");
include_once 'includes/bootstrap.php';
// generic initialization
$controller = new Controller();
$controller->init();

?>
