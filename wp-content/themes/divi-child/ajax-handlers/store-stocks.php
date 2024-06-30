<?php

define('WP_USE_THEMES', false);
require_once('../../../../wp-load.php');

if (isset($_GET['action']) && $_GET['action'] == 'fetch_and_store_stocks') {

    $url = 'https://mws-2.millistream.com/mws.fcgi';
    $args = array(
        'body' => array(
            'cmd' => 'quote',
            'usr' => 'aktierse',
            'pwd' => 'z6k1e3kZ1qzrOV6Y0QcpsqXTlbydAmX9JyNkvdPr',
            'instrumenttype' => 4,
            'fields' => 'insref,symbol,name,company,country,diff1dprc,diff3mprc,diffqtdprc,diff1yprc,lastprice,numberofshares',
        )
    );

    $response = wp_remote_get($url, $args);

    if (is_wp_error($response)) {
        echo json_encode(array('success' => false, 'error' => 'Failed to fetch data from Millistream API.'));
    } else {
        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == 200) {
            $body = wp_remote_retrieve_body($response);
            $stocks = json_decode($body);
            save_stocks($stocks);
            if ($body && is_array($stocks)) {
                echo json_encode(array('success' => true, 'message' => 'Fetched from Millistream API and stored in the database successfully!'));
            } else {
                echo json_encode(array('success' => false, 'error' => 'Failed to parse data from Millistream API response.'));
            }
        } else {
            $error_message = "HTTP request error: Unexpected response code $response_code";
            error_log($error_message);
            echo json_encode(array('success' => false, 'error' => 'Failed to fetch data from Millistream API.'));
        }
    }
    return;
    wp_die();
}


function save_stocks($stocks)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'stocks';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        insref INT NOT NULL,
        stock_name varchar(255) NOT NULL,
        symbol varchar(255) NOT NULL,
        company INT,
        country varchar(255) NOT NULL,
        diff1dprc FLOAT,
        diff3mprc FLOAT,
        diffqtdprc FLOAT,
        diff1yprc FLOAT,
        lastprice FLOAT,
        numberofshares INT,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    foreach ($stocks as $stock) {
        $wpdb->insert(
            $table_name,
            array(
                'insref' => $stock->insref,
                'stock_name' => $stock->name,
                'symbol' => $stock->symbol,
                'company' => $stock->company,
                'country' => $stock->country,
                'diff1dprc' => $stock->diff1dprc,
                'diff3mprc' => $stock->diff3mprc,
                'diffqtdprc' => $stock->diffqtdprc,
                'diff1yprc' => $stock->diff1yprc,
                'lastprice' => $stock->lastprice,
                'numberofshares' => $stock->numberofshares,
            )
        );
    }
}
