<?php
defined('ABSPATH') || exit;

// If we’re editing a company, load the profile screen and stop
if ( isset( $_GET['edit_company'] ) ) {
    include __DIR__ . '/profile.php';
    return;
}
?>

<div class="wrap">
    <div class="lcsi-settings-intro">
        <div class="logo-column">
            <img src="<?php echo esc_url(LCSIS_PLUGIN_URL . 'assets/images/lcsilogo.png'); ?>" height="100">
        </div>
        <div class="text-column">
            <h2><strong>Left Coast Survey & Inspection Systems – Affiliated Companies & Partners.</strong></h2>
        </div>
    </div>
    <h1>Companies</h1>
    <table class="lcsi-roles-table widefat fixed striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Owner</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ( $companies ): ?>
                <?php foreach ( $companies as $company ): 
                    $owner = get_userdata( $company['owner_user_id'] ); ?>
                    <tr>
                        <td><a href="<?php echo esc_url( add_query_arg( ['page' => 'lcis-systems-company', 'edit_company' => $company['company_id']] ) ); ?>"><?php echo esc_html( $company['company_name'] ); ?></a></td>
                        <td><?php echo esc_html( $company['company_address'] ); ?></td>
                        <td><?php echo esc_html( $company['company_phone'] ); ?></td>
                        <td><?php echo esc_html( $owner ? $owner->display_name : '' ); ?></td>
                        <td><?php echo esc_html( $company['company_email'] ); ?></td>
                        <td><a href="<?php echo esc_url( add_query_arg( ['page' => 'lcis-systems-company', 'edit_company' => $company['company_id']] ) ); ?>">Edit</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6">No companies found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <h2>Add New Company</h2>
    <form method="post" action="">
        <?php wp_nonce_field('add_company', 'add_company_nonce'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="company_name">Company Name</label></th>
                <td><input name="company_name" type="text" id="company_name" value="" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="company_address">Address</label></th>
                <td><input name="company_address" type="text" id="company_address" value="" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="company_phone">Phone</label></th>
                <td><input name="company_phone" type="text" id="company_phone" value="" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="company_email">Email</label></th>
                <td><input name="company_email" type="email" id="company_email" value="" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="owner_user_id">Owner</label></th>
                <td>
                    <select name="owner_user_id" id="owner_user_id">
                        <?php 
                        $owners = get_users(); // adjust when LCSI roles implemented
                        foreach ( $owners as $owner_item ) {
                            echo '<option value="' . esc_attr( $owner_item->ID ) . '">' . esc_html( $owner_item->display_name ) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
        <?php submit_button('Add Company', 'primary', 'submit_company'); ?>
    </form>
</div>
