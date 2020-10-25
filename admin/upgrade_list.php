<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'module_api.php';
if (module_has_integrated_display()) {
    module_display_admin_header('', 'Mantis Administration - Upgrade List');
} else {
    ?>
<html>
<head>
    <title> Mantis Administration - Upgrade List </title>
    <link rel="stylesheet" type="text/css" href="admin.css">
</head>
<body>
<?php
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
    <tr class="top-bar">
        <td class="links">
            [ <a href="index.php">Back to Administration</a> ]
        </td>
        <td class="title">
            Upgrade Installation
        </td>
    </tr>
</table>
<br><br>
<div align="center">
    <table width="75%">
        <tr>
            <td align="center">
                <h1>List of Upgrade Sets</h1>
                <p>[ <a href="upgrade.php">Basic upgrade set (required)</a> ]</p>
                <p>[ <a href="upgrade_escaping.php">String escaping fixes (recommended)</a> ]</p>
            </td>
        </tr>
    </table>
</div>
<?php
if (module_has_integrated_display()) {
    module_display_admin_footer();
} else {
    ?>
</body>
</html>
<?php
}
?>
