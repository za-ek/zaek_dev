<?php
$this->template()->end();

$aChanges = array();
$module = $_GET['module'];

$root = $this->conf()->get('repo', 'dir');

exec('cd '.$root.$module.' && git diff --name-only --staged', $aNames);
foreach ( $aNames as $file ) {
    exec('cd '.$root.$module.' && git diff --staged "' . $file . '"', $aChanges[$file]);
    array_splice($aChanges[$file], 0, 2);
}

foreach ( $aChanges as $file => $a ) {
    foreach ( $a as $k => $v ) {
        if ( in_array($v, array(
            '\ No newline at end of file'
        ))) {
            unset($aChanges[$file][$k]);
        } else {
            $aChanges[$file][$k] = htmlentities($v);
        }
    }
}

foreach ( $aChanges as $file => $c ) {
    echo "<h4>{$file}</h4>";
    echo "<style type='text/css'>.diff_table td{ padding:2px 3px !important; }</style><div class='diff_table' style='overflow-x:scroll;font-family:monospace;'><table class='table table-hovered'>";
    foreach ( $c as $line ) {
        if ( preg_match ( '#\@\@ ([\-,\d]{1,}) ([\+,\d]{1,}) \@\@#', $line ) ) {
            // echo "<tr><td></td><td>___________</td></tr>";
        }
        $type = substr($line, 0, 1);
        switch ( $type ) {
            case '-':
                $line = substr($line, 1);
                echo "<tr style='background-color:#f5f5f5;color:#f00;'><td>-</td><td style='white-space:pre'>{$line}</td></tr>";
                break;
            case '+':
                $line = substr($line, 1);
                echo "<tr style='background-color:#fafafa;color:#007000;'><td>+</td><td style='white-space:pre'>{$line}</td></tr>";
                break;
            default:
                echo "<tr><td></td><td style='white-space:pre'>{$line}</td></tr>";
        }
    }
    echo "</table></div>";
}

die();
