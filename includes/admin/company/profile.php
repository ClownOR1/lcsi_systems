<?php
/**
 * Profile Page Template for LCIS Systems
 *
 * This file is included by the Users admin page callback (lcis_systems_page_users).
 * It handles saving user certifications/accreditations and renders the tabbed
 * interface showing:
 *   - User Details
 *   - Certifications
 *   - Accreditations
 *
 * Requirements:
 *  - Must be included within WP admin context.
 *  - No direct access.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Process form submission: save certifications and accreditations.
 *
 * @param int $user_id ID of the user being edited.
 * @return void
 */
function lcsi_process_user_profile_save( int $user_id ): void {
    if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
        return;
    }
    $nonce = $_POST['_wpnonce'] ?? '';
    if ( ! wp_verify_nonce( $nonce, 'lcsi_save_user_profile_' . $user_id ) ) {
        return;
    }
    if ( isset( $_POST['lcsi_user_certs'] ) ) {
        $certs = array_filter( array_map( 'trim', explode( "\n", wp_unslash( $_POST['lcsi_user_certs'] ) ) ) );
        update_user_meta( $user_id, 'lcsi_user_certs', wp_slash( wp_json_encode( $certs ) ) );
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>Certifications saved.</p></div>';
        } );
    }
    if ( isset( $_POST['lcsi_user_accs'] ) ) {
        $accs = array_filter( array_map( 'trim', explode( "\n", wp_unslash( $_POST['lcsi_user_accs'] ) ) ) );
        update_user_meta( $user_id, 'lcsi_user_accs', wp_slash( wp_json_encode( $accs ) ) );
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>Accreditations saved.</p></div>';
        } );
    }
}

/**
 * Renders the LCIS user profile page with tabs.
 *
 * @param int $user_id ID of the user being viewed.
 * @return void
 */
function render_lcsi_user_profile_page( int $user_id ): void {
    lcsi_process_user_profile_save( $user_id );

    $user = get_userdata( $user_id );
    if ( ! $user ) {
        echo '<div class="notice notice-error"><p>Invalid user.</p></div>';
        return;
    }

    $existing_certs = json_decode( get_user_meta( $user_id, 'lcsi_user_certs', true ), true ) ?: [];
    $existing_accs  = json_decode( get_user_meta( $user_id, 'lcsi_user_accs',  true ), true ) ?: [];
    $nonce_field    = wp_nonce_field( 'lcsi_save_user_profile_' . $user_id, '_wpnonce', true, false );

    echo <<<HTML
<div class="wrap lcsi-user-profile">
    <h1>{$user->display_name}'s LCIS Profile</h1>
    <h2 class="nav-tab-wrapper">
        <a href="#details" class="nav-tab nav-tab-active">Details</a>
        <a href="#certifications" class="nav-tab">Certifications</a>
        <a href="#accreditations" class="nav-tab">Accreditations</a>
    </h2>
    <form method="post">
        {$nonce_field}
        <div id="details" class="lcsi-tab-content">
            <table class="form-table">
                <tr><th scope="row">Name</th><td>{$user->display_name}</td></tr>
                <tr><th scope="row">Email</th><td>{$user->user_email}</td></tr>
            </table>
        </div>
        <div id="certifications" class="lcsi-tab-content" style="display:none;">
            <h3>Certifications</h3>
            <textarea name="lcsi_user_certs" rows="5" style="width:100%;">{implode("\n", array_map("esc_textarea", $existing_certs))}</textarea>
            <p class="description">One per line.</p>
        </div>
        <div id="accreditations" class="lcsi-tab-content" style="display:none;">
            <h3>Accreditations</h3>
            <textarea name="lcsi_user_accs" rows="5" style="width:100%;">{implode("\n", array_map("esc_textarea", $existing_accs))}</textarea>
            <p class="description">One per line.</p>
        </div>
        <p class="submit"><button type="submit" class="button button-primary">Save Changes</button></p>
    </form>
</div>
<script>
jQuery(function($){
    $('.nav-tab').on('click', function(e){
        e.preventDefault();
        $('.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.lcsi-tab-content').hide();
        $($(this).attr('href')).show();
    });
});
</script>
HTML;
}

$user_id = isset($_GET['edit_user']) ? intval($_GET['edit_user']) : 0;
render_lcsi_user_profile_page($user_id);
