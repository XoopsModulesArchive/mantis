<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details

# --------------------------------------------------------
# $Id: login_cookie_test.php,v 1.4 2003/01/18 02:14:12 jfitzell Exp $
# --------------------------------------------------------
?>
<?php
# Check to see if cookies are working
?>
<?php require_once('core.php') ?>
<?php
$f_return = gpc_get_string('return', 'main_page.php');

if (auth_is_user_authenticated()) {
    $t_redirect_url = $f_return;
} else {
    $t_redirect_url = 'login_page.php?cookie_error=1';
}

print_header_redirect($t_redirect_url);
?>
