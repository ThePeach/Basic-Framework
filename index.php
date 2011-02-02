<?php
// This is the bootstrap file, it just includes the needed files
// and launches the main controller.
session_start();
//error_reporting("NONE");
include_once 'includes/controller.php';
include_once 'includes/view.php';
include_once 'includes/user.php';
// generic initialization
$controller = new Controller();
$controller->init();

?>
