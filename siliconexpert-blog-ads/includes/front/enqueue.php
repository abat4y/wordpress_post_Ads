<?php

function ads_enqueue_scripts(){
    //wp_register_style( 'ads', plugins_url( '/assets/rateit/rateit.css', RECIPE_PLUGIN_URL ) );
   // wp_enqueue_style( 'ads' );
    wp_register_script( 
        'ads_main', plugins_url( '/assets/js/main.js', RECIPE_PLUGIN_URL ), ['jquery'], '1.0.0', true 
    );
    wp_localize_script( 'ads_main', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    wp_enqueue_script( 'ads_main' );
    wp_enqueue_script( 'jquery' );
}
