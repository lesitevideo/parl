<?php

add_action('add_meta_boxes', function () {
    add_meta_box(
        'sondage_form_fields',
        'Champs du formulaire',
        'render_sondage_form_fields_metabox',
        'sondages',
        'normal',
        'default'
    );
});


function render_sondage_form_fields_metabox($post) {
    $form_data = get_post_meta($post->ID, '_form_fields', true);
    $fields = !empty($form_data) ? json_decode($form_data, true) : [];

    echo '<div id="form-fields-container"></div>';
    echo '<button type="button" id="add-field" class="button">Ajouter un champ</button>';
    echo '<input type="hidden" name="form_fields_data" id="form_fields_data" value="' . esc_attr($form_data) . '">';

    // Templates et script JS
    ?>
    <template id="field-template">
        <div class="form-field" data-index="">
            <select class="field-type">
                <option value="text">Texte</option>
                <option value="textarea">Zone de texte</option>
                <option value="select">Liste déroulante</option>
                <option value="checkbox">Cases à cocher</option>
                <option value="radio">Boutons radio</option>
            </select>
            <input type="text" class="field-label" placeholder="Label">
            <div class="field-options" style="display:none;">
                <input type="text" class="option-input" placeholder="Option 1">
                <input type="text" class="option-input" placeholder="Option 2">
                <button type="button" class="add-option button">+</button>
            </div>
            <button type="button" class="remove-field button">Supprimer</button>
            <hr>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('form-fields-container');
            const template = document.getElementById('field-template');
            const dataInput = document.getElementById('form_fields_data');
            let fields = [];

            try {
                fields = JSON.parse(dataInput.value || '[]');
            } catch(e) {}

            function renderFields() {
                container.innerHTML = '';
                fields.forEach((field, index) => {
                    const clone = template.content.cloneNode(true);
                    const el = clone.querySelector('.form-field');
                    el.dataset.index = index;
                    el.querySelector('.field-type').value = field.type;
                    el.querySelector('.field-label').value = field.label;

                    const optionsDiv = el.querySelector('.field-options');
                    if (['select', 'checkbox', 'radio'].includes(field.type)) {
                        optionsDiv.style.display = 'block';
                        optionsDiv.innerHTML = '';
                        (field.options || []).forEach(opt => {
                            const input = document.createElement('input');
                            input.type = 'text';
                            input.className = 'option-input';
                            input.value = opt;
                            optionsDiv.appendChild(input);
                        });
                        const addBtn = document.createElement('button');
                        addBtn.type = 'button';
                        addBtn.className = 'add-option button';
                        addBtn.textContent = '+';
                        optionsDiv.appendChild(addBtn);
                    }

                    el.querySelector('.field-type').addEventListener('change', (e) => {
                        const type = e.target.value;
                        fields[index].type = type;
                        if (['select', 'checkbox', 'radio'].includes(type)) {
                            optionsDiv.style.display = 'block';
                            if (!fields[index].options) fields[index].options = ['Option 1', 'Option 2'];
                        } else {
                            optionsDiv.style.display = 'none';
                            delete fields[index].options;
                        }
                        updateData();
                        renderFields();
                    });

                    el.querySelector('.field-label').addEventListener('input', (e) => {
                        fields[index].label = e.target.value;
                        updateData();
                    });

                    el.querySelector('.remove-field').addEventListener('click', () => {
                        fields.splice(index, 1);
                        updateData();
                        renderFields();
                    });

                    optionsDiv?.addEventListener('input', () => {
                        const opts = Array.from(optionsDiv.querySelectorAll('.option-input')).map(i => i.value);
                        fields[index].options = opts;
                        updateData();
                    });

                    optionsDiv?.querySelector('.add-option')?.addEventListener('click', (e) => {
                        e.preventDefault();
                        const input = document.createElement('input');
                        input.type = 'text';
                        input.className = 'option-input';
                        input.placeholder = 'Nouvelle option';
                        optionsDiv.insertBefore(input, e.target);
                        fields[index].options.push('');
                        updateData();
                    });

                    container.appendChild(el);
                });
            }

            function updateData() {
                dataInput.value = JSON.stringify(fields);
            }

            document.getElementById('add-field').addEventListener('click', () => {
                fields.push({ type: 'text', label: '' });
                updateData();
                renderFields();
            });

            renderFields();
        });
    </script>
    <style>
        .form-field { margin-bottom: 1em; }
        .option-input { display: block; margin: 2px 0; }
    </style>
    <?php
}


add_action('save_post_sondages', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['form_fields_data'])) {
        update_post_meta($post_id, '_form_fields', wp_unslash($_POST['form_fields_data']));
    }
});
