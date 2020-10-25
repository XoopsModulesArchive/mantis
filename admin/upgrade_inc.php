<?php

# Mantis - a php based bugtracking system
# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
# Copyright (C) 2002 - 2003  Mantis Team   - mantisbt-dev@lists.sourceforge.net
# This program is distributed under the terms and conditions of the GPL
# See the README and LICENSE files for details
?>
<?php
require_once dirname(__DIR__) . '/core.php';

require_once __DIR__ . '/db_table_names_inc.php';

# Check if the upgrade table has been created yet
$query = "DESCRIBE $t_upgrade_table";
$result = db_query($query, false);

# If not, then create it
if (false === $result) {
    $query = "CREATE TABLE $t_upgrade_table
				  (upgrade_id char(20) NOT NULL,
				  description char(255) NOT NULL,
				  PRIMARY KEY (upgrade_id))";

    db_query($query);

    # 0.14.0 upgrades (applied to 0.13 db)

    if (admin_check_applied($t_project_table)) {
        $t_upgrades = require __DIR__ . '/upgrades/0_13_inc.php';

        foreach ($t_upgrades as $t_item) {
            $t_item->set_applied();
        }
    }

    # 0.15.0 upgrades (applied to 0.14 db)

    if (admin_check_applied($t_bug_file_table)) {
        $t_upgrades = require __DIR__ . '/upgrades/0_14_inc.php';

        foreach ($t_upgrades as $t_item) {
            $t_item->set_applied();
        }
    }

    # 0.16.0 upgrades (applied to 0.15 db)

    if (admin_check_applied($t_bug_history_table)) {
        $t_upgrades = require __DIR__ . '/upgrades/0_15_inc.php';

        foreach ($t_upgrades as $t_item) {
            $t_item->set_applied();
        }
    }

    # 0.17.0 upgrades (applied to 0.16 db)

    if (admin_check_applied($t_bug_monitor_table)) {
        $t_upgrades = require __DIR__ . '/upgrades/0_16_inc.php';

        foreach ($t_upgrades as $t_item) {
            $t_item->set_applied();
        }
    }
}

# Compatibility function
#
# The old upgrade system used this logic to determine whether an upgrade
#  had been done.  We use the same system to check and update the user's
#  database with the appropriate notations to indicate they have been done.
function admin_check_applied($p_table_name, $p_field_name = '')
{
    $c_table_name = db_prepare_string($p_table_name);

    $c_field_name = db_prepare_string($p_field_name);

    $result = db_query("DESCRIBE $c_table_name $c_field_name");

    if ($result && db_num_rows($result)) {
        return true;
    }

    return false;
}

?>
<?php

class Upgrade
{
    public $id;

    public $description;

    public function __construct($p_id, $p_description)
    {
        $this->id = $p_id;

        $this->description = $p_description;

        $this->error = '';
    }

    public function is_applied()
    {
        $t_upgrade_table = config_get('mantis_upgrade_table');

        $query = "SELECT COUNT(*)
					  FROM $t_upgrade_table
					  WHERE upgrade_id = '$this->id'";

        $result = db_query($query);

        if (db_result($result) > 0) {
            return true;
        }

        return false;
    }

    public function set_applied()
    {
        $t_upgrade_table = config_get('mantis_upgrade_table');

        $query = "INSERT INTO $t_upgrade_table
						(upgrade_id, description)
					  VALUES
						('$this->id', '$this->description')";

        db_query($query);
    }
}

class SQLUpgrade extends Upgrade
{
    public $query;

    public function __construct($p_id, $p_description, $p_query)
    {
        parent::__construct($p_id, $p_description);

        $this->query = $p_query;
    }

    public function execute()
    {
        $result = @db_query($this->query);

        if ($result) {
            $this->set_applied();
        } else {
            $this->error = db_error_msg();
        }

        return $result;
    }

    public function display()
    {
        $t_description = "# Upgrade $this->id: $this->description<br>";

        $t_description .= $this->query . '<br><br>';

        return $t_description;
    }
}

class FunctionUpgrade extends Upgrade
{
    public $function_name;

    public function __construct($p_id, $p_description, $p_function_name)
    {
        parent::__construct($p_id, $p_description);

        $this->function_name = $p_function_name;
    }

    public function execute()
    {
        if (!function_exists($this->function_name)) {
            $this->error = "Function $this->function_name does not exist";

            return false;
        }

        $result = call_user_func($this->function_name);

        if ($result) {
            $this->set_applied();
        } else {
            $this->error = "Function $this->function_name() returned false<br>";

            $t_db_error = db_error_msg();

            if (!is_blank($t_db_error)) {
                $this->error .= 'Last database error (may not be applicable) was: ' . $t_db_error;
            }
        }

        return $result;
    }

    public function display()
    {
        return "# Upgrade $this->id: $this->description<br># Execute function $this->function_name()<br><br>";
    }
}

class UpgradeSet
{
    public $item_array;

    public $upgrade_name;

    public $upgrade_file;

    public function __construct($p_name = 'Mantis Upgrade', $p_filename = 'mantis_upgrade')
    {
        $this->item_array = [];

        $this->upgrade_name = $p_name;

        $this->upgrade_file = $p_filename;
    }

    public function add_item($p_item)
    {
        $this->item_array[] = $p_item;
    }

    public function add_items($p_items)
    {
        foreach ($p_items as $t_item) {
            $this->add_item($t_item);
        }
    }

    public function process_post_data($p_advanced = false)
    {
        $f_execute_all = gpc_get_bool($this->upgrade_file . '_execute_all');

        $f_execute_selected = gpc_get_bool($this->upgrade_file . '_execute_selected');

        $f_print_all = gpc_get_bool($this->upgrade_file . '_print_all');

        $f_print_selected = gpc_get_bool($this->upgrade_file . '_print_selected');

        $f_execute_list = gpc_get_string_array($this->upgrade_file . '_execute_list', []);

        if ($f_execute_all) {
            $this->run(true, null, $p_advanced);
        } elseif ($f_execute_selected && $p_advanced) {
            $this->run(true, $f_execute_list, $p_advanced);
        } elseif ($f_print_all) {
            $this->output();
        } elseif ($f_print_selected && $p_advanced) {
            $this->output($f_execute_list);
        } else {
            $this->run(false, null, $p_advanced);
        }
    }

    public function run($p_execute, $p_limit, $p_advanced)
    {
        if (!php_version_at_least('4.1.0')) {
            global $_SERVER;
        }

        if ($p_execute) {
            # Mark this as a long process and ignore user aborts

            helper_begin_long_process(true);

            # Disable compression so we can stream

            compress_disable();

            # Flush the output buffer

            ob_end_flush();

            echo '<b>Please be patient, this may take a while...</b>';
        }

        # Form

        echo '<form method="POST" action="' . $_SERVER['PHP_SELF'] . '">';

        # Execute All Button

        echo "<input type=\"submit\" name=\"{$this->upgrade_file}_execute_all\" value=\"Execute All\">";

        # Print All Button

        echo "<input type=\"submit\" name=\"{$this->upgrade_file}_print_all\" value=\"Print All\"><br><br>";

        if ($p_advanced) {
            # Execute Selected Button

            echo "<input type=\"submit\" name=\"{$this->upgrade_file}_execute_selected\" value=\"Execute Selected\">";

            # Print Selected Button

            echo "<input type=\"submit\" name=\"{$this->upgrade_file}_print_selected\" value=\"Print Selected\">";
        }

        # Table

        echo '<table width="80%" bgcolor="#222222" border="0" cellpadding="10" cellspacing="1">';

        echo "<tr><td bgcolor=\"#e8e8e8\" colspan=\"3\"><span class=\"title\">$this->upgrade_name</span></td></tr>";

        # Headings

        echo '<tr bgcolor="#ffffff"><th width="70%">Description</th><th nowrap="nowrap">Upgrade ID</th><th width="30%">Status</th></tr>';

        $t_error = false;

        foreach ($this->item_array as $item) {
            $t_state = '';

            if ($item->is_applied()) {
                if (!$p_advanced) {
                    continue; #next one
                }

                $t_state = 'disabled="disabled"';

                $t_color = '#00ff88';

                $t_message = 'Previously Applied';
            } elseif (null !== $p_limit && is_array($p_limit)
                      && !in_array($item->id, $p_limit, true)) {
                $t_color = '#ffff88';

                $t_message = 'Skipped';
            } elseif ($p_execute) {
                if ($t_error) {
                    $t_state = 'checked';

                    $t_color = '#ff0088';

                    $t_message = 'Skipped due to previous error';

                    continue;  # next one
                }

                if ($item->execute()) {
                    $t_state = 'disabled="disabled"';

                    $t_color = '#00ff88';

                    $t_message = 'Applied';
                } else {
                    $t_state = 'checked';

                    $t_color = '#ff0088';

                    $t_message = 'ERROR: ' . $item->error;

                    $t_error = true;
                }
            } else {  # not applied but not executing
                $t_color = '#ff0088';

                $t_message = 'Not Applied';

                $t_state = 'checked';
            }

            echo '<tr bgcolor="#ffffff"><td>';

            echo $item->description; # description

            echo '</td>';

            echo '<td nowrap="nowrap">';

            if ($p_advanced) {
                echo "<input type=\"checkbox\" name=\"{$this->upgrade_file}_execute_list[]\" value=\"$item->id\" $t_state> ";
            }

            echo "$item->id</td>";

            echo "<td bgcolor=\"$t_color\">$t_message</td>";

            echo '</tr>';
        }

        echo '</table>';

        # Execute All Button

        echo "<br><input type=\"submit\" name=\"{$this->upgrade_file}_execute_all\" value=\"Execute All\">";

        # Print All Button

        echo "<input type=\"submit\" name=\"{$this->upgrade_file}_print_all\" value=\"Print All\">";

        if ($p_advanced) {
            # Execute Selected Button

            echo "<input type=\"submit\" name=\"{$this->upgrade_file}_execute_selected\" value=\"Execute Selected\">";

            # Print Selected Button

            echo "<input type=\"submit\" name=\"{$this->upgrade_file}_print_selected\" value=\"Print Selected\">";
        }
    }

    public function output($p_limit = null)
    {
        # @@@ The generated file is in UNIX format, should it be in Windows format?

        $t_filename = $this->upgrade_file . '.sql';

        header("Content-Type: text/plain; name=$t_filename");

        #header( 'Content-Transfer-Encoding: BASE64;' );

        #header( "Content-Disposition: attachment; filename=$t_filename" );

        $t_upgrade_table = config_get('mantis_upgrade_table');

        foreach ($this->item_array as $item) {
            if ($item->is_applied()  #already applied or...
                || (null !== $p_limit && is_array($p_limit)  #limit list included
                    && !in_array($item->id, $p_limit, true))) {  #and not in limit list
                continue; #then skip to the next one
            }

            echo $item->display();
        }
    }
}

?>
