<?php
add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=sondages',   // Parent : menu "Sondages"
        'Réponses aux sondages',         // Titre de la page
        'Réponses',                      // Titre du menu
        'manage_options',                // Capacité requise
        'reponses-sondages',            // Slug de la page
        'render_reponses_sondages_page' // Callback d'affichage
    );
});

function render_reponses_sondages_page() {
	
    echo '<div class="wrap"><h1>Réponses aux sondages</h1>';
	
	echo '<form method="post" action="' . admin_url('admin-post.php') . '" style="margin-bottom:1em;">';
	echo '<input type="hidden" name="action" value="export_sondages_csv">';
	echo '<input type="submit" class="button button-primary" value="Exporter en CSV">';
	echo '</form>';


    $users = get_users();
    $sondages = get_posts([
        'post_type' => 'sondages',
        'post_status' => 'publish',
        'numberposts' => -1,
    ]);

    // On indexe les titres des sondages
    $sondage_titles = [];
    foreach ($sondages as $s) {
        $sondage_titles[$s->ID] = $s->post_title;
    }

    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Utilisateur</th><th>Sondage</th><th>Réponses</th></tr></thead>';
    echo '<tbody>';

    foreach ($users as $user) {
        $meta = get_user_meta($user->ID, 'reponses_sondages', true);
        if (!is_array($meta) || empty($meta)) continue;

        foreach ($meta as $sondage_id => $reponses) {
            echo '<tr>';
            echo '<td>' . esc_html($user->display_name) . ' (' . esc_html($user->user_email) . ')</td>';
            echo '<td><strong>' . esc_html($sondage_titles[$sondage_id] ?? 'Sondage #' . $sondage_id) . '</strong></td>';

            echo '<td><ul style="margin:0;">';
            foreach ($reponses as $label => $value) {
                if (is_array($value)) {
                    echo '<li><strong>' . esc_html($label) . ':</strong> ' . esc_html(implode(', ', $value)) . '</li>';
                } else {
                    echo '<li><strong>' . esc_html($label) . ':</strong> ' . esc_html($value) . '</li>';
                }
            }
            echo '</ul></td>';

            echo '</tr>';
        }
    }

    echo '</tbody></table>';
    echo '</div>';
}

add_action('admin_post_export_sondages_csv', 'export_sondages_csv');


function export_sondages_csv() {
    $filename = 'reponses_sondages_' . date('Y-m-d_H-i-s') . '.csv';

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // BOM UTF-8 pour Excel
    fwrite($output, "\xEF\xBB\xBF");

    // En-tête CSV
    fputcsv($output, ['Utilisateur', 'Email', 'Sondage', 'Question', 'Réponse']);

    $users = get_users();
    $sondages = get_posts([
        'post_type' => 'sondages',
        'post_status' => 'publish',
        'numberposts' => -1,
    ]);

    $sondage_titles = [];
    foreach ($sondages as $s) {
        $sondage_titles[$s->ID] = $s->post_title;
    }

    foreach ($users as $user) {
        $meta = get_user_meta($user->ID, 'reponses_sondages', true);
        if (!is_array($meta) || empty($meta)) continue;

        foreach ($meta as $sondage_id => $reponses) {
            $sondage_title = $sondage_titles[$sondage_id] ?? 'Sondage #' . $sondage_id;

            foreach ($reponses as $question => $reponse) {
                if (is_array($reponse)) {
                    $reponse = implode(', ', $reponse);
                }
                fputcsv($output, [
                    $user->display_name,
                    $user->user_email,
                    $sondage_title,
                    $question,
                    $reponse
                ]);
            }
        }
    }

    fclose($output);
}

?>