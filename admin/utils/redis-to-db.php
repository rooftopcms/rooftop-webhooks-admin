<?php

foreach(get_sites() as $blog) {
    $blog_id = $blog['blog_id'];
    switch_to_blog($blog_id);

    $redis_endpoints = get_endpoints_from_redis_for_blog($blog_id);

    if( count( $redis_endpoints ) ) {
        update_blog_option( $blog_id, 'webhook_endpoints', $redis_endpoints );
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

    $endpoints = json_decode($redis->get($key), true);

    if( !is_array($endpoints) ) {
        return array();
    }

    return $endpoints;
}

?>