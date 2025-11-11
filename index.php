<?php get_header(); ?>
<div class="container">
    <div class="row">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<div class="col-12 mb-4">
    <?php if (is_user_logged_in()) { ?>
		<h2 class="h3 mb-3 fw-normal">Toutes les enquêtes</h2>
        <?php
        $user_id = get_current_user_id();
        $args = array(
          'numberposts' => -1,
          'post_type'   => 'enquetes'
        );

        $enquetes = get_posts( $args );        
        foreach( $enquetes as $enquete ){
            $featured_img_url = get_the_post_thumbnail_url($enquete->ID, '4_3_medium'); 

            $locked = get_user_meta($user_id, 'enquete_' . $enquete->ID . '_locked', true);

        ?>
        <a class="home_enquetes_liste" href="<?php echo get_permalink( $enquete->ID ); ?>" title="Participer à l'enquête: <?php echo esc_attr($enquete->post_title); ?>">
            <div class="row p-2 mb-4">
                <div class="col-12 col-lg-4 ps-2 ps-lg-0 mb-2">
                    <img class="w-100" src="<?php echo $featured_img_url; ?>"/>
                </div>
                <div class="col-12 col-lg-8 py-2">

                    <h3><?php echo $enquete->post_title; ?></h3>
                    <?php echo get_the_excerpt( $enquete->ID ); ?>
                    <?php if($locked === "locked"){ ?>
                    <p>
                    <span class="badge text-bg-success mt-4 p-2">Vous avez terminé cette enquête, cliquez ici pour consulter vos réponses</span>
                    </p>
                    <?php } else if($locked === "inProgress"){ ?>
                    <p>
                    <span class="badge text-bg-warning mt-4 p-2">Vous n'avez pas terminé cette enquête, cliquez ici pour poursuivre</span>
                    </p>
                    <?php } else { ?>
                    <p>
                    <span class="badge text-bg-info mt-4 p-2">Vous n'avez pas commencé cette enquête</span>
                    </p>
                    <?php } ?>
                </div>
            </div>
        </a>

        <?php
        }
        ?>
		<hr>
		<h2 class="h3 mb-3 fw-normal">Tous les sondages</h2>
        <?php
        $user_id = get_current_user_id();
        $args = array(
          'numberposts' => -1,
          'post_type'   => 'sondages'
        );

        $sondages = get_posts( $args );        
        foreach( $sondages as $sondage ){
            $featured_img_url = get_the_post_thumbnail_url($sondage->ID, '4_3_medium'); 

            //$locked = get_user_meta($user_id, 'enquete_' . $sondage->ID . '_locked', true);

        ?>
        <a class="home_enquetes_liste" href="<?php echo get_permalink( $sondage->ID ); ?>" title="Participer à l'enquête: <?php echo esc_attr($sondage->post_title); ?>">
            <div class="row p-2 mb-4">
                <div class="col-12 col-lg-4 ps-2 ps-lg-0 mb-2">
                    <img class="w-100" src="<?php echo $featured_img_url; ?>"/>
                </div>
                <div class="col-12 col-lg-8 py-2">

                    <h3><?php echo $sondage->post_title; ?></h3>
                    <?php echo get_the_excerpt( $sondage->ID ); ?>
                    <!--<?php if($locked === "locked"){ ?>
                    <p>
                    <span class="badge text-bg-success mt-4 p-2">Vous avez terminé cette enquête, cliquez ici pour consulter vos réponses</span>
                    </p>
                    <?php } else if($locked === "inProgress"){ ?>
                    <p>
                    <span class="badge text-bg-warning mt-4 p-2">Vous n'avez pas terminé cette enquête, cliquez ici pour poursuivre</span>
                    </p>
                    <?php } else { ?>
                    <p>
                    <span class="badge text-bg-info mt-4 p-2">Vous n'avez pas commencé cette enquête</span>
                    </p>
                    <?php } ?>-->
                </div>
            </div>
        </a>

        <?php
        }
        ?>
		<hr>
		
		
    <?php } else { ?>
<!--        <div class="form-signin w-100 m-auto">
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
              <p class="mt-3 mb-3 text-body-secondary">
                  Pas encore inscrit ?  <a href="/creer-un-compte/">Cliquez ici pour créer un compte</a>
              </p>
            <?php //wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
          </form>
        </div>-->    
    <?php } ?>
		
    </div>
		
		
    <div class="col-12 pe-md-3">
        <h1><?php the_title(); ?></h1>
        <div class="mb-5"><?php the_content(); ?></div>
    </div>
		
    
		
<?php endwhile; endif; ?>
    </div>
</div>
<?php get_footer(); ?>