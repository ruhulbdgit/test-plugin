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
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
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
function test_plugin_call_func()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'test-plugin';

    // ডাটা যুক্ত করা (CREATE)
    if (isset($_POST['add_data'])) {
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $wpdb->insert($table_name, ['name' => $name, 'email' => $email]);
    }

    // ডাটা মুছে ফেলা (DELETE)
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $wpdb->delete($table_name, ['id' => $id]);
    }

    // ডাটা দেখানো (READ)
    $results = $wpdb->get_results("SELECT * FROM $table_name");

?>
    <div class="wrap">
        <h2>Simple CRUD System</h2>

        <!-- নতুন তথ্য যুক্ত করার ফর্ম -->
        <form method="POST">
            <input type="text" name="name" placeholder="নাম" required>
            <input type="email" name="email" placeholder="ইমেইল" required>
            <input type="submit" name="add_data" value="যুক্ত করুন">
        </form>

        <h3>তথ্য তালিকা</h3>
        <table border="1" cellpadding="10">
            <tr>
                <th>ID</th>
                <th>নাম</th>
                <th>ইমেইল</th>
                <th>অ্যাকশন</th>
            </tr>
            <?php foreach ($results as $row) { ?>
                <tr>
                    <td><?php echo esc_html($row->id); ?></td>
                    <td><?php echo esc_html($row->name); ?></td>
                    <td><?php echo esc_html($row->email); ?></td>
                    <td>
                        <a href="admin.php?page=simple-crud&delete=<?php echo $row->id; ?>">মুছুন</a>
                        <a href="admin.php?page=simple-crud-update&id=<?php echo $row->id; ?>">আপডেট</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
<?php
}

// আপডেট ফর্ম পেজ
add_action('admin_menu', function () {
    add_submenu_page('Test Plugin', 'Update Data', 'manage_options', 'simple-crud-update', 'simple_crud_update_page');
});

function simple_crud_update_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'test-plugin';

    // ডাটা আপডেট করা (UPDATE)
    if (isset($_POST['update_data'])) {
        $id = intval($_POST['id']);
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $wpdb->update($table_name, ['name' => $name, 'email' => $email], ['id' => $id]);
        echo "<script>location.href='admin.php?page=simple-crud';</script>";
    }

    // নির্দিষ্ট আইডির ডাটা রিট্রিভ করা
    $id = intval($_GET['id']);
    $data = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id");

?>
    <div class="wrap">
        <h2>তথ্য আপডেট করুন</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo esc_attr($data->id); ?>">
            <input type="text" name="name" value="<?php echo esc_attr($data->name); ?>" required>
            <input type="email" name="email" value="<?php echo esc_attr($data->email); ?>" required>
            <input type="submit" name="update_data" value="আপডেট করুন">
        </form>
    </div>
<?php
}
