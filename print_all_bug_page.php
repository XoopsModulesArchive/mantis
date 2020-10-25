<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
?>
<?php
# Bugs to display / print / export can be selected with the checkboxes
# A printing Options link allows to choose the fields to export
# Export :
#	- the bugs displayed in print_all_bug_page.php are saved in a .doc or .xls file
#   - the IE icons allows to see or directly print the same result
?>
<?php
require_once __DIR__ . '/core.php';

$t_core_path = config_get('core_path');

require_once $t_core_path . 'current_user_api.php';
require_once $t_core_path . 'bug_api.php';
require_once $t_core_path . 'date_api.php';
require_once $t_core_path . 'icon_api.php';
require_once $t_core_path . 'string_api.php';
?>
<?php auth_ensure_user_authenticated() ?>
<?php
$f_search = gpc_get_string('search', false); # @@@ need a better default
$f_offset = gpc_get_int('offset', 0);

$t_cookie_value = gpc_get_cookie(config_get('view_all_cookie'), '');

# check to see if the cookie does not exist
if (is_blank($t_cookie_value)) {
    print_header_redirect('view_all_set.php?type=0&amp;print=1');
}

# check to see if new cookie is needed
$t_setting_arr = explode('#', $t_cookie_value);
if ($t_setting_arr[0] != $g_cookie_version) {
    print_header_redirect('view_all_set.php?type=0&amp;print=1');
}

# Load preferences
$f_show_category = $t_setting_arr[1];
$f_show_severity = $t_setting_arr[2];
$f_show_status = $t_setting_arr[3];
$f_per_page = $t_setting_arr[4];
$f_highlight_changed = $t_setting_arr[5];
$f_hide_closed = $t_setting_arr[6];
$f_reporter_id = $t_setting_arr[7];
$fHandler_id = $t_setting_arr[8];
$f_sort = $t_setting_arr[9];
$f_dir = $t_setting_arr[10];
$f_start_month = $t_setting_arr[11];
$f_start_day = $t_setting_arr[12];
$f_start_year = $t_setting_arr[13];
$f_end_month = $t_setting_arr[14];
$f_end_day = $t_setting_arr[15];
$f_end_year = $t_setting_arr[16];
$f_hide_resolved = $t_setting_arr[18];

# Clean input
$c_offset = (int)$f_offset;
$c_user_id = (int)$f_reporter_id;
$c_assign_id = (int)$fHandler_id;
$c_per_page = (int)$f_per_page;
$c_show_category = addslashes($f_show_category);
$c_show_severity = addslashes($f_show_severity);
$c_show_status = addslashes($f_show_status);
$c_search = addslashes($f_search);
$c_sort = addslashes($f_sort);

if ('DESC' == $f_dir) {
    $c_dir = 'DESC';
} else {
    $c_dir = 'ASC';
}

# Limit reporters to only see their reported bugs
if ((ON == $g_limit_reporters)
    && (!access_has_project_level(UPDATER))) {
    $c_user_id = auth_get_current_user_id();
}

# Build our query string based on our viewing criteria

$query = 'SELECT DISTINCT *, UNIX_TIMESTAMP(last_updated) AS last_updated
			 FROM $g_mantis_bug_table';

$t_project_id = helper_get_current_project();

# project selection
if (ALL_PROJECTS == $t_project_id) { # ALL projects
    $t_access_level = current_user_get_field('access_level');

    $t_user_id = auth_get_current_user_id();

    $t_pub = VS_PUBLIC;

    $t_prv = VS_PRIVATE;

    $query2 = "SELECT DISTINCT(p.id)
			FROM $g_mantis_project_table p, $g_mantis_project_user_list_table u
			WHERE (p.enabled=1 AND
				p.view_state='$t_pub') OR
				(p.enabled=1 AND
				p.view_state='$t_prv' AND
				u.user_id='$t_user_id'  AND
				u.project_id=p.id)
			ORDER BY p.name";

    $result2 = db_query($query2);

    $project_count = db_num_rows($result2);

    if (0 == $project_count) {
        $t_where_clause = ' WHERE 1=1';
    } else {
        $t_where_clause = ' WHERE (';

        for ($i = 0; $i < $project_count; $i++) {
            $row = db_fetch_array($result2);

            extract($row, EXTR_PREFIX_ALL, 'v');

            $t_where_clause .= "(project_id='$v_id')";

            if ($i < $project_count - 1) {
                $t_where_clause .= ' OR ';
            }
        } # end for

        $t_where_clause .= ')';
    }
} else {
    $t_where_clause = " WHERE project_id='$t_project_id'";
}
# end project selection

if ('any' != $c_user_id) {
    $t_where_clause .= " AND reporter_id='$c_user_id'";
}

if ('none' == $fHandler_id) {
    $t_where_clause .= ' AND handler_id=0';
} elseif ('any' != $fHandler_id) {
    $t_where_clause .= " AND handler_id='$c_assign_id'";
}

$t_clo_val = CLOSED;
if (('on' == $f_hide_closed) && ('closed' != $f_show_status)) {
    $t_where_clause .= " AND status<>'$t_clo_val'";
}

$t_resolved_val = RESOLVED;
if (('on' == $f_hide_resolved) && ('resolved' != $f_show_status)) {
    $t_where_clause .= " AND status<>'$t_resolved_val'";
}

if ('any' != $f_show_category) {
    $t_where_clause .= " AND category='$c_show_category'";
}
if ('any' != $f_show_severity) {
    $t_where_clause .= " AND severity='$c_show_severity'";
}
if ('any' != $f_show_status) {
    $t_where_clause .= " AND status='$c_show_status'";
}

# Simple Text Search - Thnaks to Alan Knowles
if ($f_search) {
    $t_columns_clause = " $g_mantis_bug_table.*";

    $t_where_clause .= " AND ((summary LIKE '%$c_search%')
							OR (description LIKE '%$c_search%')
							OR (steps_to_reproduce LIKE '%$c_search%')
							OR (additional_information LIKE '%$c_search%')
							OR ($g_mantis_bug_table.id LIKE '%$c_search%')
							OR ($g_mantis_bugnote_text_table.note LIKE '%$c_search%'))
							AND $g_mantis_bug_text_table.id = $g_mantis_bug_table.bug_text_id";

    $t_from_clause = " FROM $g_mantis_bug_table, $g_mantis_bug_text_table
							LEFT JOIN $g_mantis_bugnote_table      ON $g_mantis_bugnote_table.bug_id  = $g_mantis_bug_table.id
							LEFT JOIN $g_mantis_bugnote_text_table ON $g_mantis_bugnote_text_table.id = $g_mantis_bugnote_table.bugnote_text_id ";
} else {
    $t_columns_clause = ' *';

    $t_from_clause = " FROM $g_mantis_bug_table";
}

if (is_blank($c_sort)) {
    $c_sort = 'last_updated';
}
$query = 'SELECT DISTINCT ' . $t_columns_clause . ', UNIX_TIMESTAMP(last_updated) as last_updated';
$query .= $t_from_clause;
$query .= $t_where_clause;

$query .= " ORDER BY '$c_sort' $c_dir";
if ('priority' != $f_sort) {
    $query .= ', priority DESC';
}

$query .= " LIMIT $c_offset, $c_per_page";

# perform query
$result = db_query($query);
$row_count = db_num_rows($result);

# for export
$t_show_flag = gpc_get_bool('show_flag');
?>
<?php html_page_top1() ?>
<?php html_head_end() ?>
<?php html_body_begin() ?>

<table class="width100">
    <tr>
        <td class="form-title">
            <div class="center">
                <?php echo config_get('window_title') . ' - ' . project_get_name($t_project_id); ?>
            </div>
        </td>
    </tr>
</table>

<br>

<form method="post" action="view_all_set.php">
    <input type="hidden" name="type" value="1">
    <input type="hidden" name="print" value="1">
    <input type="hidden" name="offset" value="0">
    <input type="hidden" name="sort" value="<?php echo $f_sort ?>">
    <input type="hidden" name="dir" value="<?php echo $f_dir ?>">

    <table class="width100">
        <tr>
            <td class="print">
                <?php echo lang_get('search') ?>
            </td>
            <td class="print">
                <?php echo lang_get('reporter') ?>
            </td>
            <td class="print">
                <?php echo lang_get('assigned_to') ?>
            </td>
            <td class="print">
                <?php echo lang_get('category') ?>
            </td>
            <td class="print">
                <?php echo lang_get('severity') ?>
            </td>
            <td class="print">
                <?php echo lang_get('status') ?>
            </td>
            <td class="print">
                <?php echo lang_get('show') ?>
            </td>
            <td class="print">
                <?php echo lang_get('changed') ?>
            </td>
            <td class="print">
                <?php echo lang_get('hide_status') ?>
            </td>
        </tr>
        <tr>
            <td>
                <input type="text" name="search" size="15" value="<?php echo $f_search; ?>">
            </td>
            <td>
                <select name="reporter_id">
                    <option value="any"><?php echo lang_get('any') ?></option>
                    <option value="any"></option>
                    <?php print_reporter_option_list($f_reporter_id) ?>
                </select>
            </td>
            <td>
                <select name="handler_id">
                    <option value="any"><?php echo lang_get('any') ?></option>
                    <option value="none" <?php check_selected($fHandler_id, 'none'); ?>><?php echo lang_get('none') ?></option>
                    <option value="any"></option>
                    <?php print_assign_to_option_list($fHandler_id) ?>
                </select>
            </td>
            <td>
                <select name="show_category">
                    <option value="any"><?php echo lang_get('any') ?></option>
                    <option value="any"></option>
                    <?php print_category_option_list($f_show_category) ?>
                </select>
            </td>
            <td>
                <select name="show_severity">
                    <option value="any"><?php echo lang_get('any') ?></option>
                    <option value="any"></option>
                    <?php print_enum_string_option_list('severity', $f_show_severity) ?>
                </select>
            </td>
            <td>
                <select name="show_status">
                    <option value="any"><?php echo lang_get('any') ?></option>
                    <option value="any"></option>
                    <?php print_enum_string_option_list('status', $f_show_status) ?>
                </select>
            </td>
            <td>
                <input type="text" name="per_page" size="3" maxlength="7" value="<?php echo $f_per_page ?>">
            </td>
            <td>
                <input type="text" name="highlight_changed" size="3" maxlength="7" value="<?php echo $f_highlight_changed ?>">
            </td>
            <td>
                <input type="checkbox" name="hide_resolved" <?php check_checked($f_hide_resolved, 'on'); ?>>&nbsp;<?php echo lang_get('filter_resolved'); ?>
                <input type="checkbox" name="hide_closed" <?php check_checked($f_hide_closed, 'on'); ?>>&nbsp;<?php echo lang_get('filter_closed'); ?>
            </td>
        </tr>

        <?php
        #<SQLI> Excel & Print export
        #$f_bug_array stores the number of the selected rows
        #$t_bug_arr_sort is used for displaying
        #$f_export is a string for the word and excel pages

        $f_bug_arr = gpc_get_int_array('bug_arr', []);
        $f_bug_arr[$row_count] = -1;

        for ($i = 0; $i < $row_count; $i++) {
            if (isset($f_bug_arr[$i])) {
                $index = $f_bug_arr[$i];

                $t_bug_arr_sort[$index] = 1;
            }
        }
        $f_export = implode(',', $f_bug_arr);

        $t_icon_path = config_get('icon_path');
        ?>

        <tr>
            <td colspan="8">
                <?php
                if ('DESC' == $f_dir) {
                    $t_new_dir = 'ASC';
                } else {
                    $t_new_dir = 'DESC';
                }

                $t_search = urlencode($f_search);

                $t_icons = [
                    ['print_all_bug_page_excel', 'excel', '', 'excelicon.gif', 'Excel 2000'],
                    ['print_all_bug_page_excel', 'html', 'target="_blank"', 'ieicon.gif', 'Excel View'],
                    ['print_all_bug_page_word', 'word', '', 'wordicon.gif', 'Word 2000'],
                    ['print_all_bug_page_word', 'html', 'target="_blank"', 'ieicon.gif', 'Word View'],
                ];

                foreach ($t_icons as $t_icon) {
                    echo '<a href="'
                         . $t_icon[0]
                         . '.php'
                         . "?search=$t_search"
                         . "&amp;sort=$f_sort"
                         . "&amp;dir=$t_new_dir"
                         . '&amp;type_page='
                         . $t_icon[1]
                         . "&amp;export=$f_export"
                         . "&amp;show_flag=$t_show_flag"
                         . '" '
                         . $t_icon[2]
                         . '>'
                         . '<img src="'
                         . $t_icon_path
                         . $t_icon[3]
                         . '" border="0" align="absmiddle" alt="'
                         . $t_icon[4]
                         . '"></a> ';
                }
                ?>
            </td>
            <td class="right">
                <input type="submit" value="<?php echo lang_get('filter_button') ?>">
            </td>
        </tr>
        <?php #<SQLI>?>
    </table>

</form>

<br>

<form method="post" action="print_all_bug_page.php">
    <table class="width100" cellspacing="1">
        <tr>
            <td class="form-title" colspan="6">
                <?php echo lang_get('viewing_bugs_title') ?>
                <?php
                if ($row_count > 0) {
                    $v_start = $f_offset + 1;

                    $v_end = $f_offset + $row_count;
                } else {
                    $v_start = 0;

                    $v_end = 0;
                }
                print "( $v_start - $v_end )";
                ?>
            </td>
            <td class="right" colspan="3">
                <?php print_bracket_link('print_all_bug_options_page.php', lang_get('printing_options_link')) ?>
                <?php print_bracket_link('view_all_bug_page.php', lang_get('view_bugs_link')) ?>
                <?php print_bracket_link('summary_page.php', lang_get('summary')) ?>
            </td>
        </tr>
        <tr class="row-category">
            <td class="center" width="2%">&nbsp;</td>
            <td class="center" width="8%">
                <?php print_view_bug_sort_link2('P', 'priority', $f_sort, $f_dir) ?>
                <?php print_sort_icon($f_dir, $f_sort, 'priority') ?>
            </td>
            <td class="center" width="8%">
                <?php print_view_bug_sort_link2(lang_get('id'), 'id', $f_sort, $f_dir) ?>
                <?php print_sort_icon($f_dir, $f_sort, 'id') ?>
            </td>
            <td class="center" width="3%">
                #
            </td>
            <td class="center" width="12%">
                <?php print_view_bug_sort_link2(lang_get('category'), 'category', $f_sort, $f_dir) ?>
                <?php print_sort_icon($f_dir, $f_sort, 'category') ?>
            </td>
            <td class="center" width="10%">
                <?php print_view_bug_sort_link2(lang_get('severity'), 'severity', $f_sort, $f_dir) ?>
                <?php print_sort_icon($f_dir, $f_sort, 'severity') ?>
            </td>
            <td class="center" width="10%">
                <?php print_view_bug_sort_link2(lang_get('status'), 'status', $f_sort, $f_dir) ?>
                <?php print_sort_icon($f_dir, $f_sort, 'status') ?>
            </td>
            <td class="center" width="12%">
                <?php print_view_bug_sort_link2(lang_get('updated'), 'last_updated', $f_sort, $f_dir) ?>
                <?php print_sort_icon($f_dir, $f_sort, 'last_updated') ?>
            </td>
            <td class="center" width="37%">
                <?php print_view_bug_sort_link2(lang_get('summary'), 'summary', $f_sort, $f_dir) ?>
                <?php print_sort_icon($f_dir, $f_sort, 'summary') ?>
            </td>
        </tr>
        <tr>
            <td class="spacer" colspan="9">&nbsp;</td>
        </tr>
        <?php
        for ($i = 0; $i < $row_count; $i++) {
            # prefix bug data with v_

            $row = db_fetch_array($result);

            extract($row, EXTR_PREFIX_ALL, 'v');

            $v_summary = string_display_links($v_summary);

            $t_last_updated = date($g_short_date_format, $v_last_updated);

            # alternate row colors

            $status_color = helper_alternate_colors($i, '#ffffff', '#dddddd');

            # grab the bugnote count

            $bugnote_count = bug_get_bugnote_count($v_id);

            # grab the project name

            $project_name = project_get_field($v_project_id, 'name');

            $query = "SELECT MAX( last_modified )
				FROM $g_mantis_bugnote_table
				WHERE bug_id='$v_id'";

            $res2 = db_query($query);

            $v_bugnote_updated = db_result($res2, 0, 0);

            if (isset($t_bug_arr_sort[$i]) || (0 == $t_show_flag)) {
                ?>

                <tr>
                    <td class="print" bgcolor="<?php echo $status_color ?>">
                        <input type="checkbox" name="bug_arr[]" value="<?php echo $i ?>">
                    </td>
                    <td class="print" bgcolor="<?php echo $status_color ?>">
                        <?php print_formatted_priority_string($v_status, $v_priority) ?>
                    </td>
                    <td class="print" bgcolor="<?php echo $status_color ?>">
                        <?php echo $v_id ?>
                    </td>
                    <td class="print" bgcolor="<?php echo $status_color ?>">
                        <?php
                        if ($bugnote_count > 0) {
                            if ($v_bugnote_updated > strtotime("-$f_highlight_changed hours")) {
                                print "<span class=\"bold\">$bugnote_count</span>";
                            } else {
                                echo $bugnote_count;
                            }
                        } else {
                            print '&nbsp;';
                        } ?>
                    </td>
                    <td class="print" bgcolor="<?php echo $status_color ?>">
                        <?php
                        # Print project name if viewing 'all projects'
                        if (ALL_PROJECTS == $t_project_id) {
                            print "[$project_name] <br>";
                        } ?>
                        <?php echo $v_category ?>
                    </td>
                    <td class="print" bgcolor="<?php echo $status_color ?>">
                        <?php print_formatted_severity_string($v_status, $v_severity) ?>
                    </td>
                    <td class="print" bgcolor="<?php echo $status_color ?>">
                        <?php
                        echo get_enum_element('status', $v_status);

                # print username instead of status

                if ($vHandler_id > 0 && ON == config_get('show_assigned_names')) {
                    echo '(' . user_get_name($vHandler_id) . ')';
                } ?>
                    </td>
                    <td class="print" bgcolor="<?php echo $status_color ?>">
                        <?php
                        if ($v_last_updated > strtotime("-$f_highlight_changed hours")) {
                            print "<span class=\"bold\">$t_last_updated</span>";
                        } else {
                            echo $t_last_updated;
                        } ?>
                    </td>
                    <td class="left" bgcolor="<?php echo $status_color ?>">
		<span class="print"><?php echo $v_summary ?></a>
                    </td>
                </tr>
                <?php
            } # isset_loop
        } # for_loop
        ?>
        <input type="hidden" name="show_flag" value="1">
    </table>

    <br>

    <input type="submit" value="<?php echo lang_get('hide_button') ?>">
</form>

<?php # @@@ BUG ?  Where is the closing FORM tag????>
