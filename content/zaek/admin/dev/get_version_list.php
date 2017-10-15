<?php
if ( isset($_POST['module']) ) {
    $module = preg_replace('#[^\d\w_-]*#', '', $_POST['module']);
    $root_dir = $this->conf()->get('repo', 'dir');
    exec("cd {$root_dir}" . $_POST['module'] . '/ && git tag', $aVersion);
    return ['versions' => $aVersion];
}
