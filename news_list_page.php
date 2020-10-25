<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
?>
<?php
require_once __DIR__ . '/core.php';

$t_core_path = config_get('core_path');

require_once $t_core_path . 'news_api.php';
require_once $t_core_path . 'string_api.php';
?>
<?php
access_ensure_project_level(VIEWER);
?>
<?php html_page_top1() ?>
<?php html_page_top2() ?>

<br>
<ul>
    <?php
    # Select the news posts
    $rows = news_get_rows(helper_get_current_project());
    # Loop through results
    for ($i = 0, $iMax = count($rows); $i < $iMax; $i++) {
        extract($rows[$i], EXTR_PREFIX_ALL, 'v');

        if (VS_PRIVATE == $v_view_state
            && !access_has_project_level(config_get('private_news_threshold'), $v_project_id)) {
            continue;
        }

        $v_headline = string_display($v_headline);

        $v_date_posted = date(config_get('complete_date_format'), $v_date_posted);

        $t_notes = [];

        $t_note_string = '';

        if (1 == $v_announcement) {
            $t_notes[] = lang_get('announcement');
        }

        if (VS_PRIVATE == $v_view_state) {
            $t_notes[] = lang_get('private');
        }

        if (count($t_notes) > 0) {
            $t_note_string = '[' . implode(' ', $t_notes) . ']';
        }

        echo "<li><span class=\"italic-small\">$v_date_posted</span> - <span class=\"bold\"><a href=\"news_view_page.php?news_id=$v_id\">$v_headline</a></span> <span class=\"small\">$t_note_string ";

        print_user($v_poster_id);

        echo '</span></li>';
    }  # end for loop
    ?>
</ul>

<?php html_page_bottom1(__FILE__) ?>
