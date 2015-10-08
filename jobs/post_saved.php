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
        curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // if we don't have success with response, or success with no response - requeue this message
        if(!in_array($status, array(200, 204))){
            // if this task is ok to retry, push it back onto a 'retry' queue
            // else this task has failed too many times

            $attempts = array_key_exists('attempts', $body) ? $body['attempts']+=1 : 1;
            $delay_in_minutes = $attempts*$attempts*$attempts;

            if($attempts<3) {
                $body['attempts'] = $attempts;
                $args['body'] = $body;
                error_log("\n\nRetrying in $delay_in_minutes minutes\n\n");
                Resque::later(new \DateTime("+$delay_in_minutes mins"), 'PostSaved', $args, 'retry');
            }else {
                error_log("\n\nnot retrying\n\n");
            }
        }
    }
}
?>