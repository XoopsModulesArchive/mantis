<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details

# --------------------------------------------------------
# $Id: manage_proj_ver_add.php,v 1.26 2003/02/18 02:18:01 jfitzell Exp $
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

if (is_blank($f_version)) {
    trigger_error(ERROR_EMPTY_FIELD, ERROR);
}

# We reverse the array so that if the user enters multiple versions
#  they will likely appear with the last item entered at the top of the list
#  (i.e. in reverse chronological order).  Unless we find a way to make the
#  date_order fields different for each one, however, this is fragile, since
#  the DB may actually pull the rows out in any order
$t_versions = array_reverse(explode('|', $f_version));
$t_version_count = count($t_versions);

foreach ($t_versions as $t_version) {
    if (is_blank($t_version)) {
        continue;
    }

    $t_version = trim($t_version);

    if (version_is_unique($f_project_id, $t_version)) {
        version_add($f_project_id, $t_version);
    } elseif (1 == $t_version_count) {
        # We only error out on duplicates when a single value was

        #  given.  If multiple values were given, we just add the

        #  ones we can.  The others already exist so it isn't really

        #  an error.

        trigger_error(ERROR_VERSION_DUPLICATE, ERROR);
    }
}

$t_redirect_url = 'manage_proj_edit_page.php?project_id=' . $f_project_id;
?>
<?php
html_page_top1();

html_meta_redirect($t_redirect_url);

html_page_top2();
?>

<br>
<div align="center">
    <?php
    echo lang_get('operation_successful') . '<br>';

    print_bracket_link($t_redirect_url, lang_get('proceed'));
    ?>
</div>

<?php html_page_bottom1(__FILE__) ?>
