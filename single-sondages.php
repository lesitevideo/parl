<?php
/**
 * Template: single-sondages.php
 */
get_header();

if (have_posts()) :
  while (have_posts()) : the_post();

    $post_id = get_the_ID();
    $form_data = get_post_meta($post_id, '_form_fields', true);
    $fields = json_decode($form_data, true);

    // Helper: valeur enregistrée (gère name, name[], et fallback sanitized)
    function sondage_saved_value(array $saved, string $name) {
        if (array_key_exists($name, $saved)) return $saved[$name];
        $base = preg_replace('/\[\]$/', '', $name);
        if ($base !== $name && array_key_exists($base, $saved)) return $saved[$base];
        $san  = sanitize_key($name);
        if (array_key_exists($san, $saved)) return $saved[$san];
        $sanBase = sanitize_key($base);
        if (array_key_exists($sanBase, $saved)) return $saved[$sanBase];
        return null;
    }

    $current_user_id = get_current_user_id();
    $reponses_sauvegardees = [];
    $is_locked = false;

    if ($current_user_id) {
        $meta_all = get_user_meta($current_user_id, 'reponses_sondages', true);
        if (is_array($meta_all) && isset($meta_all[$post_id])) {
            $reponses_sauvegardees = $meta_all[$post_id];
        }
        $is_locked = (bool) get_user_meta($current_user_id, "sondage_{$post_id}_locked", true);
    }
    ?>

    <main class="container my-5">
      <article <?php post_class(); ?>>
        <header class="mb-4">
          <h1 class="entry-title"><?php echo esc_html(get_the_title()); ?></h1>
          <?php if (has_excerpt()) : ?>
            <p class="lead"><?php echo esc_html(get_the_excerpt()); ?></p>
          <?php endif; ?>
        </header>

        <div class="entry-content mb-4">
          <?php the_content(); ?>
        </div>

        <?php if (!is_user_logged_in()) : ?>
          <div class="alert alert-warning">
            Vous devez être connecté pour répondre à ce sondage.
          </div>
          <?php
            // Lien de connexion
            $login_url = wp_login_url(get_permalink());
          ?>
          <p><a class="btn btn-primary" href="<?php echo esc_url($login_url); ?>">Se connecter</a></p>

        <?php elseif (!is_array($fields) || empty($fields)) : ?>
          <div class="alert alert-secondary">
            Aucun champ de sondage n’a été configuré pour ce contenu.
          </div>

        <?php elseif ($is_locked) : ?>
          <h2 class="text-center my-4">Vous avez déjà répondu à ce sondage.</h2>

        <?php else : ?>
          <?php
            // Si un brouillon existe, on peut afficher une petite info
            if (is_array($reponses_sauvegardees) && !empty($reponses_sauvegardees)) {
              echo '<div class="alert alert-info">Des réponses enregistrées ont été retrouvées et préremplies.</div>';
            }

            $nonce = wp_create_nonce('sondage_nonce');
          ?>

          <form id="sondage-formulaire" class="sondage-form" method="post" action="#">
            <input type="hidden" name="sondage_id" value="<?php echo esc_attr($post_id); ?>">
            <input type="hidden" name="sondage_nonce" value="<?php echo esc_attr($nonce); ?>">

            <?php
            // Rendu des champs
            foreach ($fields as $field) :
              // On tolère différentes conventions de clés
              $type     = isset($field['type']) ? (string) $field['type'] : 'text';
              $name     = isset($field['name']) ? (string) $field['name'] : '';
              $label    = isset($field['label']) ? (string) $field['label'] : (isset($field['title']) ? (string)$field['title'] : $name);
              $required = !empty($field['required']);
              $options  = isset($field['options']) && is_array($field['options']) ? $field['options'] : [];

              if ($name === '') {
                // Fabrique un name à partir du label si absent
                $slug = sanitize_title($label ?: 'champ');
                $name = $slug;
              }

              // Valeur(s) sauvegardée(s)
              $saved = sondage_saved_value($reponses_sauvegardees, $name);

              // ID pour le champ
              $input_id = sanitize_html_class('fld_' . $name);

              // Rendu
              echo '<div class="mb-3">';
              if ($label !== '') {
                echo '<label class="form-label" for="' . esc_attr($input_id) . '">'
                  . esc_html($label)
                  . ($required ? ' *' : '')
                  . '</label>';
              }

              switch ($type) {
                case 'textarea':
                  echo '<textarea class="form-control" id="' . esc_attr($input_id) . '" name="' . esc_attr($name) . '"'
                      . ($required ? ' required' : '')
                      . '>'
                      . (isset($saved) ? esc_textarea((string)$saved) : '')
                      . '</textarea>';
                  break;

                case 'radio':
                  // radios : $options = [ 'val' => 'Label', ... ] ou ['A','B',...]
                  if (!empty($options)) {
                    $current = isset($saved) ? (string)$saved : '';
                    echo '<div>';
                    foreach ($options as $optValue => $optLabel) {
                      // Normalise options si numérotées
                      if (is_int($optValue)) {
                        $optValue = (string) $optLabel;
                      } else {
                        $optValue = (string) $optValue;
                      }
                      $optLabelText = is_array($optLabel) ? (string) reset($optLabel) : (string) $optLabel;
                      $checked = ($current !== '' && (string)$current === $optValue) ? ' checked' : '';
                      $rid = $input_id . '_' . sanitize_html_class($optValue);
                      echo '<div class="form-check">';
                      echo '<input class="form-check-input" type="radio" id="' . esc_attr($rid) . '" name="' . esc_attr($name) . '" value="' . esc_attr($optValue) . '"'
                          . $checked
                          . ($required ? ' required' : '')
                          . '>';
                      echo '<label class="form-check-label" for="' . esc_attr($rid) . '">' . esc_html($optLabelText) . '</label>';
                      echo '</div>';
                    }
                    echo '</div>';
                  }
                  break;

                case 'checkbox':
                  // Deux cas :
                  // - case unique (pas d'options) : booléen; value "1"
                  // - cases multiples (avec options) : name="xxx[]" et tableau de valeurs
                  if (!empty($options)) {
                    $arr = is_array($saved) ? array_map('strval', $saved) : (isset($saved) ? [(string)$saved] : []);
                    $nameArr = preg_match('/\[\]$/', $name) ? $name : $name . '[]';
                    echo '<div>';
                    foreach ($options as $optValue => $optLabel) {
                      if (is_int($optValue)) {
                        $optValue = (string) $optLabel;
                      } else {
                        $optValue = (string) $optValue;
                      }
                      $optLabelText = is_array($optLabel) ? (string) reset($optLabel) : (string) $optLabel;
                      $checked = in_array($optValue, $arr, true) ? ' checked' : '';
                      $cid = $input_id . '_' . sanitize_html_class($optValue);
                      echo '<div class="form-check">';
                      echo '<input class="form-check-input" type="checkbox" id="' . esc_attr($cid) . '" name="' . esc_attr($nameArr) . '" value="' . esc_attr($optValue) . '"'
                          . $checked
                          . ($required ? ' required' : '')
                          . '>';
                      echo '<label class="form-check-label" for="' . esc_attr($cid) . '">' . esc_html($optLabelText) . '</label>';
                      echo '</div>';
                    }
                    echo '</div>';
                  } else {
                    // case unique
                    $checked = (!empty($saved) && $saved !== '0') ? ' checked' : '';
                    echo '<div class="form-check">';
                    echo '<input class="form-check-input" type="checkbox" id="' . esc_attr($input_id) . '" name="' . esc_attr($name) . '" value="1"' . $checked . '>';
                    echo '<label class="form-check-label" for="' . esc_attr($input_id) . '">' . esc_html($label ?: 'Oui/Non') . '</label>';
                    echo '</div>';
                  }
                  break;

                case 'select':
                case 'select-one':
                case 'select_multiple':
                case 'select-multiple':
                  $is_multiple = in_array($type, ['select_multiple', 'select-multiple'], true);
                  $nameAttr = $is_multiple && !preg_match('/\[\]$/', $name) ? $name . '[]' : $name;
                  $currentArr = $is_multiple
                                ? (is_array($saved) ? array_map('strval', $saved) : (isset($saved) ? [(string)$saved] : []))
                                : null;
                  echo '<select class="form-select" id="' . esc_attr($input_id) . '" name="' . esc_attr($nameAttr) . '"' . ($is_multiple ? ' multiple' : '') . ($required ? ' required' : '') . '>';
                  if (!$is_multiple) {
                    echo '<option value="">— Sélectionner —</option>';
                  }
                  foreach ($options as $optValue => $optLabel) {
                    if (is_int($optValue)) {
                      $optValue = (string) $optLabel;
                    } else {
                      $optValue = (string) $optValue;
                    }
                    $optLabelText = is_array($optLabel) ? (string) reset($optLabel) : (string) $optLabel;
                    $selected = '';
                    if ($is_multiple) {
                      $selected = in_array($optValue, $currentArr, true) ? ' selected' : '';
                    } else {
                      $current = isset($saved) ? (string)$saved : '';
                      $selected = ((string)$current === $optValue) ? ' selected' : '';
                    }
                    echo '<option value="' . esc_attr($optValue) . '"' . $selected . '>' . esc_html($optLabelText) . '</option>';
                  }
                  echo '</select>';
                  break;

                case 'email':
                case 'number':
                case 'date':
                case 'tel':
                case 'url':
                case 'text':
                default:
                  $value = isset($saved) ? (string)$saved : '';
                  $typeAttr = in_array($type, ['email','number','date','tel','url','text'], true) ? $type : 'text';
                  echo '<input class="form-control" type="' . esc_attr($typeAttr) . '" id="' . esc_attr($input_id) . '" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '"' . ($required ? ' required' : '') . '>';
                  break;
              }

              // Affichage d'une aide éventuelle
              if (!empty($field['help'])) {
                echo '<div class="form-text">' . wp_kses_post($field['help']) . '</div>';
              }

              echo '</div>';
            endforeach;
            ?>

            <!--<div class="d-flex gap-2 mt-4">
              <button type="button" id="btnSuspendreSaisie" class="btn btn-outline-secondary">Suspendre la saisie</button>
              <button type="submit" class="btn btn-primary">Envoyer</button>
            </div>-->
              
            <div class="footer mt-auto py-4 bg-body-tertiary sticky-bottom">
                <div class="container-fluid">
                    <div class="d-flex flex-wrap justify-content-between">
                        <button type="button" id="btnSuspendreSaisie" class="btn btn-warning mx-4 mb-2">Suspendre la saisie</button>
                        <button type="submit" class="btn btn-success mx-4 mb-2">Terminer le questionnaire</button>
                        
                        <!--<input type="submit" class="btn btn-warning mx-4 mb-2" value="Suspendre la saisie">
                        <button id="bt_validate_form" type="button" class="btn btn-success mx-4 mb-2">Terminer le questionnaire</button>-->
                    </div>
                </div>
            </div>  
              
              
              
          </form>

          <script>
          document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('sondage-formulaire');
            const suspendBtn = document.getElementById('btnSuspendreSaisie');
            const ajaxUrl = '<?php echo esc_url(admin_url("admin-ajax.php")); ?>';

            if (!form) return;

            function buildResponsesFromForm(formEl) {
              const fd = new FormData(formEl);
              const out = {};
              fd.forEach((value, key) => {
                if (key === 'sondage_id' || key === 'sondage_nonce') return;
                const baseKey = key.endsWith('[]') ? key.slice(0, -2) : key;
                if (Object.prototype.hasOwnProperty.call(out, baseKey)) {
                  if (Array.isArray(out[baseKey])) {
                    out[baseKey].push(value);
                  } else {
                    out[baseKey] = [out[baseKey], value];
                  }
                } else {
                  out[baseKey] = value;
                }
              });
              return out;
            }

            async function postSondage(mode) {
              const fd = new FormData(form);
              const sondageId = fd.get('sondage_id');
              const nonce = fd.get('sondage_nonce');
              const responses = buildResponsesFromForm(form);

              const params = new URLSearchParams();
              params.set('action', 'enregistrer_reponses_sondage');
              params.set('sondage_id', sondageId);
              params.set('sondage_nonce', nonce);
              params.set('mode', mode); // 'draft' | 'final'
              params.set('reponses', JSON.stringify(responses));

              const res = await fetch(ajaxUrl, {
                method: 'POST',
                body: params,
                credentials: 'same-origin'
              });

              let data = null;
              try { data = await res.json(); } catch(e) {}

              if (!res.ok || !data || data.success === false) {
                const msg = data && data.data && data.data.message ? data.data.message : 'Erreur AJAX';
                throw new Error(msg);
              }
              return data.data || {};
            }

            // Suspendre (brouillon)
            if (suspendBtn) {
              suspendBtn.addEventListener('click', async function () {
                suspendBtn.disabled = true;
                try {
                  await postSondage('draft');
                  alert('Sondage enregistré en brouillon. Vous pourrez le reprendre plus tard.');
                } catch (err) {
                  console.error(err);
                  alert('Impossible d’enregistrer le brouillon pour le moment.');
                } finally {
                  suspendBtn.disabled = false;
                }
              });
            }

            // Soumission finale
            form.addEventListener('submit', async function (e) {
              e.preventDefault();
              const submitBtn = form.querySelector('button[type="submit"]');
              if (submitBtn) submitBtn.disabled = true;

              try {
                const result = await postSondage('final');
                // Recharger pour que le lock soit pris en compte et afficher le message
                window.location.reload();
              } catch (err) {
                console.error(err);
                alert('Impossible d’envoyer le sondage pour le moment.');
              } finally {
                if (submitBtn) submitBtn.disabled = false;
              }
            });
          });
          </script>

        <?php endif; ?>
      </article>
    </main>

    <?php
  endwhile;
endif;

get_footer();
