<?php
/**
 * Main plugin bootstrap file for LCIS Systems.
 *
 * Handles activation (DB setup), admin menus, AJAX callbacks, asset enqueuing,
 * user meta-box registration, and core page callbacks.
 *
 * @package lcsi_systems
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'LCSIS_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
define( 'LCSIS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

////////////////////////////////////////////////////////////////////////////////
// Section: Activation & Database Schema
////////////////////////////////////////////////////////////////////////////////

register_activation_hook( __FILE__, 'lcsi_systems_activation' );
/**
 * Activation callback. Creates necessary database tables for LCIS.
 */
function lcsi_systems_activation(): void {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $tables = [
        $wpdb->prefix . 'lcsi_companies'          => "
            company_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            company_name VARCHAR(255) NOT NULL,
            company_address TEXT NULL,
            company_phone VARCHAR(50) NULL,
            company_email VARCHAR(100) NULL,
            owner_user_id BIGINT UNSIGNED NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (company_id),
            KEY owner_idx (owner_user_id)
        ",
        $wpdb->prefix . 'lcsi_company_user_roles' => "
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            company_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            role VARCHAR(50) NOT NULL,
            PRIMARY KEY (id),
            KEY comp_idx (company_id),
            KEY user_idx (user_id)
        ",
        $wpdb->prefix . 'lcsi_user_roles'         => "
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT UNSIGNED NOT NULL,
            role VARCHAR(50) NOT NULL,
            PRIMARY KEY (id),
            KEY user_idx (user_id)
        ",
    ];

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    foreach ( $tables as $name => $schema ) {
        dbDelta( sprintf(
            "CREATE TABLE %s (%s) %s;",
            esc_sql( $name ),
            $schema,
            $charset_collate
        ) );
    }
}

////////////////////////////////////////////////////////////////////////////////
// Section: AJAX Handlers
////////////////////////////////////////////////////////////////////////////////

add_action( 'wp_ajax_lcsi_get_timezone', 'lcsi_get_timezone_handler' );
/**
 * AJAX handler: retrieve timezone by US ZIP via Zippopotam and TimeZoneDB.
 */
function lcsi_get_timezone_handler(): void {
    $zip = sanitize_text_field( $_REQUEST['zip'] ?? '' );
    if ( ! $zip ) {
        wp_send_json_error( 'ZIP missing.' );
    }

    $resp = wp_remote_get( "http://api.zippopotam.us/us/{$zip}" );
    if ( is_wp_error( $resp ) ) {
        wp_send_json_error( 'Lookup failed.' );
    }
    $data = json_decode( wp_remote_retrieve_body( $resp ), true );
    if ( empty( $data['places'][0] ) ) {
        wp_send_json_error( 'Invalid ZIP.' );
    }

    $lat = $data['places'][0]['latitude'];
    $lon = $data['places'][0]['longitude'];
    $tz_resp = wp_remote_get( "http://api.timezonedb.com/v2.1/get-time-zone?key=YOUR_KEY&format=json&by=position&lat={$lat}&lng={$lon}" );
    if ( is_wp_error( $tz_resp ) ) {
        wp_send_json_error( 'Timezone lookup failed.' );
    }
    $tz_data = json_decode( wp_remote_retrieve_body( $tz_resp ), true );
    wp_send_json_success( $tz_data['zoneName'] ?? 'UTC' );
}

////////////////////////////////////////////////////////////////////////////////
// Section: Admin Menus & Pages
////////////////////////////////////////////////////////////////////////////////

add_action( 'admin_menu', 'lcsi_admin_menu' );
/**
 * Registers top-level LCIS Systems menu and submenus.
 */
function lcsi_admin_menu(): void {
    add_menu_page(
        'LCIS Systems',
        'LCIS Systems',
        'manage_options',
        'lcis-systems',
        'lcsi_page_main',
        'dashicons-admin-generic',
        60
    );

    $subpages = [ 'settings', 'appointments', 'company', 'invoices', 'reports', 'roles', 'users' ];
    foreach ( $subpages as $page ) {
        add_submenu_page(
            'lcis-systems',
            ucfirst( $page ),
            ucfirst( $page ),
            'manage_options',
            "lcis-systems-{$page}",
            "lcsi_page_{$page}"
        );
    }
    remove_submenu_page( 'lcis-systems', 'lcis-systems' );
}

////////////////////////////////////////////////////////////////////////////////
// Section: Asset Enqueuing
////////////////////////////////////////////////////////////////////////////////

add_action( 'admin_enqueue_scripts', 'lcsi_enqueue_assets' );
/**
 * Enqueues admin page-specific CSS files.
 *
 * @param string $hook The current admin page hook.
 */
function lcsi_enqueue_assets( string $hook ): void {
    if ( strpos( $hook, 'lcis-systems') === false ) {
        return;
    }
    wp_enqueue_style(
        'lcsi-settings-css',
        LCSIS_PLUGIN_URL . 'assets/css/settings.css',
        [],
        filemtime( LCSIS_PLUGIN_DIR . 'assets/css/settings.css' )
    );
    if ( isset( $_GET['page'] ) && $_GET['page'] === 'lcis-systems-roles' ) {
        wp_enqueue_style(
            'lcsi-roles-css',
            LCSIS_PLUGIN_URL . 'assets/css/roles.css',
            [],
            filemtime( LCSIS_PLUGIN_DIR . 'assets/css/roles.css' )
        );
    }
    if ( isset( $_GET['page'] ) && $_GET['page'] === 'lcis-systems-users' ) {
        wp_enqueue_style(
            'lcsi-forms-css',
            LCSIS_PLUGIN_URL . 'assets/css/forms.css',
            [],
            filemtime( LCSIS_PLUGIN_DIR . 'assets/css/forms.css' )
        );
    }
}

////////////////////////////////////////////////////////////////////////////////
// Section: User Role Meta-box
////////////////////////////////////////////////////////////////////////////////

add_action( 'show_user_profile', 'lcsi_user_role_meta' );
add_action( 'edit_user_profile', 'lcsi_user_role_meta' );
/**
 * Displays the LCSI secondary role selector on user profile pages.
 *
 * @param WP_User $user The current user object.
 */
function lcsi_user_role_meta( WP_User $user ): void {
    ?>
    <h2>LCSI Secondary Role</h2>
    <table class="form-table">
        <tr>
            <th><label for="lcsi_role">Role</label></th>
            <td>
                <select name="lcsi_role" id="lcsi_role">
                    <?php
                    $roles = ['Owner','Inspector','Technician','Surveyor'];
                    $current = get_user_meta( $user->ID, 'lcsi_role', true );
                    foreach ( $roles as $r ) {
                        printf('<option value="%1$s"%2$s>%1$s</option>',
                            esc_attr($r),
                            selected($current, $r, false)
                        );
                    }
                    ?>
                </select>
                <p class="description">Secondary role only; primary WP role unchanged.</p>
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'personal_options_update', 'lcsi_save_user_role' );
add_action( 'edit_user_profile_update', 'lcsi_save_user_role' );
/**
 * Saves the selected LCSI secondary role for a user.
 *
 * @param int $user_id The user ID being saved.
 */
function lcsi_save_user_role( int $user_id ): void {
    if ( ! current_user_can( 'edit_user', $user_id ) ) {
        return;
    }
    update_user_meta( $user_id, 'lcsi_role', sanitize_text_field( $_POST['lcsi_role'] ?? '' ) );
}

////////////////////////////////////////////////////////////////////////////////
// Section: Page Callbacks
////////////////////////////////////////////////////////////////////////////////

/**
 * Main dashboard page callback.
 */
function lcsi_page_main(): void {
    echo '<div class="wrap"><h1>LCIS Systems Dashboard</h1></div>';
}

/**
 * Settings page callback.
 */
function lcsi_page_settings(): void {
    require_once LCSIS_PLUGIN_DIR . 'includes/admin/settings/settings.php';
    lcis_render_settings_page();
}

/**
 * Appointments page callback.
 */
function lcsi_page_appointments(): void {
    echo '<div class="wrap"><p>Appointments coming soon.</p></div>';
}

/**
 * Company page callback.
 */
function lcsi_page_company(): void {
    require_once LCSIS_PLUGIN_DIR . 'includes/admin/company/render.php';
}

/**
 * Invoices page callback.
 */
function lcsi_page_invoices(): void {
    echo '<div class="wrap"><p>Invoices coming soon.</p></div>';
}

/**
 * Reports page callback.
 */
function lcsi_page_reports(): void {
    echo '<div class="wrap"><p>Reports coming soon.</p></div>';
}

/**
 * Roles page callback.
 */
function lcsi_page_roles(): void {
    require_once LCSIS_PLUGIN_DIR . 'includes/admin/roles/roles.php';
}

/**
 * Users page callback.
 */
function lcsi_page_users(): void {
    // Determine if editing a specific user or listing users
    if ( isset( $_GET['edit_user'] ) ) {
        require_once LCSIS_PLUGIN_DIR . 'includes/admin/users/profile.php';
    } else {
        require_once LCSIS_PLUGIN_DIR . 'includes/admin/users/users.php';
    }
}
