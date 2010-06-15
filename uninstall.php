<?php
//Check that we arrived here as a result of the plugin being deleted
if( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) ){
    exit();
}

//Remove options array from the database
delete_option('k2_style_switcher_plugin_options');
?>