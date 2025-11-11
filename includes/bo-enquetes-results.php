<?php

/* BO page resultats */
function get_user_enquetes($user_id) {
    // Récupérer toutes les métadonnées de l'utilisateur
    $user_meta = get_user_meta($user_id);

    // Filtrer les clés des métadonnées qui commencent par "enquete_" et se terminent par "_responses"
    $pattern = '/^enquete_.*_responses$/';
    $filtered_meta_keys = preg_grep($pattern, array_keys($user_meta));

    // Récupérer les métadonnées filtrées
    $filtered_meta = [];
    foreach ($filtered_meta_keys as $key) {
        $filtered_meta[$key] = get_user_meta($user_id, $key, true);
    }

    return $filtered_meta;
}

function get_user_enquete($user_id, $enquete_id) {
    // Récupérer toutes les métadonnées de l'utilisateur
    $user_meta = get_user_meta($user_id);

    // Filtrer les clés des métadonnées qui commencent par "enquete_" et se terminent par "_responses"
    $pattern = '/^enquete_'.$enquete_id.'_responses$/';
    $filtered_meta_keys = preg_grep($pattern, array_keys($user_meta));

    // Récupérer les métadonnées filtrées
    $filtered_meta = [];
    foreach ($filtered_meta_keys as $key) {
        $filtered_meta[$key] = get_user_meta($user_id, $key, true);
    }

    return $filtered_meta;
}

function extract_enquete_id($string) {
    $pattern = '/^enquete_(\d+)_responses$/';
    if (preg_match($pattern, $string, $matches)) {
        return (int) $matches[1];
    } else {
        return null; // Retourne null si le format ne correspond pas
    }
}

// Hook to add the submenu and its page
add_action('admin_menu', 'register_custom_submenu_page');

function register_custom_submenu_page() {
    // Add submenu page to the custom post type menu
    add_submenu_page(
        'edit.php?post_type=enquetes', // Parent slug (custom post type slug)
        'Résultats des enquêtes',                     // Page title
        'Résultats des enquêtes',                            // Menu title
        'edit_others_posts',                          // Capability required to access the page
        'resultats_enquetes',                // Menu slug
        'resultats_enquetes_callback'             // Callback function to display the page content
    );
}

// Callback function to display the content of the submenu page
function resultats_enquetes_callback() {
    ?>
    <style>
        .tab-titles {
            list-style: none;
            padding: 0;
            display: flex;
            border-bottom: 1px solid #ccc;
        }
        .tab-titles li {
            margin: 0;
            padding: 0;
        }
        .tab-titles li a {
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            border: 1px solid #ccc;
            border-bottom: none;
            background: #f1f1f1;
            margin-right: 2px;
        }
        .tab-titles li.active a {
            background: #fff;
            border-top: 2px solid #0073aa;
            border-bottom: 1px solid #fff;
        }
        .tab-content {
            padding: 20px;
            border: 1px solid #ccc;
            background: #fff;
        }
    </style>
    <?php
    $args = array(
        'numberposts' => -1,
        'post_type'   => 'enquetes',
        'post_status' => array('publish','private')
    );

    $enquetes = get_posts( $args );        
    
    ?>

    <div class="wrap">
        <h1>Résultats des enquêtes</h1>
        <div id="tabs">
            <ul class="tab-titles">
                <?php
                $i=0;
                foreach( $enquetes as $enquete ){
                ?>
                <li <?php if( $i<1 ){ echo 'class="active"'; } ?>><a href="#<?php echo $enquete->post_name; ?>"><?php echo $enquete->post_title; ?></a></li>
                <?php
                    $i++;
                }
                ?>
            </ul>
    <?php
    $i=0;
    foreach( $enquetes as $enquete ){
                ?>
            <div id="<?php echo $enquete->post_name; ?>" class="tab-content" <?php if( $i>0 ){ echo 'style="display:none;"'; } ?>>
        <?php
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
        $u=0;
        foreach($user_ids as $user_id){
            $current_user_data = get_userdata($user_id);
            $user_email = $current_user_data->user_email;
			echo '<div class="accordion_enq" id="accordion_'.$enquete->ID.'_'.$user_id.'">';
            echo "<h3 style='background-color:#39B0DE;padding:5px;color:white;cursor:pointer;'>Utilisateur #" . $user_id . "</h3>";
            echo "<div>";
            $user_enquetes = get_user_enquete($user_id,$enquete->ID);
            
            if( !isset($user_enquetes) || empty($user_enquetes) || $user_enquetes == "" ){
                echo "<h4>L'utilisateur #" . $user_id . " n'a pas encore répondu à une seule enquête</h4>";
            }
            
            foreach( $user_enquetes as $enquete_index => $user_enquete ){
                
                //echo $enquete_index . "<br>";
                $enquete_id = extract_enquete_id($enquete_index);
                $enquete_datas = get_post( $enquete_id ); 
                echo "<h4>" . $enquete_datas->post_title . "</h4>";
                //print_r( $enquete_datas );
        ?>
         <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th id="question" class="manage-column column-columnname" scope="col">Question</th>
                    <th id="reponse" class="manage-column column-columnname" scope="col">Réponse</th>
                    <th id="commentaire" class="manage-column column-columnname" scope="col">Commentaire</th>
                    <th id="audio" class="manage-column column-columnname" scope="col">Audio</th>
                </tr>
            </thead>

            <tbody>         

                <?php
                        foreach ($user_enquete as $index => $question){
                            //echo "<div style='display:flex;'>";
                            ?>


                <tr class="alternate">
                    <td class="column-columnname"><?php echo $user_enquete[$index]['question']; ?></td>
                    <td class="column-columnname"><?php echo $user_enquete[$index]['response']; ?></td>
                    <td class="column-columnname"><?php echo $user_enquete[$index]['comment']; ?></td>
                    <td class="column-columnname">
                        <?php 
                             if( isset($user_enquete[$index]['audio_url']) && $user_enquete[$index]['audio_url'] != "" ){
                                echo '<audio src="'. $user_enquete[$index]['audio_url'] .'" class="lecteur" controls></audio>';
                            } else {
                                echo "<p>Pas d'audio pour cette question</p>";
                            }
                        ?>
                    </td>
                </tr>


                            <?php
                        }
                ?>


            </tbody>
        </table>        
        <?php
                
            }
			echo "</div>";
             echo "</div>";
  ?>
	
<?php			
			$u++;
        } // fin loop user
		
        //print_r($user_ids);
        ?>
                <br>
        <a data-enqueteid="<?php echo $enquete_id; ?>" class="bt_export button button-primary button-large" href="#!">Exporter les réponses</a>
            </div>
                <?php

		
		$i++;
		
		
    }
    ?>            
                
            </div>
        
    </div>
    <p>
      <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    </p>
    <p>

</p>

      
		
    <script>
		
        document.addEventListener('DOMContentLoaded', function () {
			/*const accordion_enqs = document.querySelectorAll('.accordion_enq');
			accordion_enqs.forEach(bt_export => {
				jQuery( ".accordion_enq" ).accordion({
				  collapsible: true
				});				
				
			});*/
            const tabLinks = document.querySelectorAll('.tab-titles a');
            const tabContents = document.querySelectorAll('.tab-content');

			const bt_exports = document.querySelectorAll('a.bt_export');
			bt_exports.forEach(bt_export => {
                bt_export.addEventListener('click', function (e) {
                    e.preventDefault();
					var enqueteId = e.target.dataset.enqueteid
					console.log(enqueteId);
					
					let formData = new FormData();
					formData.append("action", "export_single_enquete");
					formData.append("enquete_id", enqueteId);
					formData.append("_ajax_nonce", "<?php echo wp_create_nonce('update_enquete_status_nonce'); ?>");

					fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
						method: "POST",
						body: formData
					})
					.then(response => response.json())
					.then(data => {
						console.log(data);
						if (data.success) {
							console.log(data.data.csv); 
							// Créer un Blob à partir du contenu CSV
							const blob = new Blob([data.data.csv], { type: "text/csv;charset=utf-8;" });

							// Créer un lien de téléchargement temporaire
							const link = document.createElement("a");
							link.href = URL.createObjectURL(blob);
							link.download = "export_enquetes.csv";

							// Ajouter le lien au DOM, déclencher le téléchargement, puis le retirer
							document.body.appendChild(link);
							link.click();
							document.body.removeChild(link);							
							
							
						} else {
							console.error("Erreur AJAX :", data);
						}
					})
					.catch(error => console.error("Erreur AJAX :", error));					
					
				})
			});
			
			
            tabLinks.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();

                    tabLinks.forEach(item => item.parentElement.classList.remove('active'));
                    tabContents.forEach(item => item.style.display = 'none');

                    this.parentElement.classList.add('active');
                    const activeTab = document.querySelector(this.getAttribute('href'));
                    activeTab.style.display = 'block';
                });
            });
        });
      </script>
      <?php
}

?>  