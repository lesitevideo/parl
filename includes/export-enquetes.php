<?php

function export_single_enquete() {
    // Vérification de nonce pour la sécurité
    check_ajax_referer('update_enquete_status_nonce');

    // Vérification des permissions et récupération des valeurs
    if (!isset($_POST['enquete_id'])) {
        wp_send_json_error(['message' => 'Données manquantes']);
    }

    $enquete_id = intval($_POST['enquete_id']); // ID de l'enquete à exporter

    $enquete = get_post( $enquete_id ); 
    global $wpdb;

    // Préparer la requête SQL
    $meta_key_pattern = 'enquete_'.$enquete->ID.'_responses';
    $query = $wpdb->prepare("
        SELECT DISTINCT user_id 
        FROM {$wpdb->usermeta} 
        WHERE meta_key = %s
    ", $meta_key_pattern);

    // Exécuter la requête
    $user_ids = $wpdb->get_col($query);  
    sort($user_ids);	
	
	$csv_data = generate_csv_enquetes($user_ids, $enquete_id);
	
    wp_send_json_success([ 'message' => 'OK', 'csv' => $csv_data ]);
}
add_action('wp_ajax_export_single_enquete', 'export_single_enquete');



function generate_csv_enquetes($user_ids, $enquete_id) {
    // Récupérer les options des champs à inclure
    $options = get_option('parl_options');
    $show_in_csv = isset($options['show_in_csv']) ? $options['show_in_csv'] : [];

    // Définir les champs utilisateur disponibles (sans user_id)
    $user_meta_fields = [
        'user_login' => 'Utilisateur Login',
        'birthdate' => 'Date de naissance',
        'sexe' => 'Sexe',
        'birthplace' => 'Lieu de naissance',
        'town' => 'Ville',
        'langue' => 'Langue',
        'learn_town' => 'Ville d\'apprentissage',
        'profession_id' => 'ID Profession',
        'profession_txt' => 'Profession',
        'otherlangname' => 'Autre langue',
        'otherspokenlangs' => 'Langues parlées',
        'learn_town_citycode' => 'Code ville d\'apprentissage',
        'learn_town_lat' => 'Latitude ville d\'apprentissage',
        'learn_town_lng' => 'Longitude ville d\'apprentissage',
        'birthplace_citycode' => 'Code ville naissance',
        'birthplace_lat' => 'Latitude ville naissance',
        'birthplace_lng' => 'Longitude ville naissance',
        'town_citycode' => 'Code ville',
        'town_lat' => 'Latitude ville',
        'town_lng' => 'Longitude ville'
    ];

    // Construire l'en-tête CSV
    $csv_headers = [];
    if (isset($show_in_csv['user_id'])) {
        $csv_headers[] = 'Utilisateur ID';
    }

    foreach ($user_meta_fields as $meta_key => $label) {
        if (isset($show_in_csv[$meta_key])) {
            $csv_headers[] = $label;
        }
    }

    // Ajouter les colonnes fixes
    $csv_headers = array_merge($csv_headers, ['Enquête ID', 'Titre Enquête', 'Question', 'Réponse', 'Commentaire', 'Audio URL']);

    // Ouvre un buffer de sortie
    ob_start();
    $output = fopen('php://output', 'w');

    // Écrire l'en-tête
    fputcsv($output, $csv_headers);

    // Générer les lignes
    foreach ($user_ids as $user_id) {
        $current_user_data = get_userdata($user_id);
        
        // Collecte des données utilisateur
        $user_data = [];
        if (isset($show_in_csv['user_id'])) {
            $user_data[] = $user_id;
        }

        foreach ($user_meta_fields as $meta_key => $label) {
            if (isset($show_in_csv[$meta_key])) {
                if ($meta_key === 'user_login') {
                    $user_data[] = $current_user_data->user_login;
                } else {
                    $user_data[] = get_user_meta($user_id, $meta_key, true) ?: 'N/A';
                }
            }
        }

        // Récupération des réponses aux enquêtes
        $user_enquetes = get_user_enquete($user_id, $enquete_id);
        if (!isset($user_enquetes) || empty($user_enquetes)) {
            fputcsv($output, array_merge($user_data, ['', 'Pas de réponses', '', '', '', '']));
            continue;
        }

        foreach ($user_enquetes as $enquete_index => $user_enquete) {
            $enquete_id = extract_enquete_id($enquete_index);
            $enquete_datas = get_post($enquete_id);
            $enquete_title = $enquete_datas->post_title;

            foreach ($user_enquete as $question) {
                fputcsv($output, array_merge($user_data, [
                    $enquete_id,
                    $enquete_title,
                    $question['question'],
                    $question['response'],
                    $question['comment'],
                    isset($question['audio_url']) ? $question['audio_url'] : 'Pas d\'audio'
                ]));
            }
        }
    }

    fclose($output);

    // Récupère le contenu du buffer et le retourne
    return ob_get_clean();
}



/*
function generate_csv_enquetes($user_ids, $enquete_id) {
    // Ouvre un buffer de sortie
    ob_start();
    $output = fopen('php://output', 'w');

    // En-tête du tableau CSV
    fputcsv($output, ['Utilisateur ID', 'Utilisateur Email', 'Enquête ID', 'Titre Enquête', 'Question', 'Réponse', 'Commentaire', 'Audio URL']);

    // Remplace ceci par la récupération de tes utilisateurs
    foreach ($user_ids as $user_id) {
        $current_user_data = get_userdata($user_id);
        $user_email = $current_user_data->user_email;

        $user_enquetes = get_user_enquete($user_id, $enquete_id);

        if (!isset($user_enquetes) || empty($user_enquetes)) {
            fputcsv($output, [$user_id, $user_email, '', "Pas de réponses", '', '', '', '']);
            continue;
        }

        foreach ($user_enquetes as $enquete_index => $user_enquete) {
            $enquete_id = extract_enquete_id($enquete_index);
            $enquete_datas = get_post($enquete_id);
            $enquete_title = $enquete_datas->post_title;

            foreach ($user_enquete as $index => $question) {
                fputcsv($output, [
                    $user_id,
                    $user_email,
                    $enquete_id,
                    $enquete_title,
                    $question['question'],
                    $question['response'],
                    $question['comment'],
                    isset($question['audio_url']) ? $question['audio_url'] : 'Pas d\'audio'
                ]);
            }
        }
    }

    fclose($output);

    // Récupère le contenu du buffer et le retourne
    return ob_get_clean();
}
*/
?>