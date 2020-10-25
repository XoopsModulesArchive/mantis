<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# Modified for Modularization for Xoops 2 CMS
# Copyright (C) 2003  Michael van Dam - mvandam@caltech.edu

# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details

# --------------------------------------------------------
# $Id$
# --------------------------------------------------------

###########################################################################
# Database
###########################################################################

# This is the general interface for all database calls.
# Use this as a starting point to port to other databases

# An array in which all executed queries are stored.  This is used for profiling
$g_queries_array = [];

# Stores whether a database connection was succesfully opened.
$g_db_connected = false;

# --------------------
# Make a connection to the database
function db_connect($p_hostname, $p_username, $p_password, $p_port)
{
    global $g_db_connected;

    if (module_has_integrated_database()) {
        $g_db_connected = true;

        return true;
    }

    $t_result = @mysql_connect("$p_hostname:$p_port", $p_username, $p_password);

    if (!$t_result) {
        db_error();

        trigger_error(ERROR_DB_CONNECT_FAILED, ERROR);

        return false;
    }

    $g_db_connected = true;

    return true;
}

# --------------------
# Make a persistent connection to the database
function db_pconnect($p_hostname, $p_username, $p_password, $p_port)
{
    global $g_db_connected;

    if (module_has_integrated_database()) {
        $g_db_connected = true;

        return true;
    }

    $t_result = @mysql_pconnect("$p_hostname:$p_port", $p_username, $p_password);

    if (!$t_result) {
        db_error();

        trigger_error(ERROR_DB_CONNECT_FAILED, ERROR);

        return false;
    }

    $g_db_connected = true;

    return true;
}

# --------------------
# Returns whether a connection to the database exists
function db_is_connected()
{
    global $g_db_connected;

    return $g_db_connected;
}

# --------------------
# execute query, requires connection to be opened
# If $p_error_on_failure is true (default) an error will be triggered
#  if there is a problem executing the query.
# If $p_error_on_failure is false, false will be returned if there is a
#  problem.  This should be used very infrequently.  It was added to allow
#  the admin script to check whether a table exists.
function db_query($p_query, $p_error_on_failure = true)
{
    global $g_queries_array;

    $g_queries_array[] = $p_query;

    if (module_has_integrated_database()) {
        $t_result = module_db_query($p_query);
    } else {
        $t_result = @$GLOBALS['xoopsDB']->queryF($p_query);
    }

    # @@@ remove p_error_on_failure and use @ in every caller that used to use it

    if (!$t_result && $p_error_on_failure) {
        db_error($p_query);

        trigger_error(ERROR_DB_QUERY_FAILED, ERROR);

        return false;
    }

    return $t_result;
}

# --------------------
function db_select_db($p_db_name)
{
    if (module_has_integrated_database()) {
        return true;
    }

    $t_result = @mysqli_select_db($GLOBALS['xoopsDB']->conn, $p_db_name);

    if (!$t_result) {
        db_error();

        trigger_error(ERROR_DB_SELECT_FAILED, ERROR);

        return false;
    }

    return $t_result;
}

# --------------------
function db_num_rows($p_result)
{
    if (module_has_integrated_database()) {
        return module_db_num_rows($p_result);
    }

    return $GLOBALS['xoopsDB']->getRowsNum($p_result);
}

# --------------------
function db_affected_rows()
{
    if (module_has_integrated_database()) {
        return module_db_affected_rows();
    }

    return $GLOBALS['xoopsDB']->getAffectedRows();
}

# --------------------
function db_fetch_array($p_result)
{
    if (module_has_integrated_database()) {
        return module_db_fetch_array($p_result);
    }

    return $GLOBALS['xoopsDB']->fetchBoth($p_result);
}

# --------------------
function db_result($p_result, $p_index1 = 0, $p_index2 = 0)
{
    # NOTE: This may not have a counterpart in the module.

    # Provided $p_result is a valid MySQL result resource,

    # then this will work.

    if ($p_result && (db_num_rows($p_result) > 0)) {
        return mysql_result($p_result, $p_index1, $p_index2);
    }

    return false;
}

# --------------------
# return the last inserted id
function db_insert_id()
{
    if (db_affected_rows() > 0) {
        if (module_has_integrated_database()) {
            return module_db_insert_id();
        }

        return $GLOBALS['xoopsDB']->getInsertId();
    }

    return false;
}

# --------------------
function db_field_exists($p_field_name, $p_table_name, $p_db_name = '')
{
    # NOTE: Modules may not provide a counterpart for this.

    # However, if available, the DB name is taken from the

    # CMS to be used below.

    # FIXME: Be careful if have other database connections.

    if ('' == $p_db_name) {
        if (module_has_integrated_database()) {
            $p_db_name = module_db_name();
        } else {
            global $g_database_name;

            $p_db_name = $g_database_name;
        }
    }

    $fields = mysql_list_fields($p_db_name, $p_table_name);

    $columns = mysqli_num_fields($fields);

    for ($i = 0; $i < $columns; $i++) {
        if (mysql_field_name($fields, $i) == $p_field_name) {
            return true;
        }
    }

    return false;
}

# --------------------
function db_error_num()
{
    if (module_has_integrated_database()) {
        return module_db_error_num();
    }

    return $GLOBALS['xoopsDB']->errno();
}

# --------------------
function db_error_msg()
{
    if (module_has_integrated_database()) {
        return module_db_error_msg();
    }

    return $GLOBALS['xoopsDB']->error();
}

# --------------------
# display both the error num and error msg
function db_error($p_query = null)
{
    if (null !== $p_query) {
        error_parameters(db_error_num(), db_error_msg(), $p_query);
    } else {
        error_parameters(db_error_num(), db_error_msg());
    }
}

# --------------------
# close the connection.
# Not really necessary most of the time since a connection is
# automatically closed when a page finishes loading.
function db_close()
{
    if (module_has_integrated_database()) {
        // Do nothing
    } else {
        $t_result = $GLOBALS['xoopsDB']->close();
    }
}

# --------------------
# prepare a string before DB insertion
function db_prepare_string($p_string)
{
    # NOTE: Module may not provide this function, or may

    # provide it in a format we cannot use.  e.g. Xoops 2

    # adds single quotes around, but mantis queries absence

    # of these single quotes.

    return $GLOBALS['xoopsDB']->escape($p_string);
}

# --------------------
# prepare an integer before DB insertion
function db_prepare_int($p_int)
{
    return (int)$p_int;
}

# --------------------
# prepare a boolean before DB insertion
function db_prepare_bool($p_bool)
{
    return (int)(bool)$p_bool;
}

if (!isset($g_skip_open_db)) {
    if (module_has_integrated_database()) {
        // Nothing to do
    } else {
        if (OFF == $g_use_persistent_connections) {
            db_connect($g_hostname, $g_db_username, $g_db_password, $g_port);
        } else {
            db_pconnect($g_hostname, $g_db_username, $g_db_password, $g_port);
        }

        db_select_db($g_database_name);
    }
}
