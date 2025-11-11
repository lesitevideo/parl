<?php get_header(); ?>

    <div class="container py-4">
    
        <div class="row">     
<?php
if (have_posts()) : while (have_posts()) : the_post();
    $user_id = get_current_user_id();
    $featured_img_url = get_the_post_thumbnail_url(get_the_ID(), '4_3_medium'); 
?>
    
        
        
            <div class="col-12 col-lg-8 px-4 px-lg-2">    
                <h1><?php the_title(); ?></h1>
                <div><?php the_content(); ?></div>
            </div>
            <div class="d-none d-lg-block col-4">    
                <img src="<?php echo $featured_img_url; ?>" class="w-100"/>
            </div>
        
    <?php if (is_user_logged_in()) { ?>
          
		<?php
		$form_data = get_post_meta(get_the_ID(), '_form_fields', true);
		$fields = json_decode($form_data, true);

		$current_user_id = get_current_user_id();
		$reponses_sauvegardees = [];

		if ($current_user_id) {
			$meta = get_user_meta($current_user_id, 'reponses_sondages', true);
			if (is_array($meta) && isset($meta[get_the_ID()])) {
				$reponses_sauvegardees = $meta[get_the_ID()];
			}
		}

		if (is_array($reponses_sauvegardees) && !empty($reponses_sauvegardees)) {
		?>
			<h2 class="text-center my-4">Vous avez déjà répondu à ce sondage.</h2>
		<?php
		} else {

			if (!empty($fields)) {
				$nonce = wp_create_nonce('sondage_nonce');
				?>
						<hr/>
						<div class="col-12 bg-white p-4">
							<form id="sondage-formulaire" method="post">
								<?php foreach ($fields as $i => $field) :
									$label = esc_html($field['label'] ?? '');
									$name = 'field_' . $i;
									$type = $field['type'];
									$options = $field['options'] ?? [];

									// Récupère la valeur existante si présente
									$valeur = $reponses_sauvegardees[$label] ?? null;

									echo '<div class="mb-3">';
									echo '<label for="' . esc_attr($name) . '" class="form-label">' . $label . '</label>';

									switch ($type) {
										case 'text':
											echo '<input type="text" class="form-control" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" value="' . esc_attr($valeur) . '">';
											break;

										case 'textarea':
											echo '<textarea class="form-control" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" rows="4">' . esc_textarea($valeur) . '</textarea>';
											break;

										case 'select':
											echo '<select class="form-select" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '">';
											foreach ($options as $opt) {
												$selected = ($valeur === $opt) ? ' selected' : '';
												echo '<option value="' . esc_attr($opt) . '"' . $selected . '>' . esc_html($opt) . '</option>';
											}
											echo '</select>';
											break;

										case 'checkbox':
											foreach ($options as $j => $opt) {
												$id = $name . '_' . $j;
												$checked = (is_array($valeur) && in_array($opt, $valeur)) ? ' checked' : '';
												echo '<div class="form-check">';
												echo '<input class="form-check-input" type="checkbox" name="' . esc_attr($name) . '[]" value="' . esc_attr($opt) . '" id="' . esc_attr($id) . '"' . $checked . '>';
												echo '<label class="form-check-label" for="' . esc_attr($id) . '">' . esc_html($opt) . '</label>';
												echo '</div>';
											}
											break;

										case 'radio':
											foreach ($options as $j => $opt) {
												$id = $name . '_' . $j;
												$checked = ($valeur === $opt) ? ' checked' : '';
												echo '<div class="form-check">';
												echo '<input class="form-check-input" type="radio" name="' . esc_attr($name) . '" value="' . esc_attr($opt) . '" id="' . esc_attr($id) . '"' . $checked . '>';
												echo '<label class="form-check-label" for="' . esc_attr($id) . '">' . esc_html($opt) . '</label>';
												echo '</div>';
											}
											break;
									}

									echo '</div>';
								endforeach; ?>

								<input type="hidden" name="sondage_id" value="<?php echo get_the_ID(); ?>">
								<input type="hidden" name="sondage_nonce" value="<?php echo $nonce; ?>">
								<button type="submit" class="btn btn-primary">Envoyer</button>
							</form>
						</div>
			<?php } ?>

			<script>
			document.addEventListener('DOMContentLoaded', function () {
				const form = document.getElementById('sondage-formulaire');
				if (!form) return;

				form.addEventListener('submit', function (e) {
					e.preventDefault();

					const formData = new FormData(form);
					const sondageId = formData.get('sondage_id');
					const nonce = formData.get('sondage_nonce');

					const data = {};
					formData.forEach((value, key) => {
						// Checkbox array support
						if (key.endsWith('[]')) {
							const realKey = key.replace('[]', '');
							if (!data[realKey]) data[realKey] = [];
							data[realKey].push(value);
						} else if (!['sondage_id', 'sondage_nonce'].includes(key)) {
							data[key] = value;
						}
					});

					fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
						method: 'POST',
						headers: { 'X-WP-Nonce': nonce },
						body: new URLSearchParams({
							action: 'enregistrer_reponses_sondage',
							nonce: nonce,
							sondage_id: sondageId,
							reponses: JSON.stringify(data)
						})
					})
					.then(res => res.json())
					.then(res => {
						if (res.success) {
							alert("Réponses enregistrées avec succès !");
							form.reset();
							location.reload();
						} else {
							alert("Erreur : " + res.data);
						}
					});
				});
			});
			</script>

		<?php } ?>


     <?php } else { ?>
        <p><a href="/">Connectez-vous</a> pour répondre aux sondages.</p>
    <?php } ?>         
<?php
endwhile;
endif;
?>
            
        </div>
    </div>
 
<?php get_footer(); ?>