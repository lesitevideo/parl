<?php

require_once( __DIR__ . '/includes/wp-settings.php');
require_once( __DIR__ . '/includes/enquetes-post_type.php');
require_once( __DIR__ . '/includes/sondages-post_type.php');
require_once( __DIR__ . '/includes/enquetes-metas_boxes.php');
require_once( __DIR__ . '/includes/sondages-metas_boxes.php');
require_once( __DIR__ . '/includes/enquetes-form_front.php');
require_once( __DIR__ . '/includes/sondages-form_front.php');

require_once( __DIR__ . '/includes/bo-enquetes-results.php');
require_once( __DIR__ . '/includes/bo-sondages-results.php');

require_once( __DIR__ . '/includes/ajax-register-fromfront.php');
require_once( __DIR__ . '/includes/options_page.php');
require_once( __DIR__ . '/includes/export-enquetes.php');

add_image_size( '4_3_medium', 450, 338, array( 'center', 'center' ) );
add_image_size( 'logos_footer', 0, 75, false );

function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo-bordeaux-montaigne.svg);
		height: 65px;
		width: 320px;
		background-size: 320px 65px;
		background-repeat: no-repeat;
        	padding-bottom: 30px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

add_action( 'phpmailer_init', function( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = SMTP_HOST;
    $phpmailer->SMTPAuth   = SMTP_AUTH;
    $phpmailer->Port       = SMTP_PORT;
    $phpmailer->Username   = SMTP_USER;
    $phpmailer->Password   = SMTP_PASS;
    $phpmailer->SMTPSecure = SMTP_SECURE;
    $phpmailer->From       = SMTP_FROM;
    $phpmailer->FromName   = SMTP_FROM_NAME;
});


add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );

function ajax_login(){

    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;

    $user_signon = wp_signon( $info, false );
    if ( is_wp_error($user_signon) ){
        echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.')));
    } else {
        echo json_encode(array('loggedin'=>true, 'message'=>__('Login successful, redirecting...')));
    }

    die();
}



if( is_admin() ){
    remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
    add_action( 'personal_options', 'prefix_hide_personal_options' );
}

function prefix_hide_personal_options() {
  ?>
    <script type="text/javascript">
        jQuery( document ).ready(function( $ ){
            $("#application-passwords-section").remove();
            //$( '#your-profile .form-table:first, #your-profile h3:first' ).remove();
        } );
    </script>
  <?php
}


// Ajouter les champs personnalisés à la page de profil utilisateur
add_action('show_user_profile', 'extra_user_profile_fields');
add_action('edit_user_profile', 'extra_user_profile_fields');

function extra_user_profile_fields($user) { ?>
    <h3>Informations sur l'internaute</h3>
    <table class="form-table">
        <?php
        // Liste des champs utilisateur
        $user_meta_fields = [
            'birthdate' => 'Date de naissance (yyyy-mm-dd)',
            'sexe' => 'Sexe',
            'birthplace' => 'Lieu de naissance',
            'birthplace_citycode' => 'Code INSEE Lieu de naissance',
            'town' => 'Localité de résidence',
            'town_citycode' => 'Code INSEE Localité de résidence',
            'langue' => 'Parler local',
            'learn_town' => 'Commune d’apprentissage de la langue régionale',
            'learn_town_citycode' => 'Code INSEE Commune d’apprentissage de la langue régionale',
            'learn_town_lat' => 'Latitude Commune d’apprentissage',
            'learn_town_lng' => 'Longitude Commune d’apprentissage',
            'profession_id' => 'Code INSEE Profession(s) occupée(s)',
            'profession_txt' => 'Profession(s) occupée(s)'
        ];

        foreach ($user_meta_fields as $meta_key => $label) {
            $meta_value = esc_attr(get_user_meta($user->ID, $meta_key, true));
            ?>
            <tr>
                <th><label for="<?php echo $meta_key; ?>"><?php echo $label; ?></label></th>
                <td>
                    <input type="text" id="<?php echo $meta_key; ?>" name="<?php echo $meta_key; ?>" value="<?php echo $meta_value; ?>" class="regular-text" />
                </td>
            </tr>
        <?php } ?>

        <tr>
            <th><label>Enquêtes</label></th>
            <td>
                <?php
                $args = [
                    'numberposts' => -1,
                    'post_type'   => 'enquetes'
                ];
                $enquetes = get_posts($args);
                foreach ($enquetes as $enquete) {
                    $meta_key = 'enquete_' . $enquete->ID . '_locked';
                    $status = get_user_meta($user->ID, $meta_key, true);
                    $title = esc_html($enquete->post_title);
                    ?>
                    <div style="display:flex; align-items: center;">
                        <p style="margin: 0 15px 0 0;"><?php echo $title; ?></p>
                        <input type="checkbox" name="<?php echo $meta_key; ?>" id="<?php echo $meta_key; ?>" value="locked" <?php checked($status, 'locked'); ?> />
                        <label for="<?php echo $meta_key; ?>">Verrouillé</label>
                    </div>
                    <?php
                }
                ?>
            </td>
        </tr>
    </table>
<?php }

// Sauvegarde des champs utilisateurs personnalisés
add_action('personal_options_update', 'save_extra_user_profile_fields');
add_action('edit_user_profile_update', 'save_extra_user_profile_fields');

function save_extra_user_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    // Liste des champs à enregistrer
    $user_meta_fields = [
        'birthdate', 'sexe', 'birthplace', 'birthplace_citycode',
        'town', 'town_citycode', 'langue', 'learn_town',
        'learn_town_citycode', 'learn_town_lat', 'learn_town_lng',
        'profession_id', 'profession_txt'
    ];

    foreach ($user_meta_fields as $meta_key) {
        if (isset($_POST[$meta_key])) {
            update_user_meta($user_id, $meta_key, sanitize_text_field($_POST[$meta_key]));
        }
    }

    // Gestion des cases à cocher pour les enquêtes
    $args = ['numberposts' => -1, 'post_type' => 'enquetes'];
    $enquetes = get_posts($args);

    foreach ($enquetes as $enquete) {
        $meta_key = 'enquete_' . $enquete->ID . '_locked';
        if (isset($_POST[$meta_key])) {
            update_user_meta($user_id, $meta_key, 'locked');
        } else {
            delete_user_meta($user_id, $meta_key);
        }
    }
}



function update_enquete_status() {
    // Vérification de nonce pour la sécurité
    check_ajax_referer('update_enquete_status_nonce');

    // Vérification des permissions et récupération des valeurs
    if (!isset($_POST['user_id'], $_POST['enquete_id'], $_POST['status'])) {
        wp_send_json_error(['message' => 'Données manquantes']);
    }

    $user_id = intval($_POST['user_id']);
    $enquete_id = intval($_POST['enquete_id']);
    $status = sanitize_text_field($_POST['status']); // Nettoyage des données

    // Mettre à jour le meta utilisateur
    update_user_meta($user_id, 'enquete_' . $enquete_id . '_locked', $status);

    wp_send_json_success(['message' => 'Mise à jour effectuée']);
}

// Ajouter AJAX pour les utilisateurs connectés et non connectés
add_action('wp_ajax_update_enquete_status', 'update_enquete_status');






?>