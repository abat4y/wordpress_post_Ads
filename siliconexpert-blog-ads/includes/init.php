<?php

function ads_init()
{

    $labels = array(
        'name'                  => _x('ads', 'Post type general name', 'ads'),
        'singular_name'         => _x('Insert Ads', 'Post type singular name', 'Insert Ads'),
        'menu_name'             => _x('Insert Ads', 'Admin Menu text', 'Insert Ads'),
        'name_admin_bar'        => _x('Ads', 'Add New on Toolbar', 'ads'),
        'add_new'               => __('Add New', 'ads'),
        'add_new_item'          => __('Add New Ads', 'ads'),
        'new_item'              => __('New Ads', 'ads'),
        'edit_item'             => __('Edit Ads', 'ads'),
        'view_item'             => __('View Ads', 'ads'),
        'all_items'             => __('All Ads', 'ads'),
        'search_items'          => __('Search Ads', 'ads'),
        'parent_item_colon'     => __('Parent Ads:', 'ads'),
        'not_found'             => __('No Ads found.', 'ads'),
        'not_found_in_trash'    => __('No Ads found in Trash.', 'ads'),
        'featured_image'        => _x('Ads Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'ads'),
        'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'ads'),
        'archives'              => _x('Ads archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'ads'),
        'insert_into_item'      => _x('Insert into Ads', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'ads'),
        'uploaded_to_this_item' => _x('Uploaded to this Ads', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'ads'),
        'filter_items_list'     => _x('Filter Ads list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'ads'),
        'items_list_navigation' => _x('Ads list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'ads'),
        'items_list'            => _x('Ads list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'ads'),
    );

    $args                       =   array(
        'labels'                =>  $labels,
        'description'           =>  'A custom post type for blog ads',
        'public' => false,
        'publicly_queryable' => false,
        'show_ui'               =>  true,
        'show_in_menu'          =>  true,
        'query_var'             =>  true,
        'rewrite'               =>  array('slug' => 'ads'),
        'capability_type'       =>  'post',
        'has_archive'           =>  false,
        'hierarchical'          =>  false,
        'menu_position'         =>  20,
        'supports'              =>  ['title'],
        'menu_icon'   => 'dashicons-analytics',
        'show_in_rest'          =>  true
    );

    register_post_type('ads', $args);
    //add acf field
if (function_exists('acf_add_local_field_group')) :
	acf_add_local_field_group(array(
		'key' => 'group_ads_blog_only',
		'title' => 'Advert Data',
		'fields' => array(
			array(
				'key' => 'Advert',
				'label' => 'Advert link',
				'name' => 'Advert_link',
				'type' => 'link',
				'prefix' => '',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
				'readonly' => 0,
				'disabled' => 0,
			),
            array(
				'key' => 'Advert_image',
				'label' => 'Advert image ',
				'name' => 'Advert_image',
				'type' => 'image',
				'prefix' => '',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
				'readonly' => 0,
				'disabled' => 0,
			),
			array(
				'key' => 'Advert_count',
				'label' => 'Advert click count',
				'name' => 'Advert_count',
				'type' => 'number',
				'prefix' => '',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '0',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
				'readonly' => 1,
				'disabled' => 1,
			)
		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'ads',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'left',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
	));
endif;
}