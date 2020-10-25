<?php

# Modified for Modularization for Xoops 2 CMS
# Copyright (C) 2003  Michael van Dam - mvandam@caltech.edu

// FIXME: also search for projects, categories??, and news... ??
// Need to do separate queries and append results...  limit and offset
// will be a little tricky...

function mantis_search($queryarray, $andor, $limit, $offset, $userid)
{
    // Define globals for mantis, and include main file.

    // Need to declare any global which may be used in the functions below.

    // FIXME: which are essential?

    global $g_core_path, $g_timer, $g_show_notices, $g_custom_headers, $g_string_cookie, $g_default_language, $g_show_warnings, $g_window_title, $g_html_valid_tags, $g_css_include_file, $g_meta_include_file, $g_content_expire, $g_top_include_page, $g_show_detailed_errors, $g_page_title, $g_module_type, $g_mantis_user_table, $g_queries_array;

    require_once XOOPS_ROOT_PATH . '/modules/mantis/core.php';

    ob_end_clean(); // started in core.php

    global $xoopsDB;

    // Modified from print_all_bug_page.php

    $search_clause = " ((bug.summary LIKE '%%%s%%')
                     OR (bugtext.description LIKE '%%%s%%')
                     OR (bugtext.steps_to_reproduce LIKE '%%%s%%')
                     OR (bugtext.additional_information LIKE '%%%s%%')
                     OR (bugnotetext.note LIKE '%%%s%%'))";

    $query = 'SELECT DISTINCT bug.id, bug.summary, bug.reporter_id, UNIX_TIMESTAMP(bug.last_updated) as last_updated';

    $query .= " FROM $g_mantis_bug_table as bug, $g_mantis_bug_text_table as bugtext
                LEFT JOIN $g_mantis_bugnote_table as bugnote
				ON bugnote.bug_id  = bug.id
                LEFT JOIN $g_mantis_bugnote_text_table as bugnotetext
				ON bugnotetext.id = bugnote.bugnote_text_id ";

    $query .= 'WHERE ';

    if (0 != $userid) {
        $memberHandler = xoops_getHandler('member');

        $user = $memberHandler->getUser($userid);

        // Mantis userid

        $userid = user_get_id_by_name($user->getVar('uname'));

        $query .= " bug.reporter_id = $userid AND ";
    }

    // because count() returns 1 even if a supplied variable

    // is not an array, we must check if $querryarray is really an array

    if (is_array($queryarray) && $count = count($queryarray)) {
        $temp = sprintf($search_clause, $queryarray[0], $queryarray[0], $queryarray[0], $queryarray[0], $queryarray[0], $queryarray[0]);

        $query .= '(' . $temp;

        for ($i = 1; $i < $count; $i++) {
            $query .= " $andor ";

            $temp = sprintf($search_clause, $queryarray[$i], $queryarray[$i], $queryarray[$i], $queryarray[$i], $queryarray[$i], $queryarray[$i]);

            $query .= $temp;
        }

        $query .= ') ';
    }

    $query .= 'ORDER BY last_updated DESC';

    $result = $xoopsDB->query($query, $limit, $offset);

    $ret = [];

    $i = 0;

    while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
        $ret[$i]['image'] = 'images/mantis.gif';

        $ret[$i]['link'] = 'bug_view_page.php?bug_id=' . $myrow['id'] . '';

        $ret[$i]['title'] = _BT_BUG . sprintf('%07d', $myrow['id']) . ' : ' . $myrow['summary'];

        $ret[$i]['time'] = $myrow['last_updated'];

        $ret[$i]['uid'] = $myrow['reporter_id'];

        $i++;
    }

    return $ret;
}
