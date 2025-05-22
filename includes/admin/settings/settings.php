<?php
if (!defined('ABSPATH')) exit;

// OAuth callback for Google Calendar
function lcis_handle_calendar_callback() {
    if (isset($_GET['tab'], $_GET['oauth'], $_GET['code'])
        && $_GET['tab']==='google_calendar'
        && $_GET['oauth']==='callback'
    ) {
        check_admin_referer('lcsi_settings_action','lcsi_settings_nonce');
        $code = sanitize_text_field($_GET['code']);
        $client_id = get_option('lcis_google_calendar_client_id');
        $client_secret = get_option('lcis_google_calendar_client_secret');
        $redirect_uri = admin_url('admin.php?page=lcis-systems-settings&tab=google_calendar&oauth=callback');
        $resp = wp_remote_post('https://oauth2.googleapis.com/token', [
            'body'=>[
                'code'=>$code,
                'client_id'=>$client_id,
                'client_secret'=>$client_secret,
                'redirect_uri'=>$redirect_uri,
                'grant_type'=>'authorization_code'
            ],
            'timeout'=>15
        ]);
        if (!is_wp_error($resp) && wp_remote_retrieve_response_code($resp)==200) {
            $data = json_decode(wp_remote_retrieve_body($resp), true);
            update_option('lcis_google_calendar_access_token', $data['access_token']);
            update_option('lcis_google_calendar_refresh_token', $data['refresh_token']);
            echo '<div class="updated"><p>Google Calendar connected successfully.</p></div>';
        } else {
            echo '<div class="error"><p>Failed to connect to Google Calendar.</p></div>';
        }
    }
}

// Test and persist flags
function lcis_test_google_maps($key){
    $resp = wp_remote_get('https://maps.googleapis.com/maps/api/geocode/json?address=Seattle&key='.urlencode($key), ['timeout'=>15]);
    if (is_wp_error($resp)) {
        update_option('lcis_google_maps_valid','0');
        return false;
    }
    $data = json_decode(wp_remote_retrieve_body($resp), true);
    $ok = isset($data['status']) && $data['status']==='OK';
    update_option('lcis_google_maps_valid',$ok?'1':'0');
    return $ok;
}
function lcis_test_chatgpt($key){
    $resp = wp_remote_post('https://api.openai.com/v1/models',[
        'headers'=>['Authorization'=>'Bearer '.$key],
        'timeout'=>15
    ]);
    if (is_wp_error($resp)) {
        update_option('lcis_chatgpt_valid','0');
        return false;
    }
    $code = wp_remote_retrieve_response_code($resp);
    update_option('lcis_chatgpt_valid',$code===200?'1':'0');
    return $code===200;
}
function lcis_test_square($token){
    $resp = wp_remote_get('https://connect.squareup.com/v2/locations',[
        'headers'=>['Authorization'=>'Bearer '.$token],
        'timeout'=>15
    ]);
    if (is_wp_error($resp)) {
        update_option('lcis_square_valid','0');
        return false;
    }
    $code = wp_remote_retrieve_response_code($resp);
    update_option('lcis_square_valid',$code===200?'1':'0');
    return $code===200;
}

// Render settings page
function lcis_render_settings_page(){
    lcis_handle_calendar_callback();
    $tabs=['google_maps'=>'Google Maps','google_calendar'=>'Google Calendar','chatgpt'=>'ChatGPT','square'=>'Square','caching'=>'Caching & Development'];
    if ($_SERVER['REQUEST_METHOD']==='POST') {
        check_admin_referer('lcsi_settings_action','lcsi_settings_nonce');
        $tab=sanitize_text_field($_GET['tab']??'google_maps');
        switch($tab){
            case 'google_maps':
                update_option('lcis_google_maps_api_key',sanitize_text_field($_POST['lcis_google_maps_api_key']??''));
                lcis_test_google_maps(get_option('lcis_google_maps_api_key'));
                break;
            case 'google_calendar':
                update_option('lcis_google_calendar_client_id',sanitize_text_field($_POST['lcis_google_calendar_client_id']??''));
                update_option('lcis_google_calendar_client_secret',sanitize_text_field($_POST['lcis_google_calendar_client_secret']??''));
                break;
            case 'chatgpt':
                update_option('lcis_chatgpt_api_key',sanitize_text_field($_POST['lcis_chatgpt_api_key']??''));
                lcis_test_chatgpt(get_option('lcis_chatgpt_api_key'));
                break;
            case 'square':
                update_option('lcis_square_access_token',sanitize_text_field($_POST['lcis_square_access_token']??''));
                update_option('lcis_square_location_id',sanitize_text_field($_POST['lcis_square_location_id']??''));
                lcis_test_square(get_option('lcis_square_access_token'));
                break;
            case 'caching':
                update_option('lcis_dev_cache_enabled',sanitize_text_field($_POST['lcis_dev_cache_enabled']??'0'));
                break;
        }
        if($tab!=='caching') echo '<div class="updated"><p>Settings saved.</p></div>';
    }
    $current=sanitize_text_field($_GET['tab']??'google_maps');
    ?>
    <div class="wrap">
      <div class="lcsi-settings-intro">
        <div class="logo-column"><img src="<?php echo esc_url(LCSIS_PLUGIN_URL.'assets/images/lcsilogo.png');?>" height="100"></div>
        <div class="text-column"><p><strong>Left Coast Survey & Inspection Systems unifies maps, calendar sync, AI reports, and payments.</strong></p></div>
      </div>
      <div class="lcsi-settings-overview">
        <?php $gmok = get_option('lcis_google_maps_valid')==='1'; ?>
        <div class="overview-item <?php echo $gmok?'connected':'not-connected';?>"><span class="status-indicator"></span>Google Maps API: <strong><?php echo $gmok?'Connected':'Not Connected';?></strong></div>
        <?php $gcalok = !empty(get_option('lcis_google_calendar_access_token')); ?>
        <div class="overview-item <?php echo $gcalok?'connected':'not-connected';?>"><span class="status-indicator"></span>Google Calendar: <strong><?php echo $gcalok?'Connected':'Not Connected';?></strong></div>
        <?php $cgok = get_option('lcis_chatgpt_valid')==='1'; ?>
        <div class="overview-item <?php echo $cgok?'connected':'not-connected';?>"><span class="status-indicator"></span>ChatGPT API: <strong><?php echo $cgok?'Connected':'Not Connected';?></strong></div>
        <?php $sqok = get_option('lcis_square_valid')==='1'; ?>
        <div class="overview-item <?php echo $sqok?'connected':'not-connected';?>"><span class="status-indicator"></span>Square API: <strong><?php echo $sqok?'Connected':'Not Connected';?></strong></div>
        <?php $cache = get_option('lcis_dev_cache_enabled','0'); ?>
        <div class="overview-item <?php echo $cache==='1'?'connected':'not-connected';?>"><span class="status-indicator"></span>Caching: <strong><?php echo $cache==='1'?'Disabled':'Enabled';?></strong></div>
      </div>
      <h2 class="nav-tab-wrapper">
        <?php foreach($tabs as $slug=>$nm): $act = $slug==$current?' nav-tab-active':''; ?>
          <a class="nav-tab<?php echo $act;?>" href="<?php echo esc_url(admin_url('admin.php?page=lcis-systems-settings&tab='.$slug));?>"><?php echo esc_html($nm);?></a>
        <?php endforeach;?>
      </h2>
      <form method="post">
        <?php wp_nonce_field('lcsi_settings_action','lcsi_settings_nonce');?>
        <div class="instructions">
          <?php if($current==='google_maps'): ?>
            <h4>How to get a Google Maps API key</h4>
            <ol>
              <li>In Google Cloud Console, go to <a href="https://console.cloud.google.com/apis/credentials">Credentials</a>.</li>
              <li>Create an API key restricted by IP and enabled for Geocoding API.</li>
              <li>Paste it below and Save.</li>
            </ol>
          <?php elseif($current==='google_calendar'): ?>
            <h4>Google Calendar OAuth Setup</h4>
            <ol>
              <li>Enable Calendar API at <a href="https://console.developers.google.com/apis/library/calendar.googleapis.com">API Library</a>.</li>
              <li>Create OAuth Client ID in <a href="https://console.developers.google.com/apis/credentials">Credentials</a> (Web App, add redirect URI).</li>
              <li>Paste Client ID and Secret below, Save, then Connect.</li>
            </ol>
          <?php elseif($current==='chatgpt'): ?>
            <h4>How to get a ChatGPT API key</h4>
            <ol>
              <li>Visit <a href="https://platform.openai.com/account/api-keys">OpenAI Keys</a>.</li>
              <li>Generate new key and copy.</li>
              <li>Paste below and Save.</li>
            </ol>
          <?php elseif($current==='square'): ?>
            <h4>How to obtain Square API credentials</h4>
            <ol>
              <li>Go to <a href="https://developer.squareup.com/apps">Square Developer</a>.</li>
              <li>Copy Access Token and Location ID.</li>
              <li>Paste below and Save.</li>
            </ol>
          <?php else: ?>
            <h4>Toggling development caching</h4>
            <p>Use On/Off buttons below (no separate Save needed).</p>
          <?php endif; ?>
        </div>
        <table class="form-table">
          <?php if($current==='google_maps'): ?>
            <tr><th><label for="lcis_google_maps_api_key">API Key</label></th>
            <td><input type="text" name="lcis_google_maps_api_key" id="lcis_google_maps_api_key" class="regular-text" value="<?php echo esc_attr(get_option('lcis_google_maps_api_key'));?>"/></td></tr>
            <tr><td colspan="2"><p class="submit"><input type="submit" class="button button-primary" value="Save Changes"/></p></td></tr>
          <?php elseif($current==='google_calendar'): ?>
            <tr><th><label for="lcis_google_calendar_client_id">Client ID</label></th>
            <td><input type="text" name="lcis_google_calendar_client_id" id="lcis_google_calendar_client_id" class="regular-text" value="<?php echo esc_attr(get_option('lcis_google_calendar_client_id'));?>"/></td></tr>
            <tr><th><label for="lcis_google_calendar_client_secret">Client Secret</label></th>
            <td><input type="text" name="lcis_google_calendar_client_secret" id="lcis_google_calendar_client_secret" class="regular-text" value="<?php echo esc_attr(get_option('lcis_google_calendar_client_secret'));?>"/></td></tr>
            <tr><td colspan="2"><p class="submit"><input type="submit" class="button button-primary" value="Save Changes"/></p></td></tr>
            <?php if(get_option('lcis_google_calendar_client_id') && get_option('lcis_google_calendar_client_secret')): 
              $cid=urlencode(get_option('lcis_google_calendar_client_id'));
              $redir=urlencode(admin_url('admin.php?page=lcis-systems-settings&tab=google_calendar&oauth=callback'));
              $scope=urlencode('https://www.googleapis.com/auth/calendar.events');
              $auth="https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id={$cid}&redirect_uri={$redir}&scope={$scope}&access_type=offline&prompt=consent";
            ?>
            <tr><td colspan="2"><a href="<?php echo esc_url($auth);?>" class="button button-secondary">Connect to Google Calendar</a></td></tr>
            <?php endif; ?>
          <?php elseif($current==='chatgpt'): ?>
            <tr><th><label for="lcis_chatgpt_api_key">API Key</label></th>
            <td><input type="text" name="lcis_chatgpt_api_key" id="lcis_chatgpt_api_key" class="regular-text" value="<?php echo esc_attr(get_option('lcis_chatgpt_api_key'));?>"/></td></tr>
            <tr><td colspan="2"><p class="submit"><input type="submit" class="button button-primary" value="Save Changes"/></p></td></tr>
          <?php elseif($current==='square'): ?>
            <tr><th><label for="lcis_square_access_token">Access Token</label></th>
            <td><input type="text" name="lcis_square_access_token" id="lcis_square_access_token" class="regular-text" value="<?php echo esc_attr(get_option('lcis_square_access_token'));?>"/></td></tr>
            <tr><th><label for="lcis_square_location_id">Location ID</label></th>
            <td><input type="text" name="lcis_square_location_id" id="lcis_square_location_id" class="regular-text" value="<?php echo esc_attr(get_option('lcis_square_location_id'));?>"/></td></tr>
            <tr><td colspan="2"><p class="submit"><input type="submit" class="button button-primary" value="Save Changes"/></p></td></tr>
          <?php else: ?>
            <tr><th>Development Mode</th>
            <td>
              <?php $val=get_option('lcis_dev_cache_enabled','0'); ?>
              <button type="submit" name="lcis_dev_cache_enabled" value="1" class="button<?php echo $val==='1'?' button-primary':'';?>">On</button>
              <button type="submit" name="lcis_dev_cache_enabled" value="0" class="button<?php echo $val==='0'?' button-primary':'';?>">Off</button>
            </td></tr>
          <?php endif; ?>
        </table>
      </form>
    </div>
    <?php
}
?>