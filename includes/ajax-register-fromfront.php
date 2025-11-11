<?php

add_action('wp_ajax_register_user_front_end', 'register_user_front_end');
add_action('wp_ajax_nopriv_register_user_front_end', 'register_user_front_end');

function register_user_front_end() {
    // Vérification du nonce
    if (
        !isset($_POST['nonce']) ||
        !wp_verify_nonce($_POST['nonce'], 'register_user_front_end')
    ) {
        wp_send_json_error("Vous n’avez pas l’autorisation d’effectuer cette action.");
    }

    // Nettoyage des champs
    $email              = sanitize_email($_POST['email']);
    $birthdate          = sanitize_text_field($_POST['birthdate']);
    $sexe               = sanitize_text_field($_POST['sexe']);
    $birthplace         = sanitize_text_field($_POST['birthplace']);
    $town               = sanitize_text_field($_POST['town']);
    $langue             = sanitize_text_field($_POST['langue']);
    $learn_town         = sanitize_text_field($_POST['learn_town']);
    $profession_txt     = sanitize_text_field($_POST['profession_txt']);
    $otherlangname      = sanitize_text_field($_POST['otherlangname']);
    $otherspokenlangs   = sanitize_text_field($_POST['otherspokenlangs']);

    $profession_id          = intval($_POST['profession_id']);
    $learn_town_citycode    = sanitize_text_field($_POST['learn_town_citycode']);
    $birthplace_citycode    = sanitize_text_field($_POST['birthplace_citycode']);
    $town_citycode          = sanitize_text_field($_POST['town_citycode']);
    $learn_town_lat         = sanitize_text_field($_POST['learn_town_lat']);
    $birthplace_lat         = sanitize_text_field($_POST['birthplace_lat']);
    $town_lat               = sanitize_text_field($_POST['town_lat']);
    $learn_town_lng         = sanitize_text_field($_POST['learn_town_lng']);
    $birthplace_lng         = sanitize_text_field($_POST['birthplace_lng']);
    $town_lng               = sanitize_text_field($_POST['town_lng']);

    // Vérifications minimales côté serveur
    if (empty($email) || !is_email($email)) {
        wp_send_json_error("Adresse email invalide.");
    }

    if (email_exists($email)) {
        wp_send_json_error("Un utilisateur avec cette adresse email existe déjà.");
    }

    // Génération d’un mot de passe sécurisé
    $password = wp_generate_password();

    // Création de l'utilisateur
    $user_data = array(
        'user_login'    => $email,
        'user_email'    => $email,
        'user_pass'     => $password,
        'display_name'  => $email,
        'user_nicename' => sanitize_title($email),
        'role'          => 'subscriber',
    );

    $user_id = wp_insert_user($user_data);

    if (is_wp_error($user_id)) {
        $error_message = $user_id->get_error_message();
        wp_send_json_error("Erreur lors de la création du compte : $error_message");
    }

    // Metas personnalisées
    $user_metas = [
        'birthdate'             => $birthdate,
        'sexe'                  => $sexe,
        'birthplace'            => $birthplace,
        'town'                  => $town,
        'langue'                => $langue,
        'learn_town'            => $learn_town,
        'profession_id'         => $profession_id,
        'profession_txt'        => $profession_txt,
        'otherlangname'         => $otherlangname,
        'otherspokenlangs'      => $otherspokenlangs,
        'learn_town_citycode'   => $learn_town_citycode,
        'birthplace_citycode'   => $birthplace_citycode,
        'town_citycode'         => $town_citycode,
        'learn_town_lat'        => $learn_town_lat,
        'learn_town_lng'        => $learn_town_lng,
        'birthplace_lat'        => $birthplace_lat,
        'birthplace_lng'        => $birthplace_lng,
        'town_lat'              => $town_lat,
        'town_lng'              => $town_lng,
    ];

    foreach ($user_metas as $key => $value) {
        update_user_meta($user_id, $key, $value);
    }

    // Mise à jour du user_login et du display_name avec un format personnalisé
    $usercode = 'PaRL_' . $learn_town_citycode . '_' . $user_id;

    global $wpdb;
    $wpdb->update(
        $wpdb->users,
        ['user_login' => $usercode],
        ['ID' => $user_id]
    );

    wp_update_user([
        'ID'            => $user_id,
        'user_nicename' => $usercode,
        'display_name'  => $usercode,
        'nickname'      => $usercode
    ]);

    // Email de confirmation
    $options = get_option('parl_options');
    $email_head   = isset($options['parl_email_success']) ? wpautop($options['parl_email_success']) : 'Bonjour.<br>Vous avez créé un compte sur le site des enquêtes PaRL.';
    $email_footer = isset($options['parl_email_success_footer']) ? wpautop($options['parl_email_success_footer']) : '<br>A bientôt.';

    $to      = $email;
    $subject = 'Enquêtes PaRL';
    $body    = $email_head . '<br>Votre login = ' . $email . '<br>Votre mot de passe = ' . $password . '<br>' . $email_footer;
    $headers = ['Content-Type: text/html; charset=UTF-8'];

    wp_mail($to, $subject, $body, $headers);

    wp_send_json_success('Un compte a été créé pour vous et un email vous a été envoyé.');
}



/*
add_action('wp_ajax_register_user_front_end', 'register_user_front_end', 0);
add_action('wp_ajax_nopriv_register_user_front_end', 'register_user_front_end');

function register_user_front_end() {
	
	
    // Récupération et nettoyage des données du formulaire
    $email = sanitize_email($_POST['email']);
    $birthdate = sanitize_text_field($_POST['birthdate']);
    $sexe = sanitize_text_field($_POST['sexe']);
    $birthplace = sanitize_text_field($_POST['birthplace']);
    $town = sanitize_text_field($_POST['town']);
    $langue = sanitize_text_field($_POST['langue']);
    $learn_town = sanitize_text_field($_POST['learn_town']);
    $profession_txt = sanitize_text_field($_POST['profession_txt']);
	
	$otherlangname = sanitize_text_field($_POST['otherlangname']);
	$otherspokenlangs = sanitize_text_field($_POST['otherspokenlangs']);
	
    $profession_id = intval($_POST['profession_id']);
    
    $learn_town_citycode = sanitize_text_field($_POST['learn_town_citycode']);
    $birthplace_citycode = sanitize_text_field($_POST['birthplace_citycode']);
    $town_citycode = sanitize_text_field($_POST['town_citycode']);
    
    $learn_town_lat = sanitize_text_field($_POST['learn_town_lat']);
    $birthplace_lat = sanitize_text_field($_POST['birthplace_lat']);
    $town_lat = sanitize_text_field($_POST['town_lat']);
    $learn_town_lng = sanitize_text_field($_POST['learn_town_lng']);
    $birthplace_lng = sanitize_text_field($_POST['birthplace_lng']);
    $town_lng = sanitize_text_field($_POST['town_lng']);
    
    // Vérification de sécurité
    if( !isset( $_POST['nonce'] ) or !wp_verify_nonce( $_POST['nonce'], 'register_user_front_end' ) ) {
        echo( "Vous n’avez pas l’autorisation d’effectuer cette action." );
        die();
    }    
    
    
    // Génération d'un mot de passe aléatoire
    $password = wp_generate_password();

    // Création du tableau des données utilisateur
    $user_data = array(
        'user_login' => $email, // Utiliser l'email comme login
        'user_email' => $email,
        'user_pass' => $password,
        'user_nicename' => strtolower($email),
        'display_name' => $email, // Utiliser l'email comme nom d'affichage
        'role' => 'subscriber'
    );

    // Insertion de l'utilisateur
    $user_id = wp_insert_user($user_data);
    $result = "";
    $success = false;
    if (!is_wp_error($user_id)) {
        // Ajout des métadonnées utilisateur
        update_user_meta($user_id, 'birthdate', $birthdate);
        update_user_meta($user_id, 'sexe', $sexe);
        update_user_meta($user_id, 'birthplace', $birthplace);
        update_user_meta($user_id, 'town', $town);
        update_user_meta($user_id, 'langue', $langue);
        update_user_meta($user_id, 'learn_town', $learn_town);
        update_user_meta($user_id, 'profession_id', $profession_id);
        update_user_meta($user_id, 'profession_txt', $profession_txt);

		update_user_meta($user_id, 'otherlangname', $otherlangname);
		update_user_meta($user_id, 'otherspokenlangs', $otherspokenlangs);
        
        update_user_meta($user_id, 'learn_town_citycode', $learn_town_citycode);
        update_user_meta($user_id, 'birthplace_citycode', $birthplace_citycode);
        update_user_meta($user_id, 'town_citycode', $town_citycode);
        
        update_user_meta($user_id, 'learn_town_lat', $learn_town_lat);
        update_user_meta($user_id, 'birthplace_lat', $birthplace_lat);
        update_user_meta($user_id, 'town_lat', $town_lat);
        update_user_meta($user_id, 'learn_town_lng', $learn_town_lng);
        update_user_meta($user_id, 'birthplace_lng', $birthplace_lng);
        update_user_meta($user_id, 'town_lng', $town_lng);
        
        
        //Format user_login, user_nicename & display_name = PaRL_citycode_ID
        
        $usercode = 'PaRL_'.$learn_town_citycode.'_'.$user_id;
        
        global $wpdb;
        $wpdb->update(
            $wpdb->users, 
            ['user_login' => $usercode], 
            ['ID' => $user_id]
        );
        
        //update_user_meta($user_id, 'nickname', $usercode);
        
        $userdata = array(
            'ID' => $user_id,
            'display_name' => $usercode,
            'nickname' => $usercode,
            'user_nicename' => $usercode
        );

        wp_update_user( $userdata );   
        
        $options = get_option( 'parl_options' );
        $email_head = 'Bonjour.<br>Vous avez créé un compte sur le site des enquêtes PaRL.';
        $email_footer = '<br>A bientôt.';
        
        if( isset($options['parl_email_success']) ){
            $email_head = wpautop($options['parl_email_success']);
        }
        
        if( isset($options['parl_email_success_footer']) ){
            $email_footer = wpautop($options['parl_email_success_footer']);
        }
        
        $to = $email;
        $subject = 'Enquetes PARL';
        $body = $email_head.'<br>Votre login = '.$email.'<br>Votre mot de passe = '.$password.'<br>'.$email_footer;
        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail( $to, $subject, $body, $headers );  
        
        $result = 'Un compte a été créé pour vous et un email vous a été envoyé.';
        $success = true;
    } else {
        $result = 'Une erreur est survenue. Veuillez remplir soigneusement le formulaire d\'inscription.';
        $success = false;
        if ( is_wp_error( $user_id ) ) {
            $error_code = array_key_first( $user_id->errors );
            $result = $user_id->errors[$error_code][0];
        }     

    }
    wp_send_json(array( "success" => $success, "result" => $result ));
}
*/
?>