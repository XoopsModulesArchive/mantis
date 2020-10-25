<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details

# --------------------------------------------------------
# $Id: icon_api.php,v 1.7 2003/02/09 22:15:17 jfitzell Exp $
# --------------------------------------------------------

###########################################################################
# Icon API
###########################################################################

# --------------------
# Icon associative arrays
# --------------------
# Status to icon mapping
$g_status_icon_arr = [
    NONE => 'mantis-space.gif',
LOW => 'mantis-space.gif',
NORMAL => 'mantis-space.gif',
HIGH => 'priority_1.gif',
URGENT => 'priority_2.gif',
IMMEDIATE => 'priority_3.gif',
];
# --------------------
# Sort direction to icon mapping
$g_sort_icon_arr = [
    ASC => 'up.gif',
DESC => 'down.gif',
];
# --------------------
# Read status to icon mapping
$g_unread_icon_arr = [
    READ => 'mantis-space.gif',
UNREAD => 'unread.gif',
];
# --------------------
###########################################################################
# Icon Print API
###########################################################################
# --------------------
# prints the staus icon
function print_status_icon($p_icon)
{
    global $g_icon_path, $g_status_icon_arr;

    $t_none = NONE;

    if (!is_blank($g_status_icon_arr[$p_icon])) {
        print "<img src=\"$g_icon_path$g_status_icon_arr[$p_icon]\" alt=\"\">";
    } else {
        print "<img src=\"$g_icon_path$g_status_icon_arr[$t_none]\" alt=\"\">";
    }
}

# --------------------
# The input $p_dir is either ASC or DESC
# The inputs $p_sort_by and $p_field are compared to see if they match
# If the fields match then the sort icon is printed
# This is a convenience feature to push the comparison code into this
#     function instead of in the page(s)
# $p_field is a constant and $p_sort_by is whatever the page happens to
#     be sorting by at the moment
# Multiple sort keys are not supported
function print_sort_icon($p_dir, $p_sort_by, $p_field)
{
    global $g_icon_path, $g_sort_icon_arr;

    if ($p_sort_by != $p_field) {
        return;
    }

    if ('DESC' == $p_dir || DESC == $p_dir) {
        $t_dir = DESC;
    } else {
        $t_dir = ASC;
    }

    $t_none = NONE;

    if (!is_blank($g_sort_icon_arr[$t_dir])) {
        print "<img src=\"$g_icon_path$g_sort_icon_arr[$t_dir]\" alt=\"\">";
    } else {
        print "<img src=\"$g_icon_path$g_status_icon_arr[$t_none]\" alt=\"\">";
    }
}

# --------------------
# prints the unread icon if the parameter is UNREAD
# @@@ UNUSED
function print_unread_icon($p_unread = READ)
{
    global $g_icon_path, $g_unread_icon_arr;

    $t_none = NONE;

    if (!is_blank($g_unread_icon_arr[$p_unread])) {
        print "<img src=\"$g_icon_path$g_unread_icon_arr[$p_unread]\" alt=\"\">";
    } else {
        print "<img src=\"$g_icon_path$g_status_icon_arr[$t_none]\" alt=\"\">";
    }
}

# --------------------
