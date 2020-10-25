<?php

use Xmf\Request;

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
?>
<?php
require_once __DIR__ . '/core.php';

$t_core_path = config_get('core_path');

require_once $t_core_path . 'current_user_api.php';
?>
<?php
$f_project_id = gpc_get_int('project_id');
$f_make_default = gpc_get_bool('make_default');
$f_ref = gpc_get_string('ref', '');

if (ALL_PROJECTS != $f_project_id) {
    project_ensure_exists($f_project_id);
}

# Set default project
if ($f_make_default) {
    current_user_set_default_project($f_project_id);
}

helper_set_current_project($f_project_id);

#@@@ we really need to make this more general... it is never intuitive
#  to redirect to the main page as far as I can see. Is there a reason
#  we can't just redirect back to the referrer in all cases?  See
#  issue #2686 about this... -jf

# redirect to 'same page' when switching projects.
# view_all_* pages, and summary
# for proxies that clear out HTTP_REFERER
if (!is_blank($f_ref)) {
    $t_redirect_url = $f_ref;
} elseif (!isset(Request::getString('HTTP_REFERER', '', 'SERVER')) || is_blank(Request::getString('HTTP_REFERER', '', 'SERVER'))) {
    $t_redirect_url = 'main_page.php';
} elseif (eregi('view_all_bug_page.php', Request::getString('HTTP_REFERER', '', 'SERVER'))) {
    $t_redirect_url = 'view_all_set.php?type=0';
} elseif (eregi('summary_page.php', Request::getString('HTTP_REFERER', '', 'SERVER'))) {
    $t_redirect_url = 'summary_page.php';
} elseif (eregi('proj_user_menu_page.php', Request::getString('HTTP_REFERER', '', 'SERVER'))) {
    $t_redirect_url = 'proj_user_menu_page.php';
} elseif (eregi('manage_user_page.php', Request::getString('HTTP_REFERER', '', 'SERVER'))) {
    $t_redirect_url = 'manage_user_page.php';
} else {
    $t_redirect_url = 'main_page.php';
}

print_header_redirect($t_redirect_url);
?>
<?php html_page_top1() ?>
<?php
html_meta_redirect($t_redirect_url);
?>
<?php html_page_top1() ?>

<br>
<div align="center">
    <?php
    echo lang_get('operation_successful') . '<br>';

    print_bracket_link($t_redirect_url, lang_get('proceed'));
    ?>
</div>

<?php html_page_bottom1(__FILE__) ?>
