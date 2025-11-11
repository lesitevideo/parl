<?php get_header(); ?>
  <style>
    .autocomplete-suggestions {
      border: 1px solid #ddd;
      max-height: 200px;
      overflow-y: auto;
      position: absolute;
      z-index: 1000;
      background-color: white;
      width: 100%;
    }
    .autocomplete-suggestion {
      padding: 5px 10px;
      cursor: pointer;
    }
    .autocomplete-suggestion:hover {
      background-color: #f0f0f0;
    }
      .autocomplete-list{
          background-color: white;
          position: absolute;
          width: calc(100% - 25px);
          border: 1px solid #e7e7e7;
          border-radius: 5px;
          z-index: 2;
      }
      .bgwhite{
          background-color: white;
          padding: 25px;
      }
</style>

<div class="container">
    <div class="row">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    
        
        
    <?php if (is_user_logged_in()) { ?>
    
        <div class="col-12">
        
        user connecté, redirect vers homepage
    
        </div>
            
    <?php } else { ?>
        <div class="col-12 col-lg-6 pe-lg-4">
            <h1><?php the_title(); ?></h1>
            <div><?php the_content(); ?></div>
        </div>
        
            <div class="col-12 col-lg-6 ps-lg-4">
                <div class="bgwhite">
                    <form id="account_creation_form">
                        <div class="row">

                            

                            <div class="col-12 mb-3">
                                <div class="row p-0">
                                    <div class="col-12">
                                        <label for="email" class="form-label">Adresse courriel *</label>
                                        <input type="email" class="form-control" id="email" placeholder="">
                                    </div>
                                </div>
                            </div>

                            <div class="col-4 mb-3">
                                <label for="birthdate" class="form-label">Date de naissance *</label>
                                <input type="date" class="form-control" id="birthdate" min="1900-01-01" max="2020-12-31">
                                <!--<input class="form-control" type="number" id="birthdate" min="1900" max="2099" step="1" value="" />-->
                            </div>

                            <div class="col-8 mb-3">
                                <p class="form-label">Sexe *</p>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="sexe" id="sexe_masculin" checked>
                                  <label class="form-check-label" for="sexe_masculin">
                                    Masculin
                                  </label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="sexe" id="sexe_feminin" >
                                  <label class="form-check-label" for="sexe_feminin">
                                    Féminin
                                  </label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" name="sexe" id="sexe_nc" >
                                  <label class="form-check-label" for="sexe_nc">
                                    N/C
                                  </label>
                                </div>
                            </div>

                            

                            <div class="col-12 mb-3">
                                <label for="langue" class="form-label">Nom que vous donnez à votre langue régionale ou parler local *</label>
                                <input type="text" class="form-control" id="langue" placeholder="">
                            </div>

                            <div class="col-12 mb-3 position-relative">
                                <label for="learn_town" class="form-label">Commune d’apprentissage de la langue régionale ou commune représentative du parler que vous utilisez *</label>
                                <input type="text" data-citycode="00000" data-lat="" data-lng="" class="form-control autocomplete" id="learn_town" placeholder="">
                                <div class="autocomplete-list d-none"></div>
                            </div>

                            <div class="col-12 mb-3">
                                <div class="row p-0">
                                    <div class="col-12">
                                        <label for="profession" class="form-label">Profession(s) occupée(s) *</label>
                                        <!--<input type="text" class="form-control" id="profession" placeholder="">-->
                                        
                                        <select class="form-control" id="profession" name="profession">
                                            <option selected>Sélectionnez une réponse</option>
                                            <option value="1">Agriculteurs exploitants</option>
                                            <option value="2">Artisans, commerçants, chefs d'entreprise</option>
                                            <option value="3">Cadres et professions intellectuelles supérieures</option>
                                            <option value="4">Professions intermédiaires</option>
                                            <option value="5">Employés</option>
                                            <option value="6">Ouvriers</option>
                                            <option value="7">Retraités</option>
                                            <option value="8">Autres personnes sans activité professionnelle</option>
                                        </select>                                        
                                        
                                        
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="col-12 mb-3">
                                <div class="row p-0">
                                    

                                    <div class="col-12 position-relative">
                                        <label for="town" class="form-label">Localité de résidence *</label>
                                        <input data-citycode="00000" data-lat="" data-lng="" type="text" class="form-control autocomplete" id="town" placeholder="">
                                        <div class="autocomplete-list d-none"></div>
                                    </div>
                                    
                                    <div class="col-12 position-relative">
                                        <label for="birthplace" class="form-label">Lieu de naissance</label>
                                        <input data-citycode="00000" data-lat="" data-lng="" type="text" class="form-control autocomplete" id="birthplace" placeholder="">
                                        <div class="autocomplete-list d-none"></div>
                                    </div>
									<div class="col-12 position-relative">
                                        <label for="otherlangname" class="form-label">Autre nom donné à votre parler local ou plus large</label>
                                        <input type="text" class="form-control " id="otherlangname" placeholder="">
                                        <!--<div class="autocomplete-list d-none"></div>-->
                                    </div>
									<div class="col-12 position-relative">
                                        <label for="otherspokenlangs" class="form-label">Autres langues parlées couramment</label>
                                        <input type="text" class="form-control " id="otherspokenlangs" placeholder="">
                                        <!--<div class="autocomplete-list d-none"></div>-->
                                    </div>
                                    
                                </div>
                            </div>
                            
                            <input type="hidden" name="nonce" id="nonce" value="<?php echo wp_create_nonce('register_user_front_end'); ?>">
							<div class="col-12 mb-3">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="1" id="accept_terms">
									<label class="form-check-label" for="accept_terms">
										J'accepte le <a href="/reglement/" target="_blank">règlement</a> et la <a href="/politique-de-confidentialite/" target="_blank">politique de confidentialité</a> *
									</label>
								</div>
							</div>

							
							
                            <div class="col-12 mt-4">
                                <button id="bt_register" type="button" class="btn btn-primary">Créer un compte</button>
                            </div>
                            
                            <div class="col-12 my-3">
                                <p class="fw-bold register-message"></p>
                            </div>
                            

                        </div>
                    </form>
                    
                    
                    
                </div>
            </div>
        
  <!--      
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.autocomplete');

        inputs.forEach(input => {
            
          input.addEventListener('focusout', function() {
              if( input.value ){
                  if( !input.dataset.citycode || input.dataset.citycode === "" ){
                      input.value = '';
                      alert("Choisissez une ville parmi la sélection proposée");
                  }
              }
          });
            
            
            
          const suggestionBox = input.nextElementSibling;

          input.addEventListener('input', function() {
            const query = input.value;

            if (query.length < 2) {
              suggestionBox.innerHTML = '';
              return;
            }

            fetch(`https://geo.api.gouv.fr/communes?nom=${query}&fields=departement,centre&limit=5`)  // Remplacez par l'URL de votre API
              .then(response => response.json())
              .then(data => {
                suggestionBox.classList.add('d-block');
                suggestionBox.classList.remove('d-none');
                suggestionBox.innerHTML = '';
                data.forEach(item => {
                  const suggestionItem = document.createElement('div');
                  suggestionItem.classList.add('autocomplete-suggestion');
                  suggestionItem.textContent = item.nom + " (" + item.departement.code + ")";
                  suggestionItem.addEventListener('click', function() {
                    input.value = item.nom + " (" + item.departement.code + ")";
                      input.dataset.citycode = item.code;
                      input.dataset.lat = item.centre.coordinates[1];
                      input.dataset.lng = item.centre.coordinates[0];
                    suggestionBox.innerHTML = '';  // Videz la boîte de suggestions
                      suggestionBox.classList.remove('d-block');
                      suggestionBox.classList.add('d-none');
                  });
                  suggestionBox.appendChild(suggestionItem);
                });
              })
              .catch(error => console.error('Erreur:', error));
          });

          document.addEventListener('click', function(event) {
            if (!suggestionBox.contains(event.target) && event.target !== input) {
              suggestionBox.innerHTML = '';
              suggestionBox.classList.remove('d-block');
              suggestionBox.classList.add('d-none');
            }
          });
        });
        
        
        document.getElementById('bt_register').addEventListener('click', function(e) {
            e.preventDefault();
            
            const selectElement = document.getElementById('profession');
            
            var registerMessage = document.querySelector('.register-message');
            registerMessage.classList.add("d-none");
            registerMessage.classList.remove("d-block");
            
            var email = document.getElementById('email').value;
            var birthdate = document.getElementById('birthdate').value;
            var sexe = document.querySelector('input[name="sexe"]:checked').id;
            var langue = document.getElementById('langue').value;
            var profession_id = selectElement.value;
            var profession_txt = selectElement.options[selectElement.selectedIndex].text;
            var nonce = document.getElementById('nonce').value;
            var learnTown = document.getElementById('learn_town').value;
            var birthplace = document.getElementById('birthplace').value;
            var town = document.getElementById('town').value;
			var otherlangname = document.getElementById('otherlangname').value;
			var otherspokenlangs = document.getElementById('otherspokenlangs').value;
            
			
            var town_citycode = document.getElementById('town').dataset.citycode;
            var learn_town_citycode = document.getElementById('learn_town').dataset.citycode;
            var birthplace_citycode = document.getElementById('birthplace').dataset.citycode;
            
            var town_lat = document.getElementById('town').dataset.lat;
            var town_lng = document.getElementById('town').dataset.lng;
            var learn_town_lat = document.getElementById('learn_town').dataset.lat;
            var learn_town_lng = document.getElementById('learn_town').dataset.lng;
            var birthplace_lat = document.getElementById('birthplace').dataset.lat;
            var birthplace_lng = document.getElementById('birthplace').dataset.lng;
            
            if( validateForm() ){
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo admin_url("admin-ajax.php"); ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText); // Conversion de la réponse en objet JSON
                            console.log(response);
                            if (response.success) {
                                registerMessage.textContent = response.result;
                                document.getElementById('bt_register').classList.add("d-none");
                                // Démarrer un timer pour rediriger l'utilisateur après 10 secondes
                                setTimeout(function() {
                                    window.location.href = '/'; // Redirection vers la page d'accueil
                                }, 3000); // 3 secondes
                                
                            } else {
                                registerMessage.textContent = response.result; // Message d'erreur
                            }
                            registerMessage.classList.remove("d-none");
                            registerMessage.classList.add("d-block");
                        } catch (e) {
                            console.error('Erreur lors du parsing JSON:', e);
                        }
                    } else {
                        console.error('An error occurred:', xhr.statusText);
                    }
                };

                xhr.onerror = function() {
                    console.error('An error occurred during the transaction');
                };

                var params = 'action=register_user_front_end' +
                    '&email=' + encodeURIComponent(email) + 
                    '&birthdate=' + encodeURIComponent(birthdate) + 
                    '&sexe=' + encodeURIComponent(sexe) + 
                    '&birthplace=' + encodeURIComponent(birthplace) + 
                    '&town=' + encodeURIComponent(town) + 
                    '&langue=' + encodeURIComponent(langue) + 
                    '&learn_town=' + encodeURIComponent(learnTown) + 
                    '&profession_id=' + encodeURIComponent(profession_id) +
                    '&profession_txt=' + encodeURIComponent(profession_txt) +
                    '&nonce=' + encodeURIComponent(nonce) +
                    '&birthplace_citycode=' + encodeURIComponent(birthplace_citycode) +
                    '&town_citycode=' + encodeURIComponent(town_citycode) +
                    '&learn_town_citycode=' + encodeURIComponent(learn_town_citycode) +
                    '&otherlangname=' + encodeURIComponent(otherlangname) +
					'&otherspokenlangs=' + encodeURIComponent(otherspokenlangs) +
                    '&birthplace_lat=' + encodeURIComponent(birthplace_lat) +
                    '&birthplace_lng=' + encodeURIComponent(birthplace_lng) +
                    '&town_lat=' + encodeURIComponent(town_lat) +
                    '&town_lng=' + encodeURIComponent(town_lng) +
                    '&learn_town_lat=' + encodeURIComponent(learn_town_lat) +
                    '&learn_town_lng=' + encodeURIComponent(learn_town_lng);
                
                xhr.send(params);
                
            }   
        });        
        
    });
      
    function validateForm() {
        const selectElement = document.getElementById('profession');
        
        var email = document.getElementById('email').value;
        var birthdate = document.getElementById('birthdate').value;
        var sexe = document.querySelector('input[name="sexe"]:checked');
        var langue = document.getElementById('langue').value;
        var learnTown = document.getElementById('learn_town').value;
        var profession_id = selectElement.value;
        var profession_txt = selectElement.options[selectElement.selectedIndex].text;
        var town_citycode = document.getElementById('town').dataset.citycode;
        var learn_town_citycode = document.getElementById('learn_town').dataset.citycode;
        var birthplace_citycode = document.getElementById('birthplace').dataset.citycode;

        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!email) {
            alert('Le champ email est requis.');
            return false;
        }
        if (!emailPattern.test(email)) {
            alert('Le format de l\'email est invalide.');
            return false;
        }
        if (!birthdate) {
            alert('Le champ date de naissance est requis.');
            return false;
        }
        if (!sexe) {
            alert('Le champ sexe est requis.');
            return false;
        }
        if (!langue) {
            alert('Le champ langue est requis.');
            return false;
        }
        if (!learnTown) {
            alert('Le champ ville d\'apprentissage est requis.');
            return false;
        }
        if (!profession_id) {
            alert('Le champ profession est requis.');
            return false;
        }
        if (!town_citycode || town_citycode === '00000') {
          alert("Veuillez sélectionner une localité de résidence dans la liste proposée.");
            return false;
        }
        if (!learn_town_citycode || learn_town_citycode === '00000') {
          alert("Veuillez sélectionner une commune d’apprentissage dans la liste proposée.");
            return false;
        }
        /*if (!birthplace_citycode || birthplace_citycode === '00000') {
          alert("Veuillez sélectionner un lieu de naissance dans la liste proposée.");
            return false;
        }*/

        return true;
    }      
      
      
  </script>

   --> 
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.autocomplete');

    inputs.forEach(input => {
        const suggestionBox = input.nextElementSibling;

        input.addEventListener('input', function () {
            const query = input.value.trim();

            if (query.length < 2) {
                suggestionBox.innerHTML = '';
                suggestionBox.classList.add('d-none');
                return;
            }

            fetch(`https://geo.api.gouv.fr/communes?nom=${encodeURIComponent(query)}&fields=departement,centre&limit=5`)
                .then(res => res.json())
                .then(data => {
                    suggestionBox.classList.remove('d-none');
                    suggestionBox.classList.add('d-block');
                    suggestionBox.innerHTML = '';

                    data.forEach(item => {
                        const suggestionItem = document.createElement('div');
                        suggestionItem.classList.add('autocomplete-suggestion');
                        suggestionItem.textContent = `${item.nom} (${item.departement.code})`;

                        suggestionItem.addEventListener('click', function () {
                            input.value = `${item.nom} (${item.departement.code})`;
                            input.dataset.citycode = item.code;
                            input.dataset.lat = item.centre.coordinates[1];
                            input.dataset.lng = item.centre.coordinates[0];
                            suggestionBox.innerHTML = '';
                            suggestionBox.classList.remove('d-block');
                            suggestionBox.classList.add('d-none');
                        });

                        suggestionBox.appendChild(suggestionItem);
                    });
                })
                .catch(error => {
                    console.error("Erreur lors de la récupération des communes :", error);
                });
        });

        input.addEventListener('focusout', function () {
            setTimeout(() => {
                if (input.value && (!input.dataset.citycode || input.dataset.citycode === "00000")) {
                    input.value = '';
                    alert("Choisissez une ville parmi la sélection proposée");
                }
            }, 150); // Laisse le temps au clic sur une suggestion de s'exécuter
        });

        document.addEventListener('click', function (event) {
            if (!suggestionBox.contains(event.target) && event.target !== input) {
                suggestionBox.innerHTML = '';
                suggestionBox.classList.remove('d-block');
                suggestionBox.classList.add('d-none');
            }
        });
    });

    document.getElementById('bt_register').addEventListener('click', function (e) {
        e.preventDefault();

        const registerMessage = document.querySelector('.register-message');
        registerMessage.classList.add("d-none");
        registerMessage.classList.remove("d-block");

        if (!validateForm()) return;

        const selectElement = document.getElementById('profession');

        const data = {
            action: 'register_user_front_end',
            email: document.getElementById('email').value,
            birthdate: document.getElementById('birthdate').value,
            sexe: document.querySelector('input[name="sexe"]:checked').id,
            langue: document.getElementById('langue').value,
            learn_town: document.getElementById('learn_town').value,
            birthplace: document.getElementById('birthplace').value,
            town: document.getElementById('town').value,
            otherlangname: document.getElementById('otherlangname').value,
            otherspokenlangs: document.getElementById('otherspokenlangs').value,
            profession_id: selectElement.value,
            profession_txt: selectElement.options[selectElement.selectedIndex].text,
            nonce: document.getElementById('nonce').value,
            town_citycode: document.getElementById('town').dataset.citycode,
            learn_town_citycode: document.getElementById('learn_town').dataset.citycode,
            birthplace_citycode: document.getElementById('birthplace').dataset.citycode,
            town_lat: document.getElementById('town').dataset.lat,
            town_lng: document.getElementById('town').dataset.lng,
            learn_town_lat: document.getElementById('learn_town').dataset.lat,
            learn_town_lng: document.getElementById('learn_town').dataset.lng,
            birthplace_lat: document.getElementById('birthplace').dataset.lat,
            birthplace_lng: document.getElementById('birthplace').dataset.lng
        };

        fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        })
        .then(res => res.json())
		.then(response => {
			const message = response.data || response.result || "Une réponse inattendue a été reçue.";

			if (response.success) {
				registerMessage.textContent = message;
				document.getElementById('bt_register').classList.add("d-none");
				setTimeout(() => {
					window.location.href = '/';
				}, 3000);
			} else {
				registerMessage.textContent = message;
			}

			registerMessage.classList.remove("d-none");
			registerMessage.classList.add("d-block");
		})

        .catch(err => {
            console.error('Erreur fetch:', err);
            registerMessage.textContent = "Erreur de communication avec le serveur.";
            registerMessage.classList.remove("d-none");
            registerMessage.classList.add("d-block");
        });
    });
});

function validateForm() {
    const email = document.getElementById('email').value.trim();
    const birthdate = document.getElementById('birthdate').value;
    const sexe = document.querySelector('input[name="sexe"]:checked');
    const langue = document.getElementById('langue').value.trim();
    const learnTown = document.getElementById('learn_town').value.trim();
    const profession = document.getElementById('profession').value;
    const town_citycode = document.getElementById('town').dataset.citycode;
    const learn_town_citycode = document.getElementById('learn_town').dataset.citycode;

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!email || !emailPattern.test(email)) {
        alert("Adresse email invalide.");
        return false;
    }

    if (!birthdate) {
        alert("Date de naissance obligatoire.");
        return false;
    }

    if (!sexe) {
        alert("Sexe obligatoire.");
        return false;
    }

    if (!langue) {
        alert("Langue obligatoire.");
        return false;
    }

    if (!learnTown || learn_town_citycode === "00000") {
        alert("Sélectionnez une commune d’apprentissage valide.");
        return false;
    }

    if (!profession || profession === "Sélectionnez une réponse") {
        alert("Sélectionnez une profession.");
        return false;
    }

    if (!town_citycode || town_citycode === "00000") {
        alert("Sélectionnez une localité de résidence valide.");
        return false;
    }
	const acceptTerms = document.getElementById('accept_terms');

	if (!acceptTerms.checked) {
		alert("Vous devez accepter le règlement et la politique de confidentialité.");
		return false;
	}

    return true;
}
</script>

    
    
    <?php } ?>
    
<?php endwhile; endif; ?>
    </div>
</div>
<?php get_footer(); ?>