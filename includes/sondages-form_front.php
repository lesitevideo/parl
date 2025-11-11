<?php


add_action('wp_ajax_enregistrer_reponses_sondage', 'enregistrer_reponses_sondage');
/*
function enregistrer_reponses_sondage() {
    // 1. Vérif nonce
    if (
        !isset($_POST['nonce']) ||
        !wp_verify_nonce($_POST['nonce'], 'sondage_nonce')
    ) {
        wp_send_json_error('Nonce invalide');
    }

    // 2. Vérif user connecté
    if (!is_user_logged_in()) {
        wp_send_json_error('Utilisateur non connecté');
    }

    $user_id = get_current_user_id();

    // 3. Récupération du sondage ID
    $post_id = isset($_POST['sondage_id']) ? intval($_POST['sondage_id']) : 0;
    if (!$post_id || get_post_type($post_id) !== 'sondages') {
        wp_send_json_error('ID de sondage invalide');
    }

    // 4. Récupération des réponses soumises
    $reponses_brutes = isset($_POST['reponses']) ? json_decode(stripslashes($_POST['reponses']), true) : [];

    if (!is_array($reponses_brutes)) {
        wp_send_json_error('Réponses invalides');
    }

    // 5. Récupération de la structure du sondage pour faire correspondre les labels
    $structure = get_post_meta($post_id, '_form_fields', true);
    $structure = json_decode($structure, true);

    if (!is_array($structure)) {
        wp_send_json_error('Structure du sondage introuvable');
    }

    // 6. Association champ -> label
    $reponses_formatees = [];
    foreach ($structure as $i => $field) {
        $key = 'field_' . $i;
        $label = $field['label'] ?? $key;
        if (isset($reponses_brutes[$key])) {
            $reponses_formatees[$label] = $reponses_brutes[$key];
        }
    }

    // 7. Sauvegarde dans une meta unique "reponses_sondages" sous forme d'objet
    $meta_key = 'reponses_sondages';
    $meta = get_user_meta($user_id, $meta_key, true);

    if (!is_array($meta)) $meta = [];

    // On enregistre les réponses par ID de sondage
    $meta[$post_id] = $reponses_formatees;

    update_user_meta($user_id, $meta_key, $meta);

    wp_send_json_success();
}
*/
function enregistrer_reponses_sondage() {
    // 1) Sécurité de base
    if (empty($_POST['sondage_nonce']) || !wp_verify_nonce($_POST['sondage_nonce'], 'sondage_nonce')) {
        wp_send_json_error(['message' => 'Nonce invalide.'], 403);
    }

    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'Veuillez vous connecter pour enregistrer ce sondage.'], 401);
    }

    $post_id = isset($_POST['sondage_id']) ? (int) $_POST['sondage_id'] : 0;
    if (!$post_id || !get_post($post_id)) {
        wp_send_json_error(['message' => 'Sondage introuvable.'], 400);
    }

    // 2) Mode: draft (par défaut) ou final
    $mode = isset($_POST['mode']) ? sanitize_text_field(wp_unslash($_POST['mode'])) : 'draft';
    if ($mode !== 'draft' && $mode !== 'final') {
        $mode = 'draft';
    }

    // 3) Bloquer si déjà soumis (lock)
    $lock_key = "sondage_{$post_id}_locked";
    $is_locked = get_user_meta($user_id, $lock_key, true);
    if ($is_locked) {
        wp_send_json_error(['message' => 'Sondage déjà soumis définitivement.'], 409);
    }

    // 4) Récupération et sanitation des réponses
    $raw = isset($_POST['reponses']) ? wp_unslash($_POST['reponses']) : '';
    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        $decoded = [];
    }

    $reponses_sanitized = kinoki_sanitize_deep($decoded);

    // 5) Merge dans user_meta reponses_sondages
    $all = get_user_meta($user_id, 'reponses_sondages', true);
    if (!is_array($all)) {
        $all = [];
    }
    $all[$post_id] = $reponses_sanitized;

    $ok = update_user_meta($user_id, 'reponses_sondages', $all);
    if (!$ok) {
        // Même si WP renvoie false si la valeur n'a pas changé, on considère que c'est OK si tout est identique
        // On ne bloque pas pour autant.
    }

    // 6) Si final => poser le lock
    $locked_now = false;
    if ($mode === 'final') {
        update_user_meta($user_id, $lock_key, true);
        $locked_now = true;
    }

    // 7) Réponse
    wp_send_json_success([
        'locked' => $locked_now,
        'mode'   => $mode,
        'post_id'=> $post_id,
    ]);
}

/**
 * Sanitize récursif des données (texte/numérique/tableaux).
 * - Texte: sanitize_text_field
 * - Booléens/numeriques: cast léger
 * - Tableaux: map récursif
 */
function kinoki_sanitize_deep($value) {
    if (is_array($value)) {
        $out = [];
        foreach ($value as $k => $v) {
            // NE PAS sanitizer les clés (on garde exactement les name du formulaire)
            $out[$k] = kinoki_sanitize_deep($v);
        }
        return $out;
    }
    if (is_bool($value)) return (bool) $value;
    if (is_numeric($value)) return $value + 0;
    if (is_string($value)) return sanitize_text_field($value);
    return '';
}


?>