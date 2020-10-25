<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details

# --------------------------------------------------------
# $Id: filter_api.php,v 1.12 2003/03/09 03:08:58 jfitzell Exp $
# --------------------------------------------------------

$t_core_dir = __DIR__ . DIRECTORY_SEPARATOR;

require_once $t_core_dir . 'current_user_api.php';

###########################################################################
# Filter API
###########################################################################

# @@@ Had to make all these parameters required because we can't use
#  call-time pass by reference anymore.  I really preferred not having
#  to pass all the params in if you didn't want to, but I wanted to get
#  rid of the errors for now.  If we can think of a better way later
#  (maybe return an object) that would be great.
#
# $p_page_numer
#   - the page you want to see (set to the actual page on return)
# $p_per_page
#   - the number of bugs to see per page (set to actual on return)
#     -1   indicates you want to see all bugs
#     null indicates you want to use the value specified in the filter
# $p_page_count
#   - you don't need to give a value here, the number of pages will be
#     stored here on return
# $p_bug_count
#   - you don't need to give a value here, the number of bugs will be
#     stored here on return
function filter_get_bug_rows(&$p_page_number, &$p_per_page, &$p_page_count, &$p_bug_count)
{
    $t_bug_table = config_get('mantis_bug_table');

    $t_bug_text_table = config_get('mantis_bug_text_table');

    $t_bugnote_table = config_get('mantis_bugnote_table');

    $t_bugnote_text_table = config_get('mantis_bugnote_text_table');

    $t_project_table = config_get('mantis_project_table');

    $t_filter = current_user_get_bug_filter();

    if (false === $t_filter) {
        return false; # signify a need to create a cookie
        #@@@ error instead?
    }

    $t_project_id = helper_get_current_project();

    $t_user_id = auth_get_current_user_id();

    $t_where_clauses = ["$t_project_table.enabled = 1", "$t_project_table.id = $t_bug_table.project_id"];

    $t_select_clauses = ["$t_bug_table.*"];

    $t_from_clauses = [$t_bug_table, $t_project_table];

    $t_join_clauses = [];

    if (ALL_PROJECTS == $t_project_id) {
        if (!current_user_is_administrator()) {
            $t_projects = current_user_get_accessible_projects();

            if (0 == count($t_projects)) {
                return [];  # no accessible projects, return an empty array
            }

            $t_clauses = [];

            #@@@ use project_id IN (1,2,3,4) syntax if we can

            for ($i = 0, $iMax = count($t_projects); $i < $iMax; $i++) {
                $t_clauses[] = "($t_bug_table.project_id='$t_projects[$i]')";
            }

            $t_where_clauses[] = '(' . implode(' OR ', $t_clauses) . ')';
        }
    } else {
        access_ensure_project_level(VIEWER, $t_project_id);

        $t_where_clauses[] = "($t_bug_table.project_id='$t_project_id')";
    }

    # private bug selection

    if (!access_has_project_level(config_get('private_bug_threshold'))) {
        $t_public = VS_PUBLIC;

        $t_private = VS_PRIVATE;

        $t_where_clauses[] = "($t_bug_table.view_state='$t_public' OR ($t_bug_table.view_state='$t_private' AND $t_bug_table.reporter_id='$t_user_id'))";
    }

    # reporter

    if ('any' != $t_filter['reporter_id']) {
        $c_reporter_id = db_prepare_int($t_filter['reporter_id']);

        $t_where_clauses[] = "($t_bug_table.reporter_id='$c_reporter_id')";
    }

    # handler

    if ('none' == $t_filter['handler_id']) {
        $t_where_clauses[] = "$t_bug_table.handler_id=0";
    } elseif ('any' != $t_filter['handler_id']) {
        $cHandler_id = db_prepare_int($t_filter['handler_id']);

        $t_where_clauses[] = "($t_bug_table.handler_id='$cHandler_id')";
    }

    # hide closed

    if (('on' == $t_filter['hide_closed']) && (CLOSED != $t_filter['show_status'])) {
        $t_closed = CLOSED;

        $t_where_clauses[] = "($t_bug_table.status<>'$t_closed')";
    }

    # hide resolved

    if (('on' == $t_filter['hide_resolved']) && (RESOLVED != $t_filter['show_status'])) {
        $t_resolved = RESOLVED;

        $t_where_clauses[] = "($t_bug_table.status<>'$t_resolved')";
    }

    # category

    if ('any' != $t_filter['show_category']) {
        $c_show_category = db_prepare_string($t_filter['show_category']);

        $t_where_clauses[] = "($t_bug_table.category='$c_show_category')";
    }

    # severity

    if ('any' != $t_filter['show_severity']) {
        $c_show_severity = db_prepare_string($t_filter['show_severity']);

        $t_where_clauses[] = "($t_bug_table.severity='$c_show_severity')";
    }

    # status

    if ('any' != $t_filter['show_status']) {
        $c_show_status = db_prepare_string($t_filter['show_status']);

        $t_where_clauses[] = "($t_bug_table.status='$c_show_status')";
    }

    # Simple Text Search - Thnaks to Alan Knowles

    if (!is_blank($t_filter['search'])) {
        $c_search = db_prepare_string($t_filter['search']);

        $t_where_clauses[] = "((summary LIKE '%$c_search%')
							 OR ($t_bug_text_table.description LIKE '%$c_search%')
							 OR ($t_bug_text_table.steps_to_reproduce LIKE '%$c_search%')
							 OR ($t_bug_text_table.additional_information LIKE '%$c_search%')
							 OR ($t_bug_table.id LIKE '%$c_search%')
							 OR ($t_bugnote_text_table.note LIKE '%$c_search%'))";

        $t_where_clauses[] = "($t_bug_text_table.id = $t_bug_table.bug_text_id)";

        $t_from_clauses[] = $t_bug_text_table;

        $t_join_clauses[] = "LEFT JOIN $t_bugnote_table ON $t_bugnote_table.bug_id = $t_bug_table.id";

        $t_join_clauses[] = "LEFT JOIN $t_bugnote_text_table ON $t_bugnote_text_table.id = $t_bugnote_table.bugnote_text_id";
    }

    $t_select = implode(', ', array_unique($t_select_clauses));

    $t_from = 'FROM ' . implode(', ', array_unique($t_from_clauses));

    $t_join = implode(' ', $t_join_clauses);

    if (count($t_where_clauses) > 0) {
        $t_where = 'WHERE ' . implode(' AND ', $t_where_clauses);
    } else {
        $t_where = '';
    }

    # Get the total number of bugs that meet the criteria.

    $query = "SELECT COUNT( DISTINCT ( $t_bug_table.id ) ) as count $t_from $t_join $t_where";

    $result = db_query($query);

    $bug_count = db_result($result);

    # write the value back in case the caller wants to know

    $p_bug_count = $bug_count;

    if (null === $p_per_page) {
        $p_per_page = (int)$t_filter['per_page'];
    } elseif (-1 == $p_per_page) {
        $p_per_page = $bug_count;
    }

    # Guard against silly values of $f_per_page.

    if (0 == $p_per_page) {
        $p_per_page = 1;
    }

    $p_per_page = (int)abs($p_per_page);

    # Use $bug_count and $p_per_page to determine how many pages

    # to split this list up into.

    # For the sake of consistency have at least one page, even if it

    # is empty.

    $t_page_count = ceil($bug_count / $p_per_page);

    if ($t_page_count < 1) {
        $t_page_count = 1;
    }

    # write the value back in case the caller wants to know

    $p_page_count = $t_page_count;

    # Make sure $p_page_number isn't past the last page.

    if ($p_page_number > $t_page_count) {
        $p_page_number = $t_page_count;
    }

    # Make sure $p_page_number isn't before the first page

    if ($p_page_number < 1) {
        $p_page_number = 1;
    }

    $query2 = "SELECT DISTINCT $t_select, UNIX_TIMESTAMP(last_updated) as last_updated
					$t_from
					$t_join
					$t_where";

    # Now add the rest of the criteria i.e. sorting, limit.

    $c_sort = db_prepare_string($t_filter['sort']);

    if ('DESC' == $t_filter['dir']) {
        $c_dir = 'DESC';
    } else {
        $c_dir = 'ASC';
    }

    $query2 .= " ORDER BY '$c_sort' $c_dir";

    # Figure out the offset into the db query

    # for example page number 1, per page 5:

    #     t_offset = 0

    # for example page number 2, per page 5:

    #     t_offset = 5

    $c_per_page = db_prepare_int($p_per_page);

    $c_page_number = db_prepare_int($p_page_number);

    $t_offset = (($c_page_number - 1) * $c_per_page);

    $query2 .= " LIMIT $t_offset, $c_per_page";

    # perform query

    $result2 = db_query($query2);

    $row_count = db_num_rows($result2);

    $rows = [];

    for ($i = 0; $i < $row_count; $i++) {
        $rows[] = db_fetch_array($result2);
    }

    return $rows;
}

# --------------------
# return true if the filter cookie exists and is of the correct version,
#  false otherwise
function filter_is_cookie_valid()
{
    $t_view_all_cookie = gpc_get_cookie(config_get('view_all_cookie'), '');

    # check to see if the cookie does not exist

    if (is_blank($t_view_all_cookie)) {
        return false;
    }

    # check to see if new cookie is needed

    $t_setting_arr = explode('#', $t_view_all_cookie);

    if ($t_setting_arr[0] != config_get('cookie_version')) {
        return false;
    }

    return true;
}
