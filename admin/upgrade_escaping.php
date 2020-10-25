<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
?>
<?php
require_once __DIR__ . '/upgrade_inc.php';

$upgrade_set = new UpgradeSet();

$upgrade_set->add_items(require __DIR__ . '/upgrades/0_17_escaping_fixes_inc.php');

if (module_has_integrated_display()) {
    module_display_admin_header('', 'Mantis Administration - String Escaping Database Fixes');
} else {
    ?>
<html>
<head>
    <title> Mantis Administration - String Escaping Database Fixes </title>
    <link rel="stylesheet" type="text/css" href="admin.css">
</head>
<body>
<?php
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
    <tr class="top-bar">
        <td class="links">
            [ <a href="upgrade_list.php">Back to Upgrade List</a> ]
            [ <a href="upgrade_escaping.php">Refresh view</a> ]
        </td>
        <td class="title">
            String Escaping Database Fixes
        </td>
    </tr>
</table>
<br><br>

<?php
$upgrade_set->process_post_data();

if (module_has_integrated_display()) {
    module_display_admin_footer();
} else {
    ?>
</body>
</html>
<?php
}
?>
