<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details

# --------------------------------------------------------
# $Id: manage_proj_ver_delete.php,v 1.19 2003/02/15 10:25:17 jfitzell Exp $
# --------------------------------------------------------
?>
<?php
require_once __DIR__ . '/core.php';

$t_core_path = config_get('core_path');

require_once $t_core_path . 'version_api.php';
?>
<?php
$f_project_id = gpc_get_int('project_id');
$f_version = gpc_get_string('version');

access_ensure_project_level(config_get('manage_project_threshold'), $f_project_id);

helper_ensure_confirmed(
    lang_get('version_delete_sure'),
    lang_get('delete_version_button')
);

version_remove($f_project_id, $f_version);

print_header_redirect('manage_proj_edit_page.php?project_id=' . $f_project_id);
?>
