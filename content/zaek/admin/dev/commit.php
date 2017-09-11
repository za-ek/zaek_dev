<?php
namespace zaek;

$this->template()->end();

$root_dir = $this->conf()->get('repo', 'dir');

$module = $_POST['code'];


$directory_path = $root_dir . $module;

$msg = str_replace("'", '"', $_POST['msg']);

if ( !file_exists($directory_path . '/content/zaek/admin/general/updates/current')) {
    mkdir($directory_path . '/content/zaek/admin/general/updates/current', 0777, true);
}

file_put_contents($directory_path . '/content/zaek/admin/general/updates/current/' . $module . '.php', '<?php return ' . $_POST['version'] . ';');
echo $directory_path;
exec ("cd {$directory_path} && git add 'content/zaek/admin/general/updates/current/{$module}.php' && git commit -m '{$msg}' && git tag -a '{$_POST['version']}' -m 'version {$_POST['version']}'");

die();