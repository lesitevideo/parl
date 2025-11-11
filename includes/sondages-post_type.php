<?php


/**
 * Register a custom post type called "sondages".
*/
function parl_sondages_init() {
	$labels = array(
		'name'                  => 'Sondages',
		'singular_name'         => 'Sondage',
		'menu_name'             => 'Sondages',
		'name_admin_bar'        => 'Sondage',
        'add_new'               => 'Ajouter un sondage',
		'add_new_item'          => 'Ajouter nouveau sondage',
		'new_item'              => 'Nouveau sondage',
		'edit_item'             => 'Editer le sondage',
		'view_item'             => 'Voir le sondage',
        'view_items'             => 'Voir les sondages',
		'all_items'             => 'Tous les sondages',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'sondages' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 2,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt','custom-fields' ),
	);

	register_post_type( 'sondages', $args );
}

add_action( 'init', 'parl_sondages_init' );




?>