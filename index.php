<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details

# --------------------------------------------------------
# $Id: index.php,v 1.13 2003/01/18 02:14:12 jfitzell Exp $
# --------------------------------------------------------
?>
<?php require_once('core.php') ?>
<?php
if (auth_is_user_authenticated()) {
    print_header_redirect('main_page.php');
} else {
    print_header_redirect('login_page.php');
}
?>
