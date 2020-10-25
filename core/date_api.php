<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details

# --------------------------------------------------------
# $Id: date_api.php,v 1.3 2003/01/03 03:24:24 jfitzell Exp $
# --------------------------------------------------------

###########################################################################
# Date API
###########################################################################

# --------------------
# prints the date given the formating string
function print_date($p_format, $p_date)
{
    echo date($p_format, $p_date);
}

# --------------------
function print_month_option_list($p_month = 0)
{
    for ($i = 1; $i <= 12; $i++) {
        $month_name = date('F', mktime(0, 0, 0, $i, 1, 2000));

        if ($i == $p_month) {
            print "<option value=\"$i\" selected=\"selected\">$month_name</option>";
        } else {
            print "<option value=\"$i\">$month_name</option>";
        }
    }
}

# --------------------
function print_day_option_list($p_day = 0)
{
    for ($i = 1; $i <= 31; $i++) {
        if ($i == $p_day) {
            print "<option value=\"$i\" selected=\"selected\"> $i </option>";
        } else {
            print "<option value=\"$i\"> $i </option>";
        }
    }
}

# --------------------
function print_year_option_list($p_year = 0)
{
    $current_year = date('Y');

    for ($i = $current_year; $i > 1999; $i--) {
        if ($i == $p_year) {
            print "<option value=\"$i\" selected=\"selected\"> $i </option>";
        } else {
            print "<option value=\"$i\"> $i </option>";
        }
    }
}

# --------------------
