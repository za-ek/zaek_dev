<?php
$this->template()->end();
$aPath = @parse_ini_file('assignment.ini.php', 1);

if ( is_dir( $_SERVER["DOCUMENT_ROOT"] . $_REQUEST['path'] ) ) {
    function recursive_file_select ( $path, &$files ) {

        $absolute_dir_path = preg_replace('#[/]{2,}#','/',$path);
        if ( substr($absolute_dir_path, -1) == '/') $absolute_dir_path = substr($absolute_dir_path, 0, -1);
        $relative_dir_path = substr($absolute_dir_path, strlen($_SERVER["DOCUMENT_ROOT"]));

        foreach ( array_diff(scandir($absolute_dir_path), array('..', '.')) as $file_name ) {
            if ( is_dir($absolute_dir_path . '/' . $file_name) ) {
                recursive_file_select($absolute_dir_path . '/' . $file_name, $files);
            } else {
                $files['files'][$relative_dir_path . '/' . $file_name] = $_REQUEST['module'];
            }
        }
    }

    recursive_file_select($_SERVER['DOCUMENT_ROOT'] . $_REQUEST['path'], $aPath);
} else {
    $aPath['files'][$_REQUEST['path']] = $_REQUEST['module'];
}

file_put_contents(__DIR__ . '/assignment.ini.php', ';<?php die(); ?>' . PHP_EOL . get_ini_string($aPath));

die('');