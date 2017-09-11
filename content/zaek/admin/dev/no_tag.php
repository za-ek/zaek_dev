<?php
$this->template()->end();

$aFiles = parse_ini_file('assignment.ini.php', true);

function recursive_file_list ( $path, &$files ) {
    $absolute_dir_path = preg_replace('#[/]{2,}#','/',$path);
    if ( substr($absolute_dir_path, -1) == '/') $absolute_dir_path = substr($absolute_dir_path, 0, -1);
    $relative_dir_path = substr($absolute_dir_path, strlen($_SERVER["DOCUMENT_ROOT"]));

    $aIgnore = $this->conf()->get('repo', 'ignore');

    foreach ( $aIgnore as $ignore ) {
        if (substr($relative_dir_path, 0, strlen($ignore)) == $ignore) {
            return;
        }
    }

    foreach ( array_diff(scandir($absolute_dir_path), array('..', '.')) as $file_name ) {
        if ( is_dir($absolute_dir_path . '/' . $file_name) ) {
            recursive_file_list($absolute_dir_path . '/' . $file_name, $files);
        } else {
            $files[] = $relative_dir_path . '/' . $file_name;
        }
    }
}

$aNotAssigned = array();
$aFileCur = array();
recursive_file_list($_SERVER["DOCUMENT_ROOT"], $aFileCur);
foreach ( $aFileCur as $file ) {
    if ( !isset($aFiles['files'][$file]) ) {
        foreach ( $aFiles['ignore']['dir'] as $ignored ) {
            if ( strpos($file, $ignored) === 0 ) {
                continue 2;
            }
        }
        foreach ( $aFiles['ignore']['file'] as $ignored ) {
            if ( $ignored == $file ) {
                continue 2;
            }
        }
        $aNotAssigned[] = $file;
    }
}

die(json_encode($aNotAssigned));