<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template partial loaded via require_once inside a function (rafflepress_lite_*_page / render / email builder); its top-level variables are function-local, not global.
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// translations
require_once RAFFLEPRESS_PLUGIN_PATH . 'resources/views/backend-translations.php';

//wp_enqueue_media();

global $wpdb;

$giveaway_id = '';
if ( ! empty( $_GET['id'] ) ) {
	$giveaway_id = $_GET['id'];
}

if ( ! empty( $_GET['rp-debug'] ) ) {
	$tablename = $wpdb->prefix . 'rafflepress_giveaways';
	$sql       = "SELECT meta FROM $tablename WHERE id = %d LIMIT 3";
	$safe_sql  = $wpdb->prepare( $sql, $giveaway_id );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- Direct query on a custom RafflePress table (no core API); real-time data or write op, so object caching is not applied.
	$meta      = $wpdb->get_var( $safe_sql );
	echo 'rp-debug';
	var_dump( $meta );
}

// Template Vars
$timezones            = rafflepress_lite_get_timezones();
$times                = rafflepress_lite_get_times();
$has_refer_a_friend   = false;
$has_automatic_entry  = false;
$entry_options        = rafflepress_lite_entry_options();
$temp_start_countdown = 0;
$temp_end_countdown   = 0;
$total_entires        = 0;

if ( empty( $giveaway_id ) ) {
	// create new giveaway and redirect
	$giveaway = array(
		'id'   => 0,
		'name' => '',
		'type' => '',
	);
	$settings = array(
		'is_new' => true
	);
} else {

	// update giveaway
	// $tablename = $wpdb->prefix . 'rafflepress_giveaways';
	// $sql = "SELECT * FROM $tablename WHERE id = %d";
	// $safe_sql = $wpdb->prepare($sql, $giveaway_id);
	// $giveaway = $wpdb->get_row($safe_sql);

	$tablename  = $wpdb->prefix . 'rafflepress_giveaways';
	$tablename2 = $wpdb->prefix . 'rafflepress_entries';
	$sql        = "SELECT *,(SELECT count(id) FROM $tablename2 WHERE giveaway_id = %d AND deleted_at IS NULL) as entries FROM $tablename WHERE id = %d";
	$safe_sql   = $wpdb->prepare( $sql, $giveaway_id, $giveaway_id );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- Direct query on a custom RafflePress table (no core API); real-time data or write op, so object caching is not applied.
	$giveaway   = $wpdb->get_row( $safe_sql );

	if ( ! empty( $giveaway->entries ) ) {
		$total_entires = $giveaway->entries;
	}

	// Set active bool
	if ( $giveaway->active == 1 ) {
		$giveaway->active = true;
	} else {
		$giveaway->active = false;
	}

	// if ($giveaway->show_winners == 1) {
	//     $giveaway->show_winners= true;
	// } else {
	//     $giveaway->show_winners= false;
	// }

	if ( empty( $giveaway->settings ) ) {
		$settings = array(
			'prizes'        => array(
				array(
					'name'        => 'My Awesome Prize',
					'description' => '',
					'image'       => '',
					'video'       => '',
				),
			),
			'entry_options' => array(),
		);
	} else {
		$settings = json_decode( $giveaway->settings, true );
		$settings = rafflepress_lite_array_add( $settings, 'entry_options', array() );
		// Existing giveaways are not new
		$settings['is_new'] = false;
	}

	$temp_start_countdown = strtotime( $giveaway->starts . ' UTC' );

	$temp_end_countdown = strtotime( $giveaway->ends . ' UTC' );

	// Default Confirmation Email
	if ( empty( $settings['confirmation_email'] ) ) {
		$settings['confirmation_email'] = __( 'Please click the link below to confirm your email address.', 'rafflepress' ) . PHP_EOL . '{confirmation-link}';
	}

	if ( empty( $settings['confirmation_subject'] ) ) {
		$settings['confirmation_subject'] = __( '[Action Required] Confirm your entry', 'rafflepress' );
	}

	if ( empty( $settings['from_name'] ) ) {
		$settings['from_name'] = get_option( 'admin_email' );
	}

	if ( empty( $settings['from_email'] ) ) {
		$settings['from_email'] = get_option( 'admin_email' );
	}

	// has refer a friend
	foreach ( $settings['entry_options'] as $v ) {
		if ( $v['type'] == 'refer-a-friend' ) {
			$has_refer_a_friend = true;
		}
		if ( $v['type'] == 'automatic-entry' ) {
			$has_automatic_entry = true;
		}
	}
}

// Email integration logic
$rafflepress_api_token = get_option( 'rafflepress_api_token' );
$license_key           = get_option( 'rafflepress_api_key' );
$email_integration_url = '';

// Pers
$per = array();
$active_license = false;



// set design
if ( ! empty( $settings['page_background_color'] ) ) {
	echo '<style>#rafflepress-preview-wrapper { background-color: ' . esc_attr( $settings['page_background_color'] ) . '; }</style>';
}

if ( ! empty( $settings['page_background_image'] ) ) {
	echo '<style>#rafflepress-preview-wrapper { background-image: url(' . esc_url( $settings['page_background_image'] ) . '); }</style>';
}

if ( ! empty( $settings['font'] ) ) {
	$font = rafflepress_lite_generate_font_output( $settings['font'] );
	echo $font; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static plugin-defined HTML/CSS markup with no dynamic user data; function returns hardcoded Google Fonts <link> tags and <style> blocks only.
}

// Get help documents
$inline_help_articles = rafflepress_lite_fetch_inline_help_data();


$editor_edit = false;
if (current_user_can('unfiltered_html') && current_user_can('administrator')) {
	$editor_edit = true;
}

?>


<div id="rafflepress-vue-app-builder"></div>
<div id="rafflepress-temp-font"></div>

<?php require_once RAFFLEPRESS_PLUGIN_PATH . 'resources/giveaway-templates/rules-template.php'; ?>

<?php require_once RAFFLEPRESS_PLUGIN_PATH . 'resources/giveaway-templates/google-fonts.php'; ?>
<?php require_once RAFFLEPRESS_PLUGIN_PATH . 'resources/giveaway-templates/color-schemes.php'; ?>

<script>
<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=rafflepress_lite_save_template', 'rafflepress_lite_save_template' ) ); ?>
var rafflepress_template_save_url = "<?php echo esc_url_raw( $ajax_url ); ?>";

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=rafflepress_lite_save_publish', 'rafflepress_lite_save_publish' ) ); ?>
var rafflepress_publish_save_url = "<?php echo esc_url_raw( $ajax_url ); ?>";

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=rafflepress_lite_save_giveaway', 'rafflepress_lite_save_giveaway' ) ); ?>
var rafflepress_save_giveaway_url = "<?php echo esc_url_raw( $ajax_url ); ?>";

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=rafflepress_lite_save_slug', 'rafflepress_lite_save_slug' ) ); ?>
var rafflepress_save_slug_url = "<?php echo esc_url_raw( $ajax_url ); ?>";

<?php $create_giveaway_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=rafflepress_lite_create_giveaway', 'rafflepress_create_giveaway' ) ); ?>
var rafflepress_create_giveaway_url = "<?php echo esc_url_raw( $create_giveaway_url ); ?>";

<?php $utc_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=rafflepress_lite_get_utc_offset', 'rafflepress_lite_get_utc_offset' ) ); ?>
var rafflepress_utc_url = "<?php echo esc_url_raw( $utc_url ); ?>";

<?php $get_font_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=rafflepress_lite_get_font', 'rafflepress_lite_get_font' ) ); ?>
var rafflepress_get_font_url = "<?php echo esc_url_raw( $get_font_url ); ?>";

<?php $tools_ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=rafflepress_lite_get_automation_tool_list', 'rafflepress_lite_get_automation_tool_list' ) ); ?>
var rafflepress_automation_tools_url = "<?php echo esc_url_raw( $tools_ajax_url ); ?>";

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=rafflepress_lite_install_addon', 'rafflepress_lite_install_addon' ) ); ?>
var rafflepress_get_install_automation_url = "<?php echo esc_url_raw( $ajax_url ); ?>";

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=rafflepress_lite_activate_addon', 'rafflepress_lite_activate_addon' ) ); ?>
var rafflepress_activate_automation_url = "<?php echo esc_url_raw( $ajax_url ); ?>";

<?php $ajax_url = html_entity_decode( wp_nonce_url( 'admin-ajax.php?action=rafflepress_lite_deactivate_addon', 'rafflepress_lite_deactivate_addon' ) ); ?>
var rafflepress_deactivate_automation_url = "<?php echo esc_url_raw( $ajax_url ); ?>";

<?php $rafflepress_upgrade_link = rafflepress_lite_upgrade_link( '' ); ?>

var rafflepress_data =
	<?php
	echo json_encode(
		array(
			'api_token'             => $rafflepress_api_token,
			'license_key'           => $license_key,
			'page_path'             => 'rafflepress_lite',
			'plugin_path'           => RAFFLEPRESS_PLUGIN_URL,
			'total_entries'         => $total_entires,
			'home_url'              => home_url(),
			'upgrade_link'          => $rafflepress_upgrade_link,
			'slug'                  => rafflepress_lite_get_slug(),
			'giveaway'              => $giveaway,
			'settings'              => $settings,
			'entry_options'         => $entry_options,
			'fonts'                 => $rafflepress_fonts,
			'color_schemes'         => $rafflepress_color_schemes,
			'rules_template'        => $rafflepress_rules,
			'timezones'             => $timezones,
			'times'                 => $times,
			'has_refer_a_friend'    => $has_refer_a_friend,
			'has_automatic_entry'   => $has_automatic_entry,
			'email_integration_url' => $email_integration_url,
			'temp_start_countdown'  => $temp_start_countdown,
			'temp_end_countdown'    => $temp_end_countdown,
			'per'                   => $per,
			'inline_help_articles'  => $inline_help_articles,
			'editor_edit'			=> $editor_edit,
		)
	);
	?>
		;

var rafflepress_backend_translation_data = 
		<?php echo json_encode( $rp_backend_translations ); ?>;

</script>
