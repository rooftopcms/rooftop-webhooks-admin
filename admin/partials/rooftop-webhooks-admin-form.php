<div class="wrap">
    <?php if(count($webhook_endpoints)):?>
        <table class="wp-list-table widefat fixed striped pages">
            <thead>
            <tr>
                <th>Mode</th>
                <th width="90%">Environment</th>
            </tr>
            </thead>
            <?php foreach($webhook_endpoints as $endpoint): ?>
                <tr>
                    <td><?php echo $endpoint->environment;?></td>
                    <td><a href="?page=rooftop-webhooks-admin-overview&id=<?php echo $endpoint->id ?>"><?php echo $endpoint->url;?></a></td>
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
                    <input type="text" name="url" size="50" value="<?php defined('$new_webhook_url') ? $new_webhook_url : '' ?>"/>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    Environment
                </th>
                <td>
                    <p>
                        <label for="webhook_mode_live">Live</label>
                        <input name="environment" id="webhook_mode_live" value="Live" type="radio" checked />
                    </p>
                    <p>
                        <label for="webhook_mode_staging">Staging</label>
                        <input name="environment" id="webhook_mode_staging" value="Staging" type="radio" />
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
