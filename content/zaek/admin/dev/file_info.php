<?php
$this->template()->end();
$aPath = parse_ini_file('assignment.ini.php');
if ( array_key_exists($_POST['path'], $aPath) ) {
    return $aPath[$_POST['path']];
} else {
    return false;
}