<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details

# --------------------------------------------------------
# $Id: bug_view_page.php,v 1.45 2003/03/10 18:14:10 jfitzell Exp $
# --------------------------------------------------------
?>
<?php
require_once __DIR__ . '/core.php';

$t_core_path = config_get('core_path');

require_once $t_core_path . 'bug_api.php';
require_once $t_core_path . 'custom_field_api.php';
require_once $t_core_path . 'compress_api.php';
require_once $t_core_path . 'current_user_api.php';
require_once $t_core_path . 'file_api.php';
require_once $t_core_path . 'date_api.php';
require_once $t_core_path . 'relationship_api.php';
?>
<?php
$f_bug_id = gpc_get_int('bug_id');
$f_history = gpc_get_bool('history', config_get('history_default_visible'));

if (ADVANCED_ONLY == config_get('show_view')) {
    print_header_redirect('bug_view_advanced_page.php?bug_id=' . $f_bug_id);
}

access_ensure_bug_level(VIEWER, $f_bug_id);

$t_bug = bug_prepare_display(bug_get($f_bug_id, true));

compress_enable();
?>
<?php html_page_top1() ?>
<?php html_page_top2() ?>

<br>
<table class="width100" cellspacing="1">


    <tr>

        <!-- Title -->
        <td class="form-title" colspan="4">
            <?php echo lang_get('viewing_bug_simple_details_title') ?>

            <!-- Jump to Bugnotes -->
            <span class="small"><?php print_bracket_link('#bugnotes', lang_get('jump_to_bugnotes')) ?></span>

            <!-- Send Bug Reminder -->
            <?php
            if (!current_user_is_anonymous()
                && access_has_bug_level(config_get('bug_reminder_threshold'), $f_bug_id)) {
                ?>
                <span class="small">
			<?php print_bracket_link('bug_reminder_page.php?bug_id=' . $f_bug_id, lang_get('bug_reminder')) ?>
		</span>
                <?php
            }
            ?>

        </td>

        <!-- Links -->
        <td class="right" colspan="2">

            <!-- Advanced View (if enabled) -->
            <?php if (BOTH == config_get('show_view')) { ?>
                <span class="small"><?php print_bracket_link('bug_view_advanced_page.php?bug_id=' . $f_bug_id, lang_get('view_advanced_link')) ?></span>
            <?php } ?>

            <!-- History -->
            <span class="small"><?php print_bracket_link('bug_view_page.php?bug_id=' . $f_bug_id . '&amp;history=1#history', lang_get('bug_history')) ?></span>

            <!-- Print Bug -->
            <span class="small"><?php print_bracket_link('print_bug_page.php?bug_id=' . $f_bug_id, lang_get('print')) ?></span>

        </td>

    </tr>


    <!-- Labels -->
    <tr class="row-category">
        <td>
            <?php echo lang_get('id') ?>
        </td>
        <td>
            <?php echo lang_get('category') ?>
        </td>
        <td>
            <?php echo lang_get('severity') ?>
        </td>
        <td>
            <?php echo lang_get('reproducibility') ?>
        </td>
        <td>
            <?php echo lang_get('date_submitted') ?>
        </td>
        <td>
            <?php echo lang_get('last_update') ?>
        </td>
    </tr>


    <tr <?php echo helper_alternate_class() ?>>

        <!-- Bug ID -->
        <td>
            <?php echo bug_format_id($f_bug_id) ?>
        </td>

        <!-- Category -->
        <td>
            <?php echo $t_bug->category ?>
        </td>

        <!-- Severity -->
        <td>
            <?php echo get_enum_element('severity', $t_bug->severity) ?>
        </td>

        <!-- Reproducibility -->
        <td>
            <?php echo get_enum_element('reproducibility', $t_bug->reproducibility) ?>
        </td>

        <!-- Date Submitted -->
        <td>
            <?php print_date(config_get('normal_date_format'), $t_bug->date_submitted) ?>
        </td>

        <!-- Date Updated -->
        <td>
            <?php print_date(config_get('normal_date_format'), $t_bug->last_updated) ?>
        </td>

    </tr>


    <!-- spacer -->
    <tr height="5" class="spacer">
        <td colspan="6"></td>
    </tr>


    <tr <?php echo helper_alternate_class() ?>>

        <!-- Reporter -->
        <td class="category">
            <?php echo lang_get('reporter') ?>
        </td>
        <td>
            <?php print_user_with_subject($t_bug->reporter_id, $f_bug_id) ?>
        </td>

        <!-- View Status -->
        <td class="category">
            <?php echo lang_get('view_status') ?>
        </td>
        <td>
            <?php echo get_enum_element('project_view_state', $t_bug->view_state) ?>
        </td>

        <!-- spacer -->
        <td colspan="2">&nbsp;</td>

    </tr>


    <!-- Handler -->
    <tr <?php echo helper_alternate_class() ?>>
        <td class="category">
            <?php echo lang_get('assigned_to') ?>
        </td>
        <td colspan="5">
            <?php print_user_with_subject($t_bug->handler_id, $f_bug_id) ?>
        </td>
    </tr>


    <tr <?php echo helper_alternate_class() ?>>

        <!-- Priority -->
        <td class="category">
            <?php echo lang_get('priority') ?>
        </td>
        <td>
            <?php echo get_enum_element('priority', $t_bug->priority) ?>
        </td>

        <!-- Resolution -->
        <td class="category">
            <?php echo lang_get('resolution') ?>
        </td>
        <td>
            <?php echo get_enum_element('resolution', $t_bug->resolution) ?>
        </td>

        <!-- spacer -->
        <td colspan="2">&nbsp;</td>
    </tr>


    <tr <?php echo helper_alternate_class() ?>>

        <!-- Status -->
        <td class="category">
            <?php echo lang_get('status') ?>
        </td>
        <td bgcolor="<?php echo get_status_color($t_bug->status) ?>">
            <?php echo get_enum_element('status', $t_bug->status) ?>
        </td>

        <!-- Duplicate ID -->
        <td class="category">
            <?php echo lang_get('duplicate_id') ?>
        </td>
        <td>
            <?php print_duplicate_id($t_bug->duplicate_id) ?>
        </td>

        <!-- spacer -->
        <td colspan="2">&nbsp;</td>

    </tr>


    <!-- spacer -->
    <tr height="5" class="spacer">
        <td colspan="6"></td>
    </tr>


    <!-- Summary -->
    <tr <?php echo helper_alternate_class() ?>>
        <td class="category">
            <?php echo lang_get('summary') ?>
        </td>
        <td colspan="5">
            <?php echo $t_bug->summary ?>
        </td>
    </tr>


    <!-- Description -->
    <tr <?php echo helper_alternate_class() ?>>
        <td class="category">
            <?php echo lang_get('description') ?>
        </td>
        <td colspan="5">
            <?php echo $t_bug->description ?>
        </td>
    </tr>


    <!-- Additional Information -->
    <tr <?php echo helper_alternate_class() ?>>
        <td class="category">
            <?php echo lang_get('additional_information') ?>
        </td>
        <td colspan="5">
            <?php echo $t_bug->additional_information ?>
        </td>
    </tr>


    <!-- spacer -->
    <tr height="5" class="spacer">
        <td colspan="6"></td>
    </tr>


    <!-- Custom Fields -->
    <?php
    $t_custom_fields_found = false;
    $t_related_custom_field_ids = custom_field_get_linked_ids(helper_get_current_project());
    foreach ($t_related_custom_field_ids as $t_id) {
        if (!$t_def['advanced'] && custom_field_has_read_access($t_id, $f_bug_id)) {
            $t_def = custom_field_get_definition($t_id);

            $t_custom_fields_found = true; ?>
            <tr <?php echo helper_alternate_class() ?>>
                <td class="category">
                    <?php echo lang_get_defaulted($t_def['name']) ?>
                </td>
                <td colspan="5">
                    <?php
                    $t_custom_field_value = custom_field_get_value($t_id, $f_bug_id);

            if (CUSTOM_FIELD_TYPE_EMAIL == $t_def['type']) {
                echo "<a href=\"mailto:$t_custom_field_value\">$t_custom_field_value</a>";
            } else {
                echo $t_custom_field_value;
            } ?>
                </td>
            </tr>
            <?php
        } # !$t_def['advanced'] && has read access
    } # foreach
    ?>


    <?php if ($t_custom_fields_found) { ?>
        <!-- spacer -->
        <tr height="5" class="spacer">
            <td colspan="6"></td>
        </tr>
    <?php } # custom fields found?>


    <!-- Attachments -->
    <?php
    $t_show_attachments = ($t_bug->reporter_id == auth_get_current_user_id()) || access_has_bug_level(config_get('view_attachments_threshold'), $f_bug_id);

    if ($t_show_attachments) {
        ?>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category">
                <?php echo lang_get('attached_files') ?>
            </td>
            <td colspan="5">
                <?php file_list_attachments($f_bug_id); ?>
            </td>
        </tr>
        <?php
    }
    ?>


    <!-- Bug Relationships -->
    <tr <?php echo helper_alternate_class() ?>>
        <td class="category">
            <?php echo lang_get('bug_relationships') ?>
        </td>
        <td colspan="5">
            <?php
            $result = relationship_fetch_all_src($f_bug_id);
            $relationship_count = db_num_rows($result);
            for ($i = 0; $i < $relationship_count; $i++) {
                $row = db_fetch_array($result);

                $t_bug_link = string_get_bug_view_link($row['destination_bug_id']);

                switch ($row['relationship_type']) {
                    case BUG_DUPLICATE:
                        $t_description = str_replace('%id', $t_bug_link, lang_get('duplicate_of'));
                        break;
                    case BUG_RELATED:
                        $t_description = str_replace('%id', $t_bug_link, lang_get('related_to'));
                        break;
                    case BUG_DEPENDANT:
                        $t_description = str_replace('%id', $t_bug_link, lang_get('dependant_on'));
                        break;
                    default:
                        $t_description = str_replace('%id', $t_bug_link, lang_get('duplicate_of'));
                }

                echo $t_description . '<br>';
            }
            ?>
            <?php
            $result = relationship_fetch_all_dest($f_bug_id);
            $relationship_count = db_num_rows($result);
            for ($i = 0; $i < $relationship_count; $i++) {
                $row = db_fetch_array($result);

                $t_bug_link = string_get_bug_view_link($row['source_bug_id']);

                switch ($row['relationship_type']) {
                    case BUG_DUPLICATE:
                        $t_description = str_replace('%id', $t_bug_link, lang_get('has_duplicate'));
                        break;
                    case BUG_RELATED:
                        $t_description = str_replace('%id', $t_bug_link, lang_get('related_to'));
                        break;
                    case BUG_DEPENDANT:
                        $t_description = str_replace('%id', $t_bug_link, lang_get('blocks'));
                        break;
                    default:
                        $t_description = str_replace('%id', $t_bug_link, lang_get('has_duplicate'));
                }

                echo $t_description . '<br>';
            }
            ?>
        </td>
    </tr>


    <!-- Buttons -->
    <tr align="center">
        <td class="center" colspan="6">
            <?php
            html_buttons_view_bug_page($f_bug_id);
            ?>
        </td>
    </tr>


</table>

<?php
$t_mantis_dir = __DIR__ . DIRECTORY_SEPARATOR;

# File upload box
if ($t_bug->status < config_get('bug_resolved_status_threshold')) {
    include $t_mantis_dir . 'bug_file_upload_inc.php';
}

# User list monitoring the bug
include $t_mantis_dir . 'bug_monitor_list_view_inc.php';

# Bugnotes
include $t_mantis_dir . 'bugnote_view_inc.php';
include $t_mantis_dir . 'bugnote_add_inc.php';

# History
if ($f_history) {
    include $t_mantis_dir . 'history_inc.php';
}

html_page_bottom1(__FILE__);
?>
