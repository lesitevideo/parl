<?php


/**
 * Register a custom post type called "enquetes".
*/
function parl_enquetes_init() {
	$labels = array(
		'name'                  => 'Enquêtes',
		'singular_name'         => 'Enquête',
		'menu_name'             => 'Enquêtes',
		'name_admin_bar'        => 'Enquête',
        'add_new'               => 'Ajouter une enquête',
		'add_new_item'          => 'Ajouter nouvelle enquête',
		'new_item'              => 'Nouvelle enquête',
		'edit_item'             => 'Editer l\'enquête',
		'view_item'             => 'Voir l\'enquête',
        'view_items'             => 'Voir les enquêtes',
		'all_items'             => 'Toutes les enquêtes',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'enquetes' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 2,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt' ),
	);

	register_post_type( 'enquetes', $args );
}

add_action( 'init', 'parl_enquetes_init' );

?>