<?php

# Modified for Modularization for Xoops 2 CMS
# Copyright (C) 2003  Michael van Dam - mvandam@caltech.edu

#$zip->ctrl_dir = 'man';

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'module_api.php';

# --- creates the page ---
ob_start();

require __DIR__ . '/core.php';
if (module_has_integrated_display()) {
    # FIXME: make module type independent

    require __DIR__ . '/xoops_css_inc.php';
} else {
    require __DIR__ . '/css_inc.php';
}

$content = ob_get_contents();

ob_end_clean();

header('Cache-control: private');
#header( "Content-type: application/octet-stream" );
header('Content-type: text/css');
header('Content-Disposition: attachment; filename="filename.css"');
header('Content-Description: CSS File');

# --- ---
echo $content;
