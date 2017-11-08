<?php
/**
 * @var \zaek\engine\CMain $this
 */
namespace zaek;

use zaek\kernel\CException;

if( isset($_POST['module']) ) {

    $module = $_POST['module'];

    $fs = $this->fs()->getFs(__DIR__ . '/filter')['files'];
    foreach ( $fs as $file ) {
        $this->includeFile($file);
    }

    $root_dir = $this->conf()->get('repo', 'dir');

    exec("cd {$root_dir}" . $module . '/ && git tag', $aVersion);
    natsort($aVersion);

    if (!isset($_POST['version']) || $_POST['version'] == 'last') {
        $aTmp = array_slice($aVersion, -1);
        $version_request = array_pop($aTmp);
    } else {
        $version_request = $_POST['version'];
    }



    if (in_array($version_request, $aVersion)) {
        $file_path = $root_dir . $module . '_' . $version_request . '.tar';

        exec('rm ' . $file_path);

        // CD to module directory
        // make module archive
        $command = "cd {$root_dir}" . $module . '/ && git archive --format tar -o ' . $file_path . ' ' . $version_request;

        // Show as output
        exec($command);

        if (file_exists($file_path)) {
            $this->template()->end();

            header('Content-Description: Za-EK CMS');
            header('Content-Type: application/x-tar');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);

            exit;
        }
    }
} else {
    throw new CException('MODULE_NOT_FOUND');
}
