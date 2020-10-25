<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'module_api.php';
if (module_has_integrated_display()) {
    module_display_admin_header('', 'Mantis Administration');
} else {
    ?>
<html>
<head>
    <title> Mantis Administration </title>
    <link rel="stylesheet" type="text/css" href="admin.css">
</head>
<body>
<?php
}
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
    <tr class="top-bar">
        <td class="links">
            &nbsp;
        </td>
        <td class="title">
            &nbsp;
        </td>
    </tr>
</table>
<br><br>
<div align="center">
    <table width="75%">
        <tr>
            <td align="center">
                <h1>Mantis Administration</h1>
                <p>Note: be sure to secure this area or remove it from your Mantis installation when you are done. Leaving the administration area unprotected after installation leaves system information and database update capabilities open to any unauthorized person.</p>
                <p>[ <a href="check.php">Check your installation</a> ]</p>
                <p>[ <a href="upgrade_warning.php">Upgrade your installation</a> ]</p>
                <p>[ <a href="css/index.php">Modify stylesheets</a> ]</p>
                <?php
                if (module_has_integrated_display()) {
                    ?>
                    <p>---</p>
                    <p>[ <a href="../main_page.php">Exit Administration</a> ]</p>
                    <?php
                }
                ?>
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
