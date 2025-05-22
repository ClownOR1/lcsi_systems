<?php
// form.php - User Creation Form
if (!defined('ABSPATH')) exit; ?>
<div class="wrap lcsi-form-wrap">
    <h2>Add New User</h2>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="lcsi_add_user">
        <?php wp_nonce_field('lcsi_add_user_action', 'lcsi_add_user_nonce'); ?>
        <table class="form-table">
            <tr>
                <th><label for="user_first_name">First Name</label></th>
                <td><input name="user_first_name" type="text" id="user_first_name" required></td>
            </tr>
            <tr>
                <th><label for="user_last_name">Last Name</label></th>
                <td><input name="user_last_name" type="text" id="user_last_name" required></td>
            </tr>
            <tr>
                <th><label for="user_email">Email</label></th>
                <td><input name="user_email" type="email" id="user_email" required></td>
            </tr>
            <tr>
                <th><label for="user_password">Password</label></th>
                <td><input name="user_password" type="password" id="user_password" required></td>
            </tr>
            <tr>
                <th><label for="user_role">Role</label></th>
                <td>
                    <select name="user_role" id="user_role">
                        <?php wp_dropdown_roles(); ?>
                    </select>
                </td>
            </tr>
        </table>
        <?php submit_button('Add User'); ?>
    </form>
</div>