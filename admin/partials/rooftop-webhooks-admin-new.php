<div class="wrap">
    <h1>Add new webhook</h1>

    <form action="" method="post">
        <table class="form-table">
            <tr>
                <th scope="row">
                    URL
                </th>
                <td>
                    <?php
                        $url = isset($endpoint) ? $endpoint->url : '';
                    ?>

                    <input type="text" name="url" size="50" value="<?php echo $url ?>"/>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    Environment
                </th>
                <td>
                    <p>
                        <label for="webhook_mode_live">Live</label>
                        <input name="environment" id="webhook_mode_live" value="live" type="radio" checked />
                    </p>
                    <p>
                        <label for="webhook_mode_staging">Staging</label>
                        <input name="environment" id="webhook_mode_staging" value="staging" type="radio" />
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
