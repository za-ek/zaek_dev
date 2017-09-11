<?php
//
// jQuery File Tree PHP Connector
//
// Version 1.01
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 24 March 2008
//
// History:
//
// 1.01 - updated to work with foreign characters in directory/file names (12 April 2008)
// 1.00 - released (24 March 2008)
//
// Output a list of files for jQuery File Tree
//

$tag = $_REQUEST['tag'];

$aFiles = parse_ini_file('assignment.ini.php', true);
$aFiles = $aFiles['files'];

$this->template()->end();

$root = $_SERVER['DOCUMENT_ROOT'];
$_POST['dir'] = urldecode($_POST['dir']);

if( file_exists($root . $_POST['dir']) ) {
    $files = array_diff(scandir($root . $_POST['dir']), array('..', '.'));
    natcasesort($files);
    if( count($files) ) {
        echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
        // All dirs
        foreach( $files as $file ) {
            if( file_exists($root . $_POST['dir'] . $file) && is_dir($root . $_POST['dir'] . $file) ) {
                echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "/\">" . htmlentities($file) . "</a></li>";
            }
        }
        // All files
        foreach( $files as $file ) {
            if( file_exists($root . $_POST['dir'] . $file) && !is_dir($root . $_POST['dir'] . $file) &&
                (( isset($aFiles[$_POST['dir'] . $file]) && ($aFiles[$_POST['dir'] . $file] == $tag) ) || ($tag == 'empty' && !isset($aFiles[$_POST['dir'] . $file])))
            ) {
                $ext = preg_replace('/^.*\./', '', $file);
                echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "\">" . htmlentities($file) . "</a></li>";
            }
        }
        echo "</ul>";
    }
}

die();