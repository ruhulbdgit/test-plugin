<?php
/*
Plugin Name: Test Plugin
* Plugin URI: #
 * Description: একটি সিম্পল ওয়ার্ডপ্রেস CRUD প্লাগিন যা ডাটাবেজে ডাটা সংরক্ষণ, দেখানো, আপডেট এবং ডিলিট করতে পারে।
 * Version: 1.0
 * Author: আপনার নাম
 * Author URI: #
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit;
}

// when install it will make a database table
function simple_crud_create_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'test-plugin';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'simple_crud_create_table');

//  Main Menu 
add_action('admin_menu', 'simple_crud_admin_menu');
function simple_crud_admin_menu()
{
    add_menu_page(
        'Test Plugin',
        'Test Plugin',
        'manage_options',
        'test-plugin',
        'test_plugin_call_func',
        'dashicons-welcome-write-blog',
        20
    );
}
