      </main>
 

<div class="container-fluid bg-secondary-subtle mt-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-center align-items-center py-3 mt-2 mb-5">
            <?php
            $options = get_option( 'parl_options' );
            if( isset($options['parl_partners_logos']) ){
                $logos = explode(",", str_replace(' ', '', $options['parl_partners_logos']));
                if( is_array($logos) ){
                    //print_r( $logos );
                    foreach( $logos as $logoID ){
                        
                        $img_atts = wp_get_attachment_image_src($logoID, 'thumbnail');
                        echo '<img class="mx-2" src="' . $img_atts[0] . '"/>';
                        //print_r( $img_atts[0] );
                    }
                }
            }
            
            ?>
        </div>
    </div>
</div>    
<div class="container">    
    <footer class="d-flex flex-wrap justify-content-center align-items-center pt-3 pb-5 mt-5">
        <div class="col d-flex align-items-center">
            <span class="mb-3 mb-md-0 text-body-secondary">
                © 2024 Université Bordeaux Montaigne • <a href="/politique-de-confidentialite/">Politique de confidentialité</a> • <a href="#">Mentions légales</a>
            </span>
        </div>
    </footer>
</div>


<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/jquery-3.7.1.min.js"></script>
<script>
jQuery(document).ready(function($) {
    // Perform AJAX login on form submit
    $('#bt_submit').on('click', function(e){
        console.log("login");
        //$('form#login p.status').show().text(ajax_login_object.loadingmessage);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
            data: { 
                'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
                'username': $('form#login #username').val(), 
                'password': $('form#login #password').val(), 
                'security': $('form#login #security').val() },
            success: function(data){
                console.log(data);
                $('form#login p.status').text(data.message);
                if (data.loggedin == true){
                    window.location.reload();
                }/**/
            }
        });
        e.preventDefault();
    });
});
</script>

<!--<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/ajax-login.js"></script>-->

  </body>
</html>