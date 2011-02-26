<?php

// we will be using scandir to get files in alphabetical order
// NOTE: this is suboptimal since it will not understand the file deps
$files = scandir(dirname(__FILE__));
foreach ($files as $file) {
    if ($file !== '.'
        && $file !== '..'
        && $file !== 'bootstrap.php')
    {
        include_once $file;
    }
}
unset($files);

?>
