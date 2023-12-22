<?php
/*
  Plugin Name: Multiple Roles Editor
  Plugin URI: https://github.com/13pixlar/gravity-forms-sticky-form
  Description: Bulk add/remove multiple roles to/from users
  Author: Adam Rehal
  Version: 1.0
  Author URI: http://13pixlar.se
 */

add_action( 'plugins_loaded', 'mre_load_textdomain' );
function mre_load_textdomain() {
    load_plugin_textdomain( 'mre', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}

add_action('admin_footer','edit_multiple_roles');
function edit_multiple_roles() {
    global $wpdb;
    global $pagenow;

    if($pagenow == "users.php"){
        ?>
        <style type="text/css">
            body.users-php .tablenav .actions #changeit {
                margin-right: 0;
            }
            body.users-php .tablenav .actions #remove-role-button,
            body.users-php .tablenav .actions #remove-existing-role-button {
                margin-right: 10px;
            }
        </style>
        <script type="text/javascript">
        jQuery(document).ready(function(){

            $ = jQuery;

            var role_buttons_html = "<?php render_role_buttons(); ?>";
            var add_remove_role_buttons_html = "<?php render_add_remove_role_buttons(); ?>";

            $("select#new_role option:nth(0)").html('<?php _e("Select role", "mre") ?>');

            $('.ure_roles, #ure_roles, .column-ure_roles').remove();

            // Add new buttons
            $("#changeit").after(role_buttons_html).css('margin-right', '0');

            // Add new option to select
            $("select#new_role option:last-child").after('<option value="add_remove_role"><?php _e("Add/remove role", "mre") ?></option>');

            // Show hide the "Add/remove new role" buttons
            $("#new_role").on('change', function(event) {
                if ($(this).val() === "add_remove_role") {
                    $("#add-role-button").before(add_remove_role_buttons_html);
                    $("#add-role-button, #remove-role-button").css('display', 'none');
                }else{
                    $("#add-role-button, #remove-role-button").css('display', 'inline-block');
                    $("#add-new-role-input, #add-new-role-button, #remove-existing-role-button").remove();
                }
            });

        });
        </script>
        <?php
    }

    // Add role to users
    if (isset($_GET["addtouser"]) && $_GET["new_role"] != "" && isset($_GET["users"])) {

        $users = $_GET["users"];
        $role = $_GET["new_role"];

        foreach ($users as $user) {
            $u = new WP_User($user);
            $u->add_role($role);
        }
        echo '<div id="message" class="updated notice is-dismissible"><p>' . __('Added role to users', 'mre') . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.') . '</span></button></div>';
    }

    // Remove role from users
    if (isset($_GET["removefromuser"]) && $_GET["new_role"] != "" && isset($_GET["users"])) {

        $users = $_GET["users"];
        $role = $_GET["new_role"];
        foreach ($users as $user) {
            $u = new WP_User($user);
            if($role == "administrator") {
                echo '<div id="message" class="error notice is-dismissible"><p>' . __('You can not remove administrator roles', 'mre') . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.') . '</span></button></div>';
            }else{
                $u->remove_role($role);
                $i = 1;
            }
        }
        if ($i > 0 ) echo '<div id="message" class="updated notice is-dismissible"><p>' . __('Removed role from users', 'mre') . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.') . '</span></button></div>';
    }

    // Add new role
    if (isset($_GET["adnew"]) && $_GET["new_role"] == "add_remove_role" && $_GET["addremoveinput"] != "") {

        $newrole_slug =  str_replace(" ", "_", strtolower($_GET["addremoveinput"]));
        $newrole_name = $_GET["addremoveinput"];

        add_role($newrole_slug,$newrole_name);

        echo '<div id="message" class="updated notice is-dismissible"><p>' . __('Added new role', 'mre') . ' ' . $newrole_name . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.') . '</span></button></div>';
    }

    // Remove exsisting role
    if (isset($_GET["removeexisting"]) && $_GET["new_role"] == "add_remove_role" && $_GET["addremoveinput"] != "") {

        $removerole_slug =  str_replace(" ", "_", strtolower($_GET["addremoveinput"]));
        remove_role($removerole_slug);

        echo '<div id="message" class="updated notice is-dismissible"><p>' . __('Removed existing role', 'mre') . '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">' . __('Dismiss this notice.') . '</span></button></div>';
    }
}

function render_role_buttons() {

    print( " <input id='add-role-button' type='submit' class='button' name='addtouser' value='" . __("Add"). "' style='margin-top: 1px;' /> " );
    print( "<input id='remove-role-button' type='submit' class='button' name='removefromuser' value='" . __("Remove"). "' style='margin-top: 1px;' />" );
}

function render_add_remove_role_buttons() {

    print( "<input id='add-new-role-input' name='addremoveinput' type='text' value=''> " );
    print( "<input id='add-new-role-button' type='submit' class='button' name='adnew' value='" . __('Add new role', 'mre') . "' style='margin-top: 1px;' /> " );
    print( "<input id='remove-existing-role-button' type='submit' class='button' name='removeexisting' value='" . __('Remove exisiting role', 'mre') . "' style='margin-top: 1px;' />" );
}

/*// Show extra column i user table
add_filter('manage_users_columns' , 'add_other_roles_to_user_column');
function add_other_roles_to_user_column($columns) {
    return array_merge( $columns,
              array('other_roles' => __('Other roles', 'mre')) );
}

// Populate extra column i user table
add_filter( 'manage_users_custom_column', 'show_all_other_roles', 10, 3);
function show_all_other_roles($return = '', $column_name = '', $id = 0) {

    global $wp_roles;
    $user = get_userdata($id);
    $user_roles = $user->roles;
    $all_roles = "";
    $i = 0;
    foreach ($user_roles as $role ) {
        if($i > 0) $all_roles .= $wp_roles->roles[$role]['name'] . ", ";
        $i++;
    }

    $return = rtrim($all_roles, ", ");
    return $return;
} */
