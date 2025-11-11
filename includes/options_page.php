<?php
/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */

/**
 * custom option and settings
 */
function parl_settings_init() {
	// Register a new setting for "parl" page.
	register_setting( 'parl', 'parl_options' );

	// Register a new section in the "parl" page.
	add_settings_section(
		'parl_section_developers',
		'', 'parl_section_developers_callback',
		'parl'
	);

	// Register a new field in the "parl_section_developers" section, inside the "parl" page.
	add_settings_field(
		'parl_partners_logos', // As of WP 4.6 this value is used only internally.
		'Options',
		'parl_partners_logos_cb',
		'parl',
		'parl_section_developers',
		array(
			'label_for'         => 'parl_partners_logos',
			'class'             => 'parl_row',
			'parl_custom_data' => 'custom',
		)
	);
}

/**
 * Register our parl_settings_init to the admin_init action hook.
 */
add_action( 'admin_init', 'parl_settings_init' );


/**
 * Custom option and settings:
 *  - callback functions
 */


/**
 * Developers section callback function.
 *
 * @param array $args  The settings array, defining title, id, callback.
 */
function parl_section_developers_callback( $args ) {
	
}

/**
 * Pill field callbakc function.
 *
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
function parl_partners_logos_cb( $args ) {
	// Get the value of the setting we've registered with register_setting()
	$options = get_option( 'parl_options' );
    if( !isset($options['parl_email_success']) ){
        $options['parl_email_success'] = 'Bonjour.<br>Vous avez créé un compte sur le site des enquêtes PaRL.<br>';
    }
    if( !isset($options['parl_email_success_footer']) ){
        $options['parl_email_success_footer'] = '<br>A bientôt.';
    }
	?>
    <div style="width: 100%">
    <input style="width: 100%" id="parl_partners_logos" name="parl_options[parl_partners_logos]" type="text" value="<?php echo $options['parl_partners_logos']; ?>"/>
	<p class="description">Indiquez les ID des images à placer en pied de page, séparés par des virgules.</p>
    <hr>
    
    <textarea style="width: 100%" id="parl_email_success" name="parl_options[parl_email_success]"><?php echo $options['parl_email_success']; ?></textarea>
    <p class="description">Texte du début de l'email envoyé</p>
<hr>
    
    <textarea style="width: 100%" id="parl_email_success_footer" name="parl_options[parl_email_success_footer]"><?php echo $options['parl_email_success_footer']; ?></textarea>
    <p class="description">Texte de fin de l'email envoyé</p>
    </div>
<div>

	<?php
/*
	  $user = get_user_by( 'id', $user_id );
      $user_login = $current_user->user_login;
      $birthdate = get_user_meta($user_id, 'birthdate');
      $sexe = get_user_meta($user_id, 'sexe');
      $birthplace = get_user_meta($user_id, 'birthplace');
      $town = get_user_meta($user_id, 'town');
      $langue = get_user_meta($user_id, 'langue');
      $learn_town = get_user_meta($user_id, 'learn_town');
      $profession_id = get_user_meta($user_id, 'profession_id');
      $profession_txt = get_user_meta($user_id, 'profession_txt');
      $otherlangname = get_user_meta($user_id, 'otherlangname');
      $otherspokenlangs = get_user_meta($user_id, 'otherspokenlangs');
      $learn_town_citycode = get_user_meta($user_id, 'learn_town_citycode');
      $learn_town_lat = get_user_meta($user_id, 'learn_town_lat');
	  $learn_town_lng = get_user_meta($user_id, 'learn_town_lng');
	  $birthplace_citycode = get_user_meta($user_id, 'birthplace_citycode');
      $birthplace_lat = get_user_meta($user_id, 'birthplace_lat');
	  $birthplace_lng = get_user_meta($user_id, 'birthplace_lng');
	  $town_citycode = get_user_meta($user_id, 'town_citycode');
      $town_lat = get_user_meta($user_id, 'town_lat');
      $town_lng = get_user_meta($user_id, 'town_lng');
	  
	  user_id
	  user_login
	  birthdate
	  sexe
	  birthplace
	  town
	  langue
	  learn_town
	  profession_id
	  profession_txt
	  otherlangname
	  otherspokenlangs
	  learn_town_citycode
	  learn_town_lat
	  learn_town_lng
	  birthplace_citycode
	  birthplace_lat
	  birthplace_lng
	  town_citycode
	  town_lat
	  town_lng
*/
	
	
	?>

</div>
	<?php
}

/**
 * Add the top level menu page.
 */
function parl_options_page() {
	add_menu_page(
		'PARL réglages',
		'PARL Options',
		'manage_options',
		'parl',
		'parl_options_page_html'
	);
}


/**
 * Register our parl_options_page to the admin_menu action hook.
 */
add_action( 'admin_menu', 'parl_options_page' );


/**
 * Top level menu callback function
 */
function parl_options_page_html() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// add error/update messages

	// check if the user have submitted the settings
	// WordPress will add the "settings-updated" $_GET parameter to the url
	if ( isset( $_GET['settings-updated'] ) ) {
		// add settings saved message with the class of "updated"
		add_settings_error( 'parl_messages', 'parl_message', __( 'Settings Saved', 'parl' ), 'updated' );
	}

	// show error/update messages
	settings_errors( 'parl_messages' );
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
			<?php
			// output security fields for the registered setting "parl"
			settings_fields( 'parl' );
			// output setting sections and their fields
			// (sections are registered for "parl", each field is registered to a specific section)
			do_settings_sections( 'parl' );
			// output save settings button
			submit_button( 'Save Settings' );
			?>
		</form>
	</div>
	<?php
}
?>
<?php
// Liste des options à afficher sous forme de checkbox
function parl_show_in_csv_cb() {
    $options = get_option('parl_options');
    $show_in_csv = isset($options['show_in_csv']) ? $options['show_in_csv'] : array();
    
    $fields = [
        'user_id', 'user_login', 'birthdate', 'sexe', 'birthplace',
        'town', 'langue', 'learn_town', 'profession_id', 'profession_txt',
        'otherlangname', 'otherspokenlangs', 'learn_town_citycode',
        'learn_town_lat', 'learn_town_lng', 'birthplace_citycode',
        'birthplace_lat', 'birthplace_lng', 'town_citycode', 'town_lat', 'town_lng'
    ];

    echo '<fieldset>';
    foreach ($fields as $field) {
        $checked = isset($show_in_csv[$field]) ? 'checked' : '';
        echo "<label><input type='checkbox' name='parl_options[show_in_csv][$field]' value='1' $checked> " . ucfirst(str_replace('_', ' ', $field)) . "</label><br>";
    }
    echo '</fieldset>';
}

// Enregistrer les paramètres
function parl_register_csv_options() {
    register_setting('parl', 'parl_options');

    add_settings_field(
        'parl_show_in_csv',
        'Champs à inclure dans le CSV',
        'parl_show_in_csv_cb',
        'parl',
        'parl_section_developers'
    );
}

add_action('admin_init', 'parl_register_csv_options');

?>