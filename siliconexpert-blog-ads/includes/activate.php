<?php
function ads_activate_plugin(){
    // 5.8 < 5.0
    if( version_compare( get_bloginfo( 'version' ), '6.0', '<' ) ){
        wp_die( __( "You must update WordPress to use this plugin.", 'ads' ) );
    }
}