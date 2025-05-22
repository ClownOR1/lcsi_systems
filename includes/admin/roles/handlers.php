<?php
defined('ABSPATH') || exit;

function lcsi_handle_add_role() {
    if (!current_user_can('manage_options') || !check_admin_referer('lcsi_add_role')) {
        wp_die('Unauthorized');
    }
    $role_name = sanitize_text_field($_POST['role_name']);
    $position = intval($_POST['role_position']);
    $roles = get_option('lcsi_roles_hierarchy', array());
    $roles[$role_name] = $position;
    update_option('lcsi_roles_hierarchy', $roles);
    wp_redirect(admin_url('admin.php?page=lcis-systems-roles&message=role_created'));
    exit;
}
add_action('admin_post_lcsi_add_role', 'lcsi_handle_add_role');

function lcsi_handle_edit_role() {
    if (!current_user_can('manage_options') || !check_admin_referer('lcsi_edit_role')) {
        wp_die('Unauthorized');
    }
    global $wpdb;
    $orig = sanitize_text_field($_POST['orig_role_name']);
    $new = sanitize_text_field($_POST['role_name']);
    $position = intval($_POST['role_position']);
    $defaults = array('Owner','Partner','Operator','Surveyor','Technician','Inspector');
    if (in_array($orig, $defaults)) {
        wp_die('Cannot edit default role.');
    }
    $roles = get_option('lcsi_roles_hierarchy', array());
    if ($new !== $orig) {
        if (isset($roles[$orig])) {
            unset($roles[$orig]);
        }
    }
    $roles[$new] = $position;
    update_option('lcsi_roles_hierarchy', $roles);
    $wpdb->update(
        $wpdb->usermeta,
        array('meta_value' => $new),
        array('meta_key' => 'lcsi_role', 'meta_value' => $orig)
    );
    wp_redirect(admin_url('admin.php?page=lcis-systems-roles&message=role_updated'));
    exit;
}
add_action('admin_post_lcsi_edit_role', 'lcsi_handle_edit_role');

function lcsi_handle_delete_role() {
    if (!current_user_can('manage_options') || !check_admin_referer('lcsi_delete_role')) {
        wp_die(__('Unauthorized', 'lcsi_systems'), '', array('back_link' => true));
    }
    $role = sanitize_text_field($_GET['role_name']);
    $defaults = array('Owner','Partner','Operator','Surveyor','Technician','Inspector');
    if (in_array($role, $defaults)) {
        wp_die(__('Cannot delete default role.', 'lcsi_systems'), '', array('back_link' => true));
    }
    $roles = get_option('lcsi_roles_hierarchy', array());
    if (isset($roles[$role])) {
        unset($roles[$role]);
        update_option('lcsi_roles_hierarchy', $roles);
    }
    global $wpdb;
    $wpdb->delete(
        $wpdb->usermeta,
        array('meta_key' => 'lcsi_role', 'meta_value' => $role)
    );
    wp_redirect(admin_url('admin.php?page=lcis-systems-roles&message=role_deleted'));
    exit;
}
add_action('admin_post_lcsi_delete_role', 'lcsi_handle_delete_role');
