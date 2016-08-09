<?php

$blogs = wp_get_sites();

foreach($blogs as $blog) {
    $blog_id = $blog['blog_id'];

    switch_to_blog($blog_id);
    echo "Processing blog {$blog['domain']} ({$blog_id})\n--------------------\n\n";

    $redis_endpoints = get_endpoints_from_redis_for_blog($blog_id);
    $wp_endpoints    = get_site_option( 'webhook_endpoints', [] );

    if( count( $redis_endpoints ) && !count( $wp_endpoints ) ) {
    }else {
    }
}

function get_endpoints_from_redis_for_blog($blog_id) {
    $key = 'site_id:'.$blog_id.':webhooks';
    $redis = new Predis\Client([
        'scheme' => 'tcp',
        'host'   => REDIS_HOST,
        'port'   => REDIS_PORT,
        'password' => REDIS_PASSWORD
    ]);

    $endpoints = json_decode($redis->get($key));
    if( !is_array($endpoints) ) {
        return array();
    }

    return $endpoints;
}

?>