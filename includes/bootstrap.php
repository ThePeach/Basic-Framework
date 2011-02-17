<?php

// include all files in current directory
$resource = opendir('includes');
while(false !== ($file = readdir($resource))) {
    if ($file !== '.' && $file !== '..') {
        include_once($file);
    }
}
closedir($resource);
unset($resource);

?>
