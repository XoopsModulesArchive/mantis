<?php

# Modified for Modularization for Xoops 2 CMS
# Copyright (C) 2003  Michael van Dam - mvandam@caltech.edu

# Define constants for identifying module type

define('MANTIS_MODULE_XOOPS2', 1);

# Return true if mantis is running as a module; false otherwise.
function module_is_module()
{
    global $g_module_type;

    return !empty($g_module_type) ? true : false;
}

# Return (string) type of module
function module_type()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            return 'Xoops 2';
            break;
        default:
            return '';
            break;
    }
}

# Return true if mantis is using external user management; false otherwise.
function module_has_integrated_users()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            # FIXME: How to implement choice?
            return true;
            break;
        default:
            return false;
            break;
    }
}

# Return true if mantis is using external database; false otherwise.
function module_has_integrated_database()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            # FIXME: How to implement choice?
            return true;
            break;
        default:
            return false;
            break;
    }
}

# Return true if mantis is interated into CMS display; false if standalone.
function module_has_integrated_display()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            global $xoopsModuleConfig;

            return !empty($xoopsModuleConfig['integrate_display']) ? true : false;
            break;
        default:
            return false;
            break;
    }
}

#
# All functions below assume you have first checked whether the module
# supports the category of functions.
#

#
# Database Functions
#

# Prefix the $original prefix with the CMS prefix
function module_db_prefix($original_prefix)
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            global $xoopsDB;

            return $xoopsDB->prefix($original_prefix);
    }
}

# Perform a query
function module_db_query($query)
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            global $xoopsDB;
            # NOTE: need queryF (instead of query) since mantis not comply
            # with GET/POST for read/write entirely.  e.g. updates last
            # visit field for users.
            return $xoopsDB->queryF($query);
            break;
        default:
            return false;
            break;
    }
}

# Return the number of rows in a result set
function module_db_num_rows($result)
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            global $xoopsDB;

            return $xoopsDB->getRowsNum($result);
            break;
        default:
            return false;
            break;
    }
}

# Return the number of rows affected by last query
function module_db_affected_rows()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            global $xoopsDB;

            return $xoopsDB->getAffectedRows();
            break;
        default:
            return false;
            break;
    }
}

# Fetch an array from the result set
function module_db_fetch_array($result)
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            global $xoopsDB;

            return $xoopsDB->fetchArray($result);
            break;
        default:
            return false;
            break;
    }
}

# Return last inserted id
function module_db_insert_id()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            global $xoopsDB;

            return $xoopsDB->getInsertId();
            break;
        default:
            return false;
            break;
    }
}

# Return error number for last query
function module_db_error_num()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            global $xoopsDB;

            return $xoopsDB->errno();
            break;
        default:
            return false;
            break;
    }
}

# Return error message for last query
function module_db_error_msg()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            global $xoopsDB;

            return $xoopsDB->error();
            break;
        default:
            return false;
            break;
    }
}

# The following functions provide detailed information about the
# database.  Normally they are not needed, but certain mysql
# functions require the database name.

# Return the database name
function module_db_name()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            return XOOPS_DB_NAME;
            break;
        default:
            return false;
            break;
    }
}

#
# Site Config Functions
#

# TODO: split into two classes of functions... module_config_xxx and
# module_siteconfig_xxx... if the need arises.

# Return the default language of the site.  Must be in $g_language_choices_arr
# of config_defaults_inc.php.  Return false if none specified.
function module_config_default_language()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            # Xoops does not have user-selectable language, so we
            # return the site language
            global $xoopsConfig;
            switch ($xoopsConfig['language']) {
                case 'english':
                case 'french':
                case 'german':
                case 'italian':
                case 'romanian':
                case 'russian':
                case 'spanish':
                    $g_default_language = $xoopsConfig['language'];
                    break;
                case 'japanese':
                    $g_default_language = 'japanese_euc'; // FIXME: check this
                    break;
                case 'nederlands':
                    $g_default_language = 'dutch';
                    break;
                case 'portuguesebr':
                    $g_default_language = 'portuguese_brazil';
                    break;
                case 'portuguesept':
                    $g_default_language = 'portuguese_standard'; // FIXME: check
                    break;
                case 'schinese':
                    $g_default_language = 'chinese_simplified';
                    break;
                case 'tchinese':
                    $g_default_language = 'chinese_traditional';
                    break;
                case 'vietnamese':
                default:
                    $g_default_language = 'english'; // not in mantis.. which?
                    break;
            }

            // no break
        default:
            return false;
            break;
    }
}

# FIXME: Use language string here?
# Return a link (HTML) for the site homepage
function module_config_homepage_link()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            return '<a href="' . XOOPS_URL . '">Home</a>';
            break;
        default:
            return '';
            break;
    }
}

#
# User Functions
#

# Return the current user's selected language; false if not selectable
function module_current_user_language()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
        default:
            return false;
            break;
    }
}

# Return the current user's email address; false if not specified
function module_user_email($username)
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            $memberHandler = xoops_getHandler('member');
            $user          = array_shift($memberHandler->getUsers(new Criteria('uname', $username)));
            if (!empty($user)) {
                return $user->getVar('email');
            }

                return false;
    }
}

# Return true if the current user has admin privileges for this module;
# otherwise return false.
function module_current_user_is_admin()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            global $xoopsUser;
            global $xoopsModule;
            if (empty($xoopsUser)) {
                return false;
            }

            return $xoopsUser->isAdmin($xoopsModule->getVar('mid')) ? true : false;
            break;
        default:
            return false;
            break;
    }
}

# Return email of current user; false if not specified
function module_current_user_email()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            global $xoopsUser;
            if (!empty($xoopsUser)) {
                return $xoopsUser->getVar('email');
            }

                return false;
    }
}

# Return username of current user.  If user is anonymous, return the
# anonymous user name.
function module_current_user_name()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            global $xoopsUser;
            if (!empty($xoopsUser)) {
                return $xoopsUser->getVar('uname');
            }
                global $xoopsConfig;

                return $xoopsConfig['anonymous'];
            break;
        default:
            return false;
            break;
    }
}

# Return true if current user is anonymous; false otherwise
function module_current_user_is_anonymous()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            global $xoopsUser;

            return empty($xoopsUser) ? true : false;
            break;
        default:
            return false;
            break;
    }
}

#
# Display Functions
#

# Generate the module header
function module_display_header($html_header, $page_title = false)
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
        default:
            // FIXME: which globals do we really need?
            global $xoopsRequestUri, $xoopsConfig, $xoopsOption, $xoopsTpl, $xoopsUser, $xoopsModule, $xoopsLogger, $xoopsCachedTemplate, $xoopsCachedTemplateId, $xoopsConfigMetaFooter;
            require XOOPS_ROOT_PATH . '/header.php';
            $xoopsTpl->assign('xoops_module_header', $html_header);
            if (false !== $page_title) {
                $xoopsTpl->assign('xoops_pagetitle', $page_title);
            }
            break;
    }
}

# Generate the module footer
function module_display_footer()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
        default:
            // FIXME: which globals do we really need?
            global $xoopsLogger, $xoopsOption, $xoopsConfigMetaFooter, $xoopsTpl, $xoopsCachedTemplateId, $xoopsCachedTemplate, $xoopsConfig, $xoopsUser, $xoopsModule;
            require XOOPS_ROOT_PATH . '/footer.php';
            break;
    }
}

# Generate the module admin header.
function module_display_admin_header($html_header, $page_title = false)
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            # Xoops not have support for easily inserting headers/title
            ### require_once XOOPS_ROOT_PATH . '/include/cp_functions.php';
            ### xoops_cp_header();

            # Ensure user is administrator.
            # TODO: move this to separate function?
            if (module_has_integrated_users() && !module_current_user_is_admin()) {
                ob_end_clean();

                header('Location: ' . XOOPS_URL . '/modules/mantis/main_page.php');

                exit;
            }

            # Include the admin stylesheet.
            # TODO: does this really belong here?
            $style_file = XOOPS_URL . '/modules/mantis/admin/xoops_admin.css';
            $style_header = '<link rel="stylesheet" type="text/css" href="' . $style_file . '">';
            module_display_header($style_header . $html_header, $page_title);
            break;
        default:
            break;
    }
}

# Generate the module admin footer
function module_display_admin_footer()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            ### require_once XOOPS_ROOT_PATH . '/include/cp_functions.php';
            ### xoops_cp_footer();
            module_display_footer();
            break;
        default:
            break;
    }
}

# Specify the CSS prefix.  This is useful if module content is enclosed
# in a DIV, for example.
function module_display_css_prefix()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            $prefix = 'div#content';
            break;
        default:
            $prefix = '';
            break;
    }

    return $prefix . ' ';
}

# Specify the admin CSS prefix
function module_display_admin_css_prefix()
{
    global $g_module_type;

    switch ($g_module_type) {
        case MANTIS_MODULE_XOOPS2:
            $prefix = 'div#content';
            break;
        default:
            $prefix = '';
            break;
    }

    return $prefix . ' ';
}

#
# Global Variable to store module type
#

$g_module_type = false;

#
# Attempt to automatically detect the type of module, and include any
# necessary files.
#

# Xoops 2

$xoops2_file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'mainfile.php';

if (file_exists($xoops2_file)) {
    if (!defined(XOOPS_ROOT_PATH)) {
        require_once $xoops2_file;
    }

    $g_module_type = MANTIS_MODULE_XOOPS2;

    # Declare globals, since xoops doesn't do this.  Only need to do this

    # in case 'mainfile.php' was included elsewhere and we may no longer

    # be in same scope.  Only need to declare any that are used below.

    global $xoopsModule, $xoopsUser, $xoopsConfig;

    # FIXME: The rest is a hack.  Xoops doesn't detect module if

    # script not in root path (e.g. mantis/admin/) so we do it by

    # hand here.  I considered putting in Xoops admin by using

    # xoops_cp_header/footer, but there is no easy way to insert

    # module-defined CSS, so some of mantis admin functions will be

    # pretty useless.  Besides, what we really want in xoops admin

    # style is the 'manage' functions instead.

    if (empty($xoopsModule)) {
        $moduleHandler = xoops_getHandler('module');

        $xoopsModule = $moduleHandler->getByDirname('mantis');

        if (!$xoopsModule || !$xoopsModule->getVar('isactive')) {
            require_once XOOPS_ROOT_PATH . '/header.php';

            echo '<h4>' . _MODULENOEXIST . '</h4>';

            require_once XOOPS_ROOT_PATH . '/footer.php';

            exit();
        }

        $modulepermHandler = xoops_getHandler('groupperm');

        if ($xoopsUser) {
            if (!$modulepermHandler->checkRight('module_read', $xoopsModule->getVar('mid'), $xoopsUser->getGroups())) {
                redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);

                exit();
            }
        } else {
            if (!$modulepermHandler->checkRight('module_read', $xoopsModule->getVar('mid'), XOOPS_GROUP_ANONYMOUS)) {
                redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);

                exit();
            }
        }

        if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/' . $xoopsConfig['language'] . '/main.php')) {
            require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/' . $xoopsConfig['language'] . '/main.php';
        } else {
            if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/english/main.php')) {
            }
        }

        if (1 == $xoopsModule->getVar('hasconfig') || 1 == $xoopsModule->getVar('hascomments') || 1 == $xoopsModule->getVar('hasnotification')) {
            $configHandler = xoops_getHandler('config');

            $xoopsModuleConfig = $configHandler->getConfigsByCat(0, $xoopsModule->getVar('mid'));
        }
    }
}

unset($xoops2_file);
