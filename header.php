<?php
header("Cache-Control: max-age=0");
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="/favicon.ico" rel="shortcut icon" />  
    <title></title>
    <link href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
      <link href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/parl.css?<?php echo rand();?>" rel="stylesheet">
      

  </head>
  <body>
<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
  <symbol id="people-circle" viewBox="0 0 16 16">
    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"></path>
    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"></path>
  </symbol>
</svg>
          <div class="container-fluid px-4">
              <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
                  
                  <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
                      <span class="me-4">
                          <img width="150" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo-bordeaux-montaigne.svg" alt=""/>
                      </span>
                      <div class="d-block">
                          <p class="fs-4 mb-0">Patrimoine Régional Linguistique (PaRL)</p>
                          <p class="fs-6 mb-0">Variation linguistique, représentations sociales, et enjeux didactiques en Nouvelle-Aquitaine</p>
                      </div>
                  </a>
          <?php if (is_user_logged_in()) { ?>        
          <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
            <li>
              <a href="/mon-profil/" class="nav-link text-white" title="Mon profil">
                <svg class="bi d-block mx-auto mb-1" width="36" height="36"><use xlink:href="#people-circle"></use></svg>
              </a>
            </li>
			 <li>
              <a href="<?php echo wp_logout_url(); ?>" class="nav-link" title="Déconnexion">
				
				<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="#000000" class="bi bi-box-arrow-left me-2" viewBox="0 0 16 16">
				  <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z"/>
				  <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z"/>
				</svg>
				  Déconnexion
              </a>
            </li> 
          </ul>
          <?php } else { ?>
		  <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
            
			  <li>
              <a href="/creer-un-compte/" class="nav-link" title="Créer un compte">
				<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="#000000" class="bi bi-person-add me-2" viewBox="0 0 16 16">
				  <path fill-rule="evenodd" d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
				  <path fill-rule="evenodd" d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z"/>
				</svg>
				  Créer un compte
              </a>
            </li>
			<li>
              <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="nav-link" title="Connexion">
				<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="#000000" class="bi bi-box-arrow-right me-2" viewBox="0 0 16 16">
				  <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
				  <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
				</svg>
				  Connexion
              </a>
            </li>  
			  
          </ul>	
				  
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      
      <div class="modal-body">
          <div class="form-signin w-100 m-auto">
          <form id="login" method="post">

            <h1 class="h3 mb-3 fw-normal">Connectez-vous</h1>
            <p class="status"></p>
            <div class="form-floating mb-2">
              <input type="email" class="form-control" id="username" placeholder="name@example.com">
              <label for="username">Adresse email</label>
            </div>

            <div class="form-floating mb-2">
              <input type="password" class="form-control" id="password" placeholder="Password">
              <label for="password">Mot de passe</label>
            </div>
              <a class="btn btn-primary w-100 py-2" id="bt_submit" name="submit">Connexion</a>
              <p class="mt-2 mb-2 text-body-secondary text-center">
                  <a href="<?php echo wp_lostpassword_url(); ?>">Mot de passe oublié ?</a>
              </p>
              <p class="mt-3 mb-3 text-body-secondary">
                  Pas encore inscrit ?  <a href="/creer-un-compte/">Cliquez ici pour créer un compte</a>
              </p>
            <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
          </form>
        </div>
      </div>
      
    </div>
  </div>
</div>				  
				  
		
			<?php } ?> 
                 
              </header>      
      </div> 
  <main>
          
          