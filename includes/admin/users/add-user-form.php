<?php
defined('ABSPATH') || exit;

// Determine context
$company_id = isset($company_id) ? intval($company_id) : 0;

// Handle form submission
if (isset($_POST['add_new_user']) && check_admin_referer('add_new_user_form', 'add_new_user_nonce')) {
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name  = sanitize_text_field($_POST['last_name']);
    $email      = sanitize_email($_POST['email']);
    $password   = $_POST['password'];
    $role       = sanitize_text_field($_POST['role']);

    if (!email_exists($email)) {
        $username = sanitize_user($first_name . '.' . $last_name);
        $user_id  = wp_create_user($username, $password, $email);
        if (!is_wp_error($user_id)) {
            wp_update_user([
                'ID'         => $user_id,
                'first_name' => $first_name,
                'last_name'  => $last_name,
            ]);
            // Assign LCSI role
            global $wpdb;
            if ($company_id) {
                // company-specific role
                $role_table = $wpdb->prefix . 'lcsi_company_user_roles';
                $wpdb->insert($role_table, [
                    'company_id' => $company_id,
                    'user_id'    => $user_id,
                    'role'       => $role,
                ]);
                $redirect_url = add_query_arg([
                    'page'         => 'lcis-systems-company',
                    'edit_company' => $company_id,
                ], admin_url('admin.php'));
            } else {
                // global user role
                $ur_table = $wpdb->prefix . 'lcsi_user_roles';
                $wpdb->insert($ur_table, [
                    'user_id' => $user_id,
                    'role'    => $role,
                ]);
                $redirect_url = add_query_arg([
                    'page'      => 'lcis-systems-users',
                    'edit_user' => $user_id,
                ], admin_url('admin.php'));
            }
            wp_redirect($redirect_url);
            exit;
        } else {
            echo '<div class="error"><p>' . esc_html($user_id->get_error_message()) . '</p></div>';
        }
    } else {
        echo '<div class="error"><p>Email already exists.</p></div>';
    }
}

// Display form
?>
<h2>Add New User</h2>
<form method="post" action="">
    <?php wp_nonce_field('add_new_user_form', 'add_new_user_nonce'); ?>
    <table class="form-table">
        <tr>
            <th scope="row"><label for="first_name">First Name</label></th>
            <td><input name="first_name" type="text" id="first_name" class="regular-text" required></td>
        </tr>
        <tr>
            <th scope="row"><label for="last_name">Last Name</label></th>
            <td><input name="last_name" type="text" id="last_name" class="regular-text" required></td>
        </tr>
        <tr>
            <th scope="row"><label for="email">Email</label></th>
            <td><input name="email" type="email" id="email" class="regular-text" required></td>
        </tr>
        <tr>
            <th scope="row"><label for="password">Password</label></th>
            <td><input name="password" type="password" id="password" class="regular-text" required></td>
        </tr>
        <tr>
            <th scope="row"><label for="role">Role</label></th>
            <td>
                <select name="role" id="role">
                    <?php
                    $roles = ['Owner', 'Inspector', 'Technician', 'Surveyor'];
                    foreach ($roles as $r) {
                        echo '<option value="' . esc_attr($r) . '">' . esc_html($r) . '</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <?php if ($company_id): ?>
            <input type="hidden" name="company_id" value="<?php echo esc_attr($company_id); ?>">
        <?php endif; ?>
    </table>
    <?php submit_button('Add User', 'primary', 'add_new_user'); ?>
</form>
