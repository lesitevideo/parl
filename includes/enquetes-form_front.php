<?php

//fonctions pour les formulaires en front
function show_mp3_url_to_blob($url){
    if($url !== null && $url !== '' && isset($url)){
        // Téléchargement du contenu du fichier audio
        $audioContent = file_get_contents($url);

        // Vérifier si le téléchargement a réussi
        if ($audioContent === FALSE) {
            die('Erreur lors du téléchargement du fichier audio.');
        }

        // Conversion du contenu audio en Base64
        $base64Audio = base64_encode($audioContent);

        // Création de l'URL Base64
        $base64Url = 'data:audio/mp3;base64,' . $base64Audio;    
        echo $base64Url;
    } else {
        echo '';
    }
}

function audio_data_depasse_taille_max($audio_data) {
    // Taille maximale autorisée en Ko (20 Ko)
    $taille_max_ko = 10;

    // Convertir la taille maximale en octets
    $taille_max_octets = $taille_max_ko * 1024;

    // Obtenez la taille de l'audio_data
    $taille_audio_data = strlen($audio_data);

    // Vérifier si la taille de l'audio_data dépasse la taille maximale autorisée
    if ($taille_audio_data > $taille_max_octets) {
        // L'audio_data dépasse la taille maximale autorisée
        return true;
    } else {
        // L'audio_data respecte la taille maximale autorisée
        return false;
    }
}



// traiter formulaires
function save_enquete_responses() {
    if (isset($_POST['enquete_id']) && isset($_POST['questions']) && isset($_POST['action']) && $_POST['action'] == 'save_enquete_responses' && is_user_logged_in()) {
        $user_id = get_current_user_id();
        $enquete_id = sanitize_text_field($_POST['enquete_id']);
        $enquete_url = get_permalink(intval($enquete_id));
        $enquete_slugged = sanitize_title(get_the_title(intval($enquete_id)));
        $questions = $_POST['questions'];
        $responses = $_POST['responses'];
        $comments = isset($_POST['comments']) ? $_POST['comments'] : [];
        $audio_blobs = isset($_POST['audio_blobs']) ? $_POST['audio_blobs'] : [];
        
		$locked = sanitize_text_field($_POST['locked']);
		
        $stored_responses = get_user_meta($user_id, 'enquete_' . $enquete_id . '_responses', true);
        $saved_responses = [];

        if( $_POST['action'] == 'save_enquete_responses' ){
            //print_r($_POST);
            //print_r($stored_responses);
            //die();
        }
        
        
        foreach ($questions as $index => $question) {
            if( isset($stored_responses[$index]) ){
                if( isset($stored_responses[$index]['audio_url']) && isset($stored_responses[$index]['audio_url']) != "" ){
                    $audio_url = $stored_responses[$index]['audio_url'];
                } else {
                    $audio_url = '';
                }
            } else {
                $audio_url = '';
            }
            
            if (!empty($audio_blobs[$index])) {
                // Décoder le base64
                $audio_data = base64_decode(str_replace('data:audio/mpeg-3;base64,', '', $audio_blobs[$index]));
                
                if (audio_data_depasse_taille_max($audio_data)) {
                    // Créer un fichier unique
                    $upload_dir = wp_upload_dir();
                    //$file_name = 'audio_u_' . $user_id . '_e_' . $enquete_id . '_q_' . ($index + 1) . '.mp3';
                    $file_name = 'audio_u_' . $user_id . '_e_' . $enquete_slugged . '_q_' . ($index + 1) . '.mp3';
                    $file_path = $upload_dir['path'] . '/' . $file_name;
                    $filetype = wp_check_filetype($file_path);

                    if (!file_exists($file_path)) {

                        file_put_contents($file_path, $audio_data);

                        // Préparer les données de l'attachement
                        $attachment = [
                            'guid'           => $upload_dir['url'] . '/' . $file_name,
                            'post_mime_type' => $filetype['type'],
                            'post_title'     => sanitize_file_name($file_name),
                            'post_content'   => '',
                            'post_status'    => 'inherit'
                        ];

                        // Insérer l'attachement dans la base de données
                        $attach_id = wp_insert_attachment($attachment, $file_path, intval($enquete_id));

                        // Générer les métadonnées des attachements
                        require_once(ABSPATH . 'wp-admin/includes/image.php');
                        require_once(ABSPATH . 'wp-admin/includes/media.php');
                        $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
                        wp_update_attachment_metadata($attach_id, $attach_data);

                        $audio_url = $upload_dir['url'] . '/' . $file_name;

                    } else {
                        file_put_contents($file_path, $audio_data);

                        $audio_url = $upload_dir['url'] . '/' . $file_name;

                    }
                }
            }

            $saved_responses[] = [
                'question' => sanitize_text_field($question),
                'response' => sanitize_text_field($responses[$index]),
                'comment' => isset($comments[$index]) ? sanitize_text_field($comments[$index]) : '',
                'audio_url' => $audio_url
            ];
            
        }
		//if( $locked === "locked" ){
		update_user_meta($user_id, 'enquete_' . $enquete_id . '_locked', $locked);
		//}
        
		
        update_user_meta($user_id, 'enquete_' . $enquete_id . '_responses', $saved_responses);

        // Rediriger ou afficher un message de succès
        wp_redirect($enquete_url);
        exit;
    }
}


function handle_form_submission() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_enquete_responses' && is_user_logged_in()) {
        save_enquete_responses();
    }
}
add_action('init', 'handle_form_submission');


?>