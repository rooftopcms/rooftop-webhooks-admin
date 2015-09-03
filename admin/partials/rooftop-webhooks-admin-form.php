<div class="wrap">
    <h1>Add new API key</h1>

    <form action="" method="post">
        <table class="form-table">
            <tr>
                <th scope="row">
                    Name
                </th>
                <td>
                    <input type="text" name="webhook_name" value="<?php defined('$new_webhook_name') ? $new_webhook_name : '' ?>"/>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    URI
                </th>
                <td>
                    <input type="text" name="webhook_uri" size="50" value="<?php defined('$new_webhook_uri') ? $new_webhook_uri : '' ?>"/>
                </td>
            </tr>
        </table>

        <?php wp_nonce_field( 'rooftop-webhook-admin', 'webhook-field-token' ); ?>

        <p class="submit">
            <input type="submit" value="Add Webhook" class="button button-primary" />
        </p>

    </form>
</div>
