<?php
$this->template()->end();

function read_all_files($root = '.'){
    $files  = array();
    $directories  = array();
    $last_letter  = $root[strlen($root)-1];
    $root  = ($last_letter == '\\' || $last_letter == '/') ? $root : $root.DIRECTORY_SEPARATOR;

    $directories[]  = $root;

    while (sizeof($directories)) {
        $dir  = array_pop($directories);
        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if ($file == '.' || $file == '..') {
                    continue;
                }

                if (is_dir($dir.$file) && $file != '.git') {
                    $directory_path = $dir.$file.DIRECTORY_SEPARATOR;
                    array_push($directories, $directory_path);
                } elseif (is_file($dir.$file)) {
                    $files[]  = $dir.$file;
                }
            }
            closedir($handle);
        }
    }

    return $files;
}

$aResult = array();

$directory_path = $this->conf()->get('repo', 'dir');
$config_user_email = $this->conf()->get('repo', 'user_email');
$config_user_name = $this->conf()->get('repo', 'user_name');
$directory_path_len = strlen($directory_path) - 1;

$aFiles = parse_ini_file('assignment.ini.php', true);
$aTags = parse_ini_file('tags.ini.php', true);
$aNeedChange = array_fill_keys(array_keys($aTags['list']), array());

foreach ( $aTags['list'] as $k => $v ) {
    if ( !file_exists($directory_path . $k) ) {
        mkdir($directory_path . $k, 0777, true);
        exec('cd ' . $directory_path . $k . ' && git init --shared=group && git config user.email "'.$config_user_email.'" && git config user.name "'.$config_user_name.'"');
    }

    $aRepositoryFiles = read_all_files ( $directory_path . $k );
    foreach ( $aRepositoryFiles as $file ) {
        $rel = substr($file, $directory_path_len + strlen($k) + 1);
        if ( !isset($aFiles['files'][$rel]) || $aFiles['files'][$rel] != $k) {
            unlink($file);
        }
    }
}

foreach ( $aFiles['files'] as $file => $module ) {
    $last_m = @filemtime($_SERVER["DOCUMENT_ROOT"] . $file);

    if ( $module != 'ignore' ) {
        if ( $last_m > @$aTags['creation_time'][$module][$aTags['current_version'][$module]] ) {
            $aNeedChange[$module][] = $file;
        }
    }
}

foreach ( $aNeedChange as $module => $arr ) {
    exec("cd {$directory_path}{$module} && git init && git config user.email '{$config_user_email}' && git config user.name '{$config_user_name}'");
    foreach ( $arr as $file ) {
        $dst = $directory_path . $module . $file;
        @mkdir(dirname($dst), 0777, true);
        copy ( $_SERVER['DOCUMENT_ROOT'] . $file, $dst);
    }
    exec("cd {$directory_path}{$module} && git add .");
    $aResult[$module] = count($arr);
}

die(json_encode($aResult));