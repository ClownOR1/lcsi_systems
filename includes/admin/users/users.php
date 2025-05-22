<?php
if ( isset($_GET['edit_user']) ) {
    include_once __DIR__ . '/profile.php';
    return;
}
?>
<?php
defined('ABSPATH') || exit;
$current_page = isset($_GET['page']) ? esc_attr($_GET['page']) : 'lcsi-systems-users';
if (!empty($_GET['edit_user'])) {
    include __DIR__ . '/profile.php';
    return;
}
if (isset($_GET['edit_user'])) {
    include __DIR__ . '/profile.php';
    return;
}
?>
<?php
if ( isset($_GET['edit_user']) ) {
    include_once __DIR__ . '/profile.php';
    return;
}
?><?php defined('ABSPATH') || exit; ?>
<div class="wrap">
    <div class="lcsi-settings-intro">
        <div class="logo-column">
            <img src="<?php echo esc_url(LCSIS_PLUGIN_URL . 'assets/images/lcsilogo.png'); ?>" height="100">
        </div>
        <div class="text-column">
            <p><strong>Left Coast Survey & Inspection Systems – Users, Partners, Inspectors, Surveyors.</strong></p>
        </div>
    </div>

    <h1>LCSI Users</h1>

    

    <table class="lcsi-roles-table widefat fixed striped">
        <thead>
            <tr><th>Name</th><th>Email</th><th>Phone</th><th>Company</th><th>Role</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php
        $users = get_users(array(
            'meta_key' => 'lcsi_role',
            'meta_compare' => 'EXISTS'
        ));

        foreach ($users as $user) {
            $name = $user->display_name;
            $email = $user->user_email;
            $phone = get_user_meta($user->ID, 'lcsi_phone', true);
            $company = get_user_meta($user->ID, 'lcsi_company', true);
            $role = get_user_meta($user->ID, 'lcsi_role', true);

            // Apply filters
            if (!empty($_GET['filter_name']) && stripos($name, $_GET['filter_name']) === false) continue;
            if (!empty($_GET['filter_email']) && stripos($email, $_GET['filter_email']) === false) continue;
            if (!empty($_GET['filter_phone']) && stripos($phone, $_GET['filter_phone']) === false) continue;
            if (!empty($_GET['filter_company']) && stripos($company, $_GET['filter_company']) === false) continue;
            if (!empty($_GET['filter_role']) && stripos($role, $_GET['filter_role']) === false) continue;

            $edit_url = esc_url(add_query_arg(
                array('page'=>'lcis-systems-users','edit_user' => $user->ID),
                admin_url('admin.php')
            ));

            
            echo '<tr>';
            echo '<td><a href="' . $edit_url . '">' . esc_html($name) . '</a></td>';
            echo '<td>' . esc_html($email) . '</td>';
            echo '<td>' . esc_html($phone) . '</td>';
            echo '<td>' . esc_html($company ?: '-') . '</td>';
            echo '<td>' . esc_html($role) . '</td>';
            echo '<td><a href="' . $edit_url . '">Edit</a></td>';
            echo '</tr>';

        }
        ?>
        </tbody>
    </table>

<?php if (!isset($_GET['edit_user'])): ?>
    <h2>Add New User</h2>
    <?php include_once 'form.php'; ?>
<?php endif; ?>
</div>
