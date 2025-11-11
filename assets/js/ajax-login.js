jQuery(document).ready(function($) {


    // Perform AJAX login on form submit
    $('#bt_submit').on('click', function(e){
        console.log("login");
        //$('form#login p.status').show().text(ajax_login_object.loadingmessage);
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxurl,
            data: { 
                'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
                'username': $('form#login #username').val(), 
                'password': $('form#login #password').val(), 
                'security': $('form#login #security').val() },
            success: function(data){
                console.log(data);
                /*$('form#login p.status').text(data.message);
                if (data.loggedin == true){
                    document.location.href = ajax_login_object.redirecturl;
                }*/
            }
        });
        e.preventDefault();
    });

});