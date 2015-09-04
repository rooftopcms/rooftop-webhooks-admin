<div class="wrap">
    <?php if(count($webhok_endpoints)):?>
        <table class="wp-list-table widefat fixed striped pages">
            <thead>
            <tr>
                <th>Mode</th>
                <th width="90%">Endpoint</th>
            </tr>
            </thead>
            <?php foreach($webhok_endpoints as $endpoint): ?>
                <tr>
                    <td><?php echo $endpoint['webhook_mode'];?></td>
                    <td><a href="?page=rooftop-webhooks-admin-overview&id=1"><?php echo $endpoint['webhook_url'];?></a></td>
                </tr>
            <?php endforeach;?>
        </table>
    <?php endif; ?>

    <h1>Add new API key</h1>

    <form action="" method="post">
        <table class="form-table">
            <tr>
                <th scope="row">
                    URL
                </th>
                <td>
                    <input type="text" name="webhook_url" size="50" value="<?php defined('$new_webhook_url') ? $new_webhook_url : '' ?>"/>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    Mode
                </th>
                <td>
                    <p>
                        <label for="webhook_mode_live">Live</label>
                        <input name="webhook_mode" id="webhook_mode_live" value="Live" type="radio" checked />
                    </p>
                    <p>
                        <label for="webhook_mode_staging">Staging</label>
                        <input name="webhook_mode" id="webhook_mode_staging" value="Staging" type="radio" />
                    </p>
                </td>
            </tr>
        </table>

        <?php wp_nonce_field( 'rooftop-webhook-admin', 'webhook-field-token' ); ?>

        <p class="submit">
            <input type="submit" value="Add Webhook" class="button button-primary" />
        </p>

    </form>
</div>
