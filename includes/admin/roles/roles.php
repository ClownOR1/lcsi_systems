<?php defined('ABSPATH') || exit; ?>
<div class="wrap">
    <div class="lcsi-settings-intro">
        <div class="lcsi-settings-intro">
        <div class="logo-column"><img src="<?php echo esc_url(LCSIS_PLUGIN_URL.'assets/images/lcsilogo.png');?>" height="100"></div>
        <div class="text-column"><p><strong>Left Coast Survey & Inspection Systems LCSI Role Management.</strong></p></div>
      </div>    	
    </div>
    <div class="lcsi-roles-container">
<?php
if (!empty($_GET['message'])) {
    $msg = sanitize_text_field($_GET['message']);
    if ($msg === 'role_created') {
        echo '<div class="notice notice-success is-dismissible"><p>New role created.</p></div>';
    } elseif ($msg === 'role_updated') {
        echo '<div class="notice notice-info is-dismissible"><p>Role updated.</p></div>';
    } elseif ($msg === 'role_deleted') {
        echo '<div class="notice notice-error is-dismissible"><p>Role deleted.</p></div>';
    }
}
?>
        <h1>Default Roles (Non-Editable)</h1>
        <table class="lcsi-roles-table widefat fixed striped">
            <thead>
                <tr><th>Role</th><th>Hierarchy Position</th><th>User Count</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php
            global $wpdb;
            $default_roles = array(
                'Owner' => 1,
                'Partner' => 2,
                'Operator' => 3,
                'Surveyor' => 4,
                'Technician' => 4,
                'Inspector' => 4,
            );
            foreach ($default_roles as $role => $position) {
                $count = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s",
                    'lcsi_role', $role
                ));
                echo '<tr>';
                echo '<td>' . esc_html($role) . '</td>';
                echo '<td>' . intval($position) . '</td>';
                echo '<td>' . intval($count) . '</td>';
                echo '<td><span class="lcsi-default-role">Default</span></td>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
        <h1>Custom Roles</h1>
        <table class="lcsi-roles-table widefat fixed striped">
            <thead>
                <tr><th>Role</th><th>Hierarchy Position</th><th>User Count</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php
            $stored_roles = get_option('lcsi_roles_hierarchy', array());
            // Filter out default roles from custom list
            $default_roles_keys = array_keys($default_roles);
            $stored_roles = array_diff_key($stored_roles, array_flip($default_roles_keys));

            foreach ($stored_roles as $role => $position) {
                $count = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value = %s",
                    'lcsi_role', $role
                ));
                $edit_url = esc_url(add_query_arg(
                    array('page'=>'lcis-systems-roles','edit_role'=>$role),
                    admin_url('admin.php')
                ));
                $delete_url = esc_url(wp_nonce_url(
                    add_query_arg(array(
                        'action' => 'lcsi_delete_role',
                        'role_name' => $role,
                    ), admin_url('admin-post.php')),
                    'lcsi_delete_role'
                ));
                echo '<tr>';
                echo '<td>' . esc_html($role) . '</td>';
                echo '<td>' . intval($position) . '</td>';
                echo '<td>' . intval($count) . '</td>';
                echo '<td><a class="edit-role-link" href="'.$edit_url.'">Edit</a> | ';
                echo '<a class="delete-role-link" href="'.$delete_url.'" onclick="return confirm(&quot;Are you sure you want to delete this role?&quot;)">Delete</a></td>';
                echo '</tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($_GET['edit_role']) && isset($stored_roles[$_GET['edit_role']])): 
        $edit = sanitize_text_field($_GET['edit_role']);
        $edit_pos = $stored_roles[$edit];
    ?>
    <div class="lcsi-roles-form">
        <h2>Edit Role: <?php echo esc_html($edit); ?></h2>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('lcsi_edit_role'); ?>
            <table class="form-table">
                <tr><th><label for="role_name">Role Name</label></th>
                    <td><input name="role_name" type="text" id="role_name" value="<?php echo esc_attr($edit); ?>" required /></td></tr>
                <tr><th><label for="role_position">Hierarchy Position</label></th>
                    <td><input name="role_position" type="number" id="role_position" min="1" value="<?php echo intval($edit_pos); ?>" required /></td></tr>
            </table>
            <input type="hidden" name="orig_role_name" value="<?php echo esc_attr($edit); ?>">
            <input type="hidden" name="action" value="lcsi_edit_role">
            <?php submit_button('Update Role'); ?>
            <a class="button" href="<?php echo esc_url(admin_url('admin.php?page=lcis-systems-roles')); ?>">Cancel</a>
        </form>
    </div>
    <?php else: ?>
    <div class="lcsi-roles-form">
        <h2>Add New Role</h2>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('lcsi_add_role'); ?>
            <table class="form-table">
                <tr><th><label for="role_name">Role Name</label></th>
                    <td><input name="role_name" type="text" id="role_name" required /></td></tr>
                <tr><th><label for="role_position">Hierarchy Position</label></th>
                    <td><input name="role_position" type="number" id="role_position" min="1" required /></td></tr>
            </table>
            <input type="hidden" name="action" value="lcsi_add_role">
            <?php submit_button('Add Role'); ?>
        </form>
    </div>
    <?php endif; ?>
</div>
