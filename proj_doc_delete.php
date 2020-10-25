<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
?>
<?php require_once('core.php') ?>
<?php
# @@@ Need to obtain the project_id from the file once we have an API for that
access_ensure_project_level(MANAGER);

$f_file_id = gpc_get_int('file_id');

$c_file_id = (int)$f_file_id;

helper_ensure_confirmed(
    lang_get('confirm_file_delete_msg'),
    lang_get('file_delete_button')
);

if (DISK == $g_file_upload_method) {
    # grab the file name

    $query = "SELECT diskfile
				FROM $g_mantis_project_file_table
				WHERE id='$c_file_id'";

    $result = db_query($query);

    $t_diskfile = db_result($result);

    # in windows replace with system("del $t_diskfile");

    chmod($t_diskfile, 0775);

    unlink($t_diskfile);
}

$query = "DELETE FROM $g_mantis_project_file_table
			WHERE id='$c_file_id'";
$result = db_query($query);

$t_redirect_url = 'proj_doc_page.php';
if ($result) {
    print_header_redirect($t_redirect_url);
} else {
    print_mantis_error(ERROR_GENERIC);
}
?>
