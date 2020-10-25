<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details

# --------------------------------------------------------
# $Id: logout_page.php,v 1.13 2003/02/27 08:14:55 jfitzell Exp $
# --------------------------------------------------------
?>
<?php
# Removes all the cookies and then redirect to the page specified in
#  the config option logout_redirect_page
?>
<?php require_once __DIR__ . '/core.php'; ?>
<?php
auth_logout();

print_header_redirect(config_get('logout_redirect_page'));
?>
