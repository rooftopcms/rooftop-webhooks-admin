<?php
class PostSaved {
    public function setUp() {
    }
    public function tearDown() {
    }

    public function perform($args) {
        $url = $args['endpoint']['url'];
        $body = $args['body'];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
    }
}
?>