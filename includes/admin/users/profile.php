<?php
defined('ABSPATH') || exit;
?>
<div class="wrap">
<?php
$uid = isset( $_GET['edit_user'] ) ? intval( $_GET['edit_user'] ) : 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['certs_json']) ) {
    if ( ! empty( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'lcsi_save_user_profile_' . $uid ) ) {
        $certs = wp_unslash( $_POST['certs_json'] );
        update_user_meta( $uid, 'lcsi_user_certs', wp_slash( $certs ) );
        echo '<div id="message" class="notice notice-success is-dismissible"><p>Certifications saved.</p></div>';
    }
}
$coded = get_user_meta( $uid, 'lcsi_user_certs', true );
$existing_certs = ! empty( $coded ) ? json_decode( wp_unslash( $coded ), true ) : array();
if ( ! is_array( $existing_certs ) ) {
    $existing_certs = array();
}
?>

<div class="lcsi-settings-intro">
  <div class="logo-column">
    <img src="<?php echo esc_url(LCSIS_PLUGIN_URL.'assets/images/lcsilogo.png');?>" height="100">
  </div>
  <div class="text-column">
    <?php
      $uid   = isset($_GET['edit_user']) ? intval($_GET['edit_user']) : 0;
      $first = get_user_meta($uid, 'first_name', true);
      $last  = get_user_meta($uid, 'last_name', true);
    ?>
    <h2><strong>
      Left Coast Survey &amp; Inspection Systems – User Profile:
      <?php echo esc_html( trim("$first $last") ); ?>
    </strong></h2>
  </div>
</div>
<?php
// Determine active tab
$active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'details';

// Handle saving of Contact Info
if (isset($_POST['save_profile']) && isset($_GET['edit_user']) && $active_tab === 'details') {
    $uid = intval($_GET['edit_user']);
    $fields = array(
        'first_name','last_name','job_title','department','manager',
        'work_email','personal_email','mobile_phone','work_phone',
        'address_street','address_city','address_state',
        'address_zip','address_country','timezone'
    );
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_user_meta($uid, $field, sanitize_text_field($_POST[$field]));
        }
    }
    echo '<div id="message" class="updated below-h2"><p>Contact information saved.</p></div>';
}

// Handle saving of Emergency Contacts
if (isset($_POST['save_profile']) && isset($_GET['edit_user']) && $active_tab === 'emergency') {
    $uid = intval($_GET['edit_user']);
    for ($i = 1; $i <= 2; $i++) {
        $prefix = 'emergency' . $i . '_';
        $fields = array('name', 'relationship', 'phone', 'email');
        foreach ($fields as $field) {
            if (isset($_POST[$prefix . $field])) {
                update_user_meta($uid, $prefix . $field, sanitize_text_field($_POST[$prefix . $field]));
            }
        }
    }
    echo '<div id="message" class="updated below-h2"><p>Emergency contacts saved.</p></div>';
}



// Nav tabs
echo '<h2 class="nav-tab-wrapper">';
$tabs = array(
  'details' => 'Contact Info',
  'emergency' => 'Emergency Contacts',
  'certifications' => 'Certificates',
  'reports' => 'Reports'
);
foreach ($tabs as $tab_key => $tab_label) {
    $class = ($active_tab === $tab_key) ? ' nav-tab-active' : '';
    echo '<a class="nav-tab'.$class.'" href="?page=' . esc_attr($_GET['page']) . '&edit_user=' . intval($_GET['edit_user']) . '&tab='.$tab_key.'">'.$tab_label.'</a>';
}
echo '</h2>';

// Content area
echo '<form method="post" action="">';
echo '<input type="hidden" name="tab" value="' . esc_attr($active_tab) . '" />';
echo '<div class="lcsi-tab-content">';
switch ($active_tab) {
    
        
        
        
        
        
        case 'details':
            ?>
            <h3>Contact Information</h3>
            <div class="form-grid">
            <?php $uid = intval($_GET['edit_user']); ?>
<script type="text/javascript">
window.certSchemas = <?php echo wp_json_encode($cert_schemas); ?>;
</script>
            <div class="col-2">
              <label for="first_name">First Name</label>
              <input name="first_name" type="text" id="first_name" value="<?php echo esc_attr(get_user_meta($uid, 'first_name', true)); ?>">
            </div>
            <div class="col-2">
              <label for="last_name">Last Name</label>
              <input name="last_name" type="text" id="last_name" value="<?php echo esc_attr(get_user_meta($uid, 'last_name', true)); ?>">
            </div>
            <div class="col-2">
              <label for="job_title">Job Title</label>
              <input name="job_title" type="text" id="job_title" value="<?php echo esc_attr(get_user_meta($uid, 'job_title', true)); ?>">
            </div>
            <div class="col-2">
              <label for="department">Department</label>
              <input name="department" type="text" id="department" value="<?php echo esc_attr(get_user_meta($uid, 'department', true)); ?>">
            </div>
            <div class="col-2">
              <label for="manager">Manager/Supervisor</label>
              <input name="manager" type="text" id="manager" value="<?php echo esc_attr(get_user_meta($uid, 'manager', true)); ?>">
            </div>
            <div class="col-2">
              <label for="work_email">Work Email</label>
              <input name="work_email" type="email" id="work_email" value="<?php echo esc_attr(get_user_meta($uid, 'work_email', true)); ?>">
            </div>
            <div class="col-2">
              <label for="personal_email">Personal Email</label>
              <input name="personal_email" type="email" id="personal_email" value="<?php echo esc_attr(get_user_meta($uid, 'personal_email', true)); ?>">
            </div>
            <div class="col-1">
              <label for="mobile_phone">Mobile Phone</label>
              <input name="mobile_phone" type="tel" id="mobile_phone" value="<?php echo esc_attr(get_user_meta($uid, 'mobile_phone', true)); ?>">
            </div>
            <div class="col-1">
              <label for="work_phone">Work Phone</label>
              <input name="work_phone" type="tel" id="work_phone" value="<?php echo esc_attr(get_user_meta($uid, 'work_phone', true)); ?>">
            </div>
            <div class="col-4">
              <label for="address_street">Street Address</label>
              <input name="address_street" type="text" id="address_street" value="<?php echo esc_attr(get_user_meta($uid, 'address_street', true)); ?>">
            </div>
            <div class="col-1">
              <label for="address_city">City</label>
              <input name="address_city" type="text" id="address_city" value="<?php echo esc_attr(get_user_meta($uid, 'address_city', true)); ?>">
            </div>
            <div class="col-1">
              <label for="address_state">State/Province</label>
              <input name="address_state" type="text" id="address_state" value="<?php echo esc_attr(get_user_meta($uid, 'address_state', true)); ?>">
            </div>
            <div class="col-1">
              <label for="address_zip">ZIP/Postal Code</label>
              <input name="address_zip" type="text" id="address_zip" value="<?php echo esc_attr(get_user_meta($uid, 'address_zip', true)); ?>">
            </div>
            <div class="col-1">
              <label for="address_country">Country</label>
              <input name="address_country" type="text" id="address_country" value="<?php echo esc_attr(get_user_meta($uid, 'address_country', true)); ?>">
            </div>
            <div class="col-2">
            <div class="col-2">
              <label for="timezone">Time Zone</label>
              <select name="timezone" id="timezone">
              <?php
                $current_tz = esc_attr(get_user_meta($uid, 'timezone', true));
                foreach (timezone_identifiers_list() as $tz) {
                    printf(
                        '<option value="%s"%s>%s</option>',
                        esc_attr($tz),
                        selected($current_tz, $tz, false),
                        esc_html($tz)
                    );
                }
              ?>
              </select>
            </div>
            </div>
            </div>
            <?php
            break;






    case 'emergency':
        ?>
        <h3>Emergency Contacts</h3>
        <div class="form-grid">
        <?php $uid = intval($_GET['edit_user']); ?>
        <?php for ($i = 1; $i <= 2; $i++): 
            $prefix = 'emergency' . $i . '_';
        ?>
            <div class="col-4"><h4>Contact <?php echo $i; ?></h4></div>
            <div class="col-2">
              <label for="<?php echo $prefix; ?>name">Name</label>
              <input name="<?php echo $prefix; ?>name" type="text" id="<?php echo $prefix; ?>name" value="<?php echo esc_attr(get_user_meta($uid, $prefix . 'name', true)); ?>">
            </div>
            <div class="col-2">
              <label for="<?php echo $prefix; ?>relationship">Relationship</label>
              <input name="<?php echo $prefix; ?>relationship" type="text" id="<?php echo $prefix; ?>relationship" value="<?php echo esc_attr(get_user_meta($uid, $prefix . 'relationship', true)); ?>">
            </div>
            <div class="col-2">
              <label for="<?php echo $prefix; ?>phone">Phone</label>
              <input name="<?php echo $prefix; ?>phone" type="tel" id="<?php echo $prefix; ?>phone" value="<?php echo esc_attr(get_user_meta($uid, $prefix . 'phone', true)); ?>">
            </div>
            <div class="col-2">
              <label for="<?php echo $prefix; ?>email">Email</label>
              <input name="<?php echo $prefix; ?>email" type="email" id="<?php echo $prefix; ?>email" value="<?php echo esc_attr(get_user_meta($uid, $prefix . 'email', true)); ?>">
            </div>
        <?php endfor; ?>
        </div>
        <?php
        break;
    
    
    case 'certifications':
        ?>
<?php $profile_user = get_userdata( $uid ); ?>
<?php wp_nonce_field( 'lcsi_save_user_profile_' . $uid, '_wpnonce' ); ?>
<input type="hidden" name="certs_json" id="certs_json" value="<?php echo esc_attr( wp_json_encode( $existing_certs ) ); ?>" />
        <h3>Certificates &amp; Accreditations</h3>
        <table class="widefat">
          <thead>
            <tr>
              <th>Organization</th>
              <th>Certification</th>
              <th>Abbreviation</th>
              <th>Date Issued</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
<?php
if ( ! empty( $existing_certs ) ) {
    foreach ( $existing_certs as $cert ) {
        echo '<tr>';
        echo '<td>' . esc_html( $cert['organizationName'] ?? $cert['orgKey'] ?? '' ) . '</td>';
        echo '<td>' . esc_html( $cert['certification'] ) . '</td>';
        echo '<td>' . esc_html( $cert['abbreviation'] ) . '</td>';
        echo '<td>' . esc_html( $cert['dateIssued'] ) . '</td>';
        echo '<td><button type="button" class="button-link delete-cert">Remove</button></td>';
        echo '</tr>';
    }
}
?>
</tbody>
        </table>
    <?php include __DIR__ . '/add-certifications.php'; ?>
    <?php
        break;


    case 'reports':
        echo '<h3>User Reports</h3>';
        echo '<p>Coming soon.</p>';
        break;
}
echo '</div>';
echo '<p><input type="submit" name="save_profile" class="button button-primary" value="Save Changes" /></p>';
echo '</form>';

echo '</div>'; // .wrap
?>