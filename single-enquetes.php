<?php get_header(); ?>

    <div class="container py-4">
    
        <div class="row">     
<?php
if (have_posts()) : while (have_posts()) : the_post();
    

    $questions = get_post_meta(get_the_ID(), 'questions', true);
    $user_id = get_current_user_id();
    $saved_responses = is_user_logged_in() ? get_user_meta($user_id, 'enquete_' . get_the_ID() . '_responses', true) : [];
    $featured_img_url = get_the_post_thumbnail_url(get_the_ID(), '4_3_medium'); 
            
    $locked = get_user_meta($user_id, 'enquete_' . get_the_ID() . '_locked', true);
?>
    
        
        
            <div class="col-12 col-lg-8 px-4 px-lg-2">    
                <h1><?php the_title(); ?></h1>
                <div><?php the_content(); ?></div>
            </div>
            <div class="d-none d-lg-block col-4">    
                <img src="<?php echo $featured_img_url; ?>" class="w-100"/>
            </div>
        
    <?php if (is_user_logged_in()) : ?>
          <form method="post" action="<?php echo get_permalink(get_the_ID()); ?>">  
          <div class="questionnaire_form mt-5 mb-5">
                
                    <input type="hidden" name="enquete_id" value="<?php echo get_the_ID(); ?>" />
                    <input type="hidden" name="action" value="save_enquete_responses">
                    <?php if( is_array($questions) ){ ?>
                    <?php foreach ($questions as $index => $question) : ?>
                    <div id="question<?php echo $index; ?>" class="question mb-4">
                        <input type="hidden" name="questions[]" value="<?php echo esc_attr($question); ?>" />
                        <div class="col-12 d-flex align-self-center mb-4">
                            <span class="fs-5 pe-4 qindex"><?php echo $index+1; ?></span>
                            <h3 class="fs-5 border-bottom w-100 mb-0 pt-1 pb-2"><?php echo esc_html($question); ?></h3>
                        </div>

                        <div class="row ms-4">
                            <div class="col-12 col-md-6 mb-4">
                                <p class="fs-5 mb-0">Votre traduction</p>
                              <p class="fs-6 text-secondary mb-1">Parler, langue locale</p>
								
                                <textarea class="form-control response" <?php if($locked === "locked"){ ?>disabled="disabled" readonly="readonly"<?php } ?> rows="3" placeholder="Saisissez votre traduction" name="responses[]"><?php echo esc_attr($saved_responses[$index]['response'] ?? ''); ?></textarea>
                               
                            </div>
                            <div class="col-12 col-md-6 mb-4">
                                <p class="fs-5 mb-0">Commentaire</p>
                                <p class="fs-6 text-secondary mb-1">(difficulté particulière, autre traduction possible...)</p>
								
                                <textarea class="form-control" rows="3" <?php if($locked === "locked"){ ?>disabled="disabled" readonly="readonly"<?php } ?> placeholder="Saisissez votre commentaire" name="comments[]"><?php echo esc_attr($saved_responses[$index]['comment'] ?? ''); ?></textarea>
                                
                            </div>
                            <div class="col-12 mb-4">
                                  <div class="enregistreur">
                                      <?php 
                                      if( isset($saved_responses[$index]['audio_url']) && $saved_responses[$index]['audio_url'] != "" ){
                                          $audio_url = esc_url($saved_responses[$index]['audio_url'].'?'.rand() ?? '');
                                      } else {
                                          $audio_url = '';
                                      }
                                      ?>
									  <?php if($locked !== "locked"){ ?>
                                      <a class="bouton-record pe-2 " data-etat="inactif"><i class="bi bi-mic-fill btplayer"></i></a>
									  <?php } ?>
                                      <a class="bouton-play pe-2 " data-etat="inactif"><i class="bi bi-play-fill btplayer"></i></a>
                                      <audio src="<?php echo $audio_url; ?>" class="lecteur w-100" controls></audio>
                                      <input type="hidden" name="audio_blobs[]" class="audio-blob" value="<?php ///show_mp3_url_to_blob($audio_url); ?>">
                                  </div>
                            </div>


                        </div>
                        <div class="row ms-4 d-none text-center justify-content-center">
                             <div class="col-12 mb-4">
                                <!--<button type="button" class="btn btn-secondary">Enregistrer votre réponse</button>-->
                               <input data-index="question<?php echo $index; ?>" type="submit" class="btn btn-danger mx-4" value="Enregistrer la réponse à cette question" />
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?> 
                    <?php } else { ?>
                    <h4 class="text-center">Aucune question n'a été saisie pour cette enquête.</h4>
                    <?php } ?> 
                
                
            </div>
            <?php if( is_array($questions) ){ ?>
            <div class="footer mt-auto py-4 bg-body-tertiary sticky-bottom">
                    <div class="container-fluid">
						<?php if($locked !== "locked"){ ?>
                        <div class="d-flex flex-wrap justify-content-between">
							<input type="submit" class="btn btn-warning mx-4 mb-2" value="Suspendre la saisie" />
                            <button id="bt_validate_form" type="button" class="btn btn-success mx-4 mb-2">Terminer le questionnaire</button>
                        </div>
						<?php } else { ?>
						<div class="col-12">
							<p class="text-center">Vous avez déjà répondu à cette enquête, et vous ne pouvez plus modifier vos réponses</p>
						</div>
						<?php } ?>
                    </div>
            </div>
            <?php } ?>
			  
			  <input type="hidden" value="<?php echo $locked; ?>" name="locked" id="locked"/>
			</form>
<script>
      
document.addEventListener('DOMContentLoaded', function() {
    
    // Vérifie si un index est stocké dans localStorage
    const index = localStorage.getItem('lastIndex');
    if (index) {
      // Scroll jusqu'à l'élément correspondant
      const nextQuestionDiv = document.getElementById(index);
      if (nextQuestionDiv) {
        nextQuestionDiv.scrollIntoView({ behavior: 'smooth' });
      }
      // Supprime l'index du localStorage après avoir scrollé
      localStorage.removeItem('lastIndex');
    }

    // Ajoute un écouteur sur chaque bouton submit
    const submitButtons = document.querySelectorAll('input[type="submit"]');
    submitButtons.forEach(function (button) {
      button.addEventListener('click', function (event) {
		  document.getElementById("locked").value = "inProgress";
        // Empêche la soumission immédiate du formulaire pour stocker l'index
        const index = this.getAttribute('data-index');
		  if( index ){
			  localStorage.setItem('lastIndex', `question${parseInt(index.slice(-1)) + 1}`);
			  
		  }
        
      });
    });    
    
    const boutonsRecord = document.querySelectorAll('.bouton-record');
    const lecteurs = document.querySelectorAll('.lecteur');
    const audioBlobs = document.querySelectorAll('.audio-blob');
    const boutonsPlay = document.querySelectorAll('.bouton-play');
	const bt_validate_form = document.getElementById("bt_validate_form");
    const MAX_RECORDING_DURATION = 20000; //ms
    let mediaStream;
    let mediaRecorder;

    // Demande d'autorisation au chargement de la page
    navigator.mediaDevices.getUserMedia({ audio: true })
        .then(stream => {
            mediaStream = stream;
        })
        .catch(error => {
            console.error('Erreur lors de l\'accès au microphone : ', error);
        });

    boutonsRecord.forEach((bouton, index) => {
        bouton.addEventListener('click', () => {
            const etat = bouton.getAttribute('data-etat');
            if (etat === 'inactif') {
                if (!mediaStream) {
                    console.error('Autorisation non accordée pour accéder au microphone.');
                    return;
                }

                mediaRecorder = new MediaRecorder(mediaStream);
                const audioChunks = [];

                mediaRecorder.addEventListener('dataavailable', event => {
                    audioChunks.push(event.data);
                });

                mediaRecorder.addEventListener('stop', () => {
                    const audioBlob = new Blob(audioChunks, { type: 'audio/mpeg-3' });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    lecteurs[index].src = audioUrl;

                    // Créer un FileReader pour lire le Blob
                    const reader = new FileReader();
                    reader.readAsDataURL(audioBlob);
                    reader.onloadend = function() {
                        const base64data = reader.result;
                        audioBlobs[index].value = base64data;
                    };
                });

                mediaRecorder.start();
                bouton.setAttribute('data-etat', 'actif');
                bouton.innerHTML = '<i class="bi bi-stop-fill btplayer"></i>';
                
                // Arrêter automatiquement l'enregistrement après la durée limite
                setTimeout(() => {
                    if (mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                        bouton.setAttribute('data-etat', 'inactif');
                        bouton.innerHTML = '<i class="bi bi-mic-fill btplayer"></i>';
                    }
                }, MAX_RECORDING_DURATION);
                
            } else {
                mediaRecorder.stop();
                bouton.setAttribute('data-etat', 'inactif');
                bouton.innerHTML = '<i class="bi bi-mic-fill btplayer"></i>';
            }
        });
    });
    
    
    boutonsPlay.forEach((bouton, index) => {
        bouton.addEventListener('click', () => {
            const etat = bouton.getAttribute('data-etat');
            if (lecteurs[index].src && etat === 'inactif') {
                lecteurs[index].play();
            } else if (etat === 'actif') {
                lecteurs[index].pause();
                lecteurs[index].currentTime = 0;
            }
        });

        lecteurs[index].addEventListener('play', () => {
            boutonsPlay[index].setAttribute('data-etat', 'actif');
            boutonsPlay[index].innerHTML = '<i class="bi bi-pause-fill btplayer"></i>';
        });

        lecteurs[index].addEventListener('pause', () => {
            boutonsPlay[index].setAttribute('data-etat', 'inactif');
            boutonsPlay[index].innerHTML = '<i class="bi bi-play-fill btplayer"></i>';
        });

        lecteurs[index].addEventListener('ended', () => {
            boutonsPlay[index].setAttribute('data-etat', 'inactif');
            boutonsPlay[index].innerHTML = '<i class="bi bi-play-fill btplayer"></i>';
        });
    });
    
    
});

	bt_validate_form.addEventListener('click', () => {
		validate_form();
	});
	
	function count_answers(){
		
		var answers_fields = document.getElementsByClassName("response");
		var filledCount = 0;
		var totalFieldsCount = answers_fields.length;
		
		for (let i = 0; i < totalFieldsCount; i++) {
		  // Vérifier si le champ n'est pas vide après avoir retiré les espaces superflus
		  if (answers_fields[i].value.trim() !== "") {
			filledCount++;
		  }
		}
		const result = {
			filledCount:filledCount,
			totalFieldsCount:totalFieldsCount
		}
		return result;
		
	}
	
	function validate_form(){
		var responsesCount = count_answers();
		if( responsesCount.filledCount < responsesCount.totalFieldsCount ){ // pas fini
			alert( "Vous n'avez pas répondu à toutes les questions." );
		} else { // toutes les questions sont traitées
			document.getElementById("locked").value = "locked";
			document.forms[0].submit();
		}
	}
      
</script>


     <?php else : ?>
        <p><a href="/">Connectez-vous</a> pour répondre aux enquêtes.</p>
    <?php endif; ?>         
<?php
endwhile;
endif;
?>
            
        </div>
    </div>
 
<?php get_footer(); ?>