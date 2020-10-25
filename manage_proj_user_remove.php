<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details

# --------------------------------------------------------
# $Id: manage_proj_user_remove.php,v 1.3 2003/02/15 10:25:17 jfitzell Exp $
# --------------------------------------------------------
?>
<?php require_once('core.php') ?>
<?php
$f_project_id = gpc_get_int('project_id');
$f_user_id = gpc_get_int('user_id');

# We should check both since we are in the project section and an
#  admin might raise the first threshold and not realize they need
#  to raise the second
access_ensure_project_level(config_get('manage_project_threshold'), $f_project_id);
access_ensure_project_level(config_get('project_user_threshold'), $f_project_id);

project_remove_user($f_project_id, $f_user_id);

print_header_redirect('manage_proj_edit_page.php?project_id=' . $f_project_id);
?>
