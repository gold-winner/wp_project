<?php

define('WP_USE_THEMES', false);
require_once('../../../../wp-load.php');

if (isset($_GET['action']) && $_GET['action'] == 'store_realtime_discords') {

    global $wpdb;

    $table_realtime_discords = $wpdb->prefix . 'realtime_discords';
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_realtime_discords'") == $table_realtime_discords;

    if ($table_exists) {
        $wpdb->query("DROP TABLE $table_realtime_discords");
    }

    // Create the table
    $sql_create_table = "CREATE TABLE IF NOT EXISTS $table_realtime_discords (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        discord_id VARCHAR(255),
                        name VARCHAR(255),
                        member_count INT,
                        presence_count INT,
                        img_src MEDIUMBLOB,
                        url VARCHAR(255),
                        timestamp DATETIME
                    )";
    $wpdb->query($sql_create_table);


    echo json_encode(array('success' => true, 'error' => 'Success!'));
    return;
    wp_die();
}
