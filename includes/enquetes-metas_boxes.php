<?php

/* questions meta box */
function add_questions_meta_box() {
    add_meta_box(
        'questions_meta_box',
        'Questions',
        'render_questions_meta_box',
        'enquetes',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_questions_meta_box');

function render_questions_meta_box($post) {
    $questions = get_post_meta($post->ID, 'questions', true);
    $questions = is_array($questions) ? $questions : [];
    wp_nonce_field(basename(__FILE__), 'questions_meta_box_nonce');
    ?>
    <div id="questions-container">
        <?php foreach ($questions as $index => $question) : ?>
            <div class="question-item">
                <input style="width:80%" type="text" name="questions[]" value="<?php echo esc_attr($question); ?>" />
                <button type="button" class="remove-question">Supprimer</button>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" id="add-question">Ajouter une question</button>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var container = document.getElementById('questions-container');
            var addQuestionButton = document.getElementById('add-question');
            addQuestionButton.addEventListener('click', function() {
                var div = document.createElement('div');
                div.className = 'question-item';
                div.innerHTML = '<input type="text" name="questions[]" value="" /> <button type="button" class="remove-question">Supprimer</button>';
                container.appendChild(div);
            });
            container.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-question')) {
                    e.target.parentNode.remove();
                }
            });
        });
    </script>
    <style>
        .question-item {
            margin-bottom: 10px;
        }
        .question-item input {
            margin-right: 10px;
        }
    </style>
    <?php
}

function save_questions_meta_box($post_id) {
    if (!isset($_POST['questions_meta_box_nonce']) || !wp_verify_nonce($_POST['questions_meta_box_nonce'], basename(__FILE__))) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['questions'])) {
        $questions = array_map('sanitize_text_field', $_POST['questions']);
        update_post_meta($post_id, 'questions', $questions);
    } else {
        delete_post_meta($post_id, 'questions');
    }
}
add_action('save_post', 'save_questions_meta_box');

?>