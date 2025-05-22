<?php
function lcsi_roles_page_content() {
    global $wpdb;
    $roles = array('Owner','Partner','Operator','Surveyor','Technician','Inspector');
    echo '<div class="wrap lcsi-roles-container"><h1>LCSI Roles</h1>';
    echo '<table class="lcsi-roles-table widefat fixed striped"><thead><tr><th>Role</th><th>User Count</th></tr></thead><tbody>';
    foreach ($roles as $role) {
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE meta_key=%s AND meta_value=%s",
            'lcsi_role', $role
        ));
        echo '<tr><td>'.esc_html($role).'</td><td>'.intval($count).'</td></tr>';
    }
    echo '</tbody></table></div>';
}
?>