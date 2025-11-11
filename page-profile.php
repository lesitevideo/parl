<?php
/* Template Name: User Profile */

get_header();

?>
<div class="container">
<?php

if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $user_info = get_userdata($user_id);
    ?>

    <h1>Profil de <?php echo esc_html($user_info->user_email); ?></h1>

    <h2>Vos réponses aux enquêtes</h2>

    <?php
    // Récupérer tous les custom post types 'enquetes'
    $args = array(
        'post_type' => 'enquetes',
        'posts_per_page' => -1,
    );
    $enquetes = new WP_Query($args);

    if ($enquetes->have_posts()) :
        while ($enquetes->have_posts()) : $enquetes->the_post();
            $enquete_id = get_the_ID();
            $questions = get_post_meta($enquete_id, 'questions', true);
            $responses = get_user_meta($user_id, 'enquete_' . $enquete_id . '_responses', true);
            //print_r($responses);
            ?>

            <div class="enquete-responses profil_list">
                <h3><?php the_title(); ?></h3>
                <?php if ($responses) : ?>
                    <ul>
                        <?php foreach ($responses as $response) : ?>
                            <li>
                                <strong>Question :</strong> <?php echo esc_html($response['question']); ?><br>
                                <strong>Réponse :</strong> <?php echo esc_html($response['response']); ?><br>
                                <strong>Commentaire :</strong> <?php echo esc_html($response['comment']); ?><br>
                                <strong>Audio :</strong><br><audio src="<?php echo esc_html($response['audio_url']); ?>" class="" controls></audio>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else : ?>
                    <p>Vous n'avez pas encore répondu à cette enquête.</p>
                <?php endif; ?>
            </div>

            <?php
        endwhile;
    else :
        ?>
        <p>Aucune enquête trouvée.</p>
    <?php
    endif;
    wp_reset_postdata();
} else {
    ?>
    <p>Vous devez être connecté pour voir votre profil.</p>
    <?php
}
?>
</div>
<?php
get_footer();
