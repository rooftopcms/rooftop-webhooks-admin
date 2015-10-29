<div class="wrap">
    <h2>
        Webhooks <a href="?page=rooftop-webhooks-admin-overview&new=true" class="page-title-action">Add New</a>
    </h2>
    <?php if(count($webhook_endpoints)):?>
        <table class="wp-list-table widefat fixed striped pages">
            <thead>
            <tr>
                <th>Mode</th>
                <th width="90%">URL</th>
            </tr>
            </thead>
            <?php foreach($webhook_endpoints as $endpoint): ?>
                <tr>
                    <td><?php echo $endpoint->environment;?></td>
                    <td><a href="?page=rooftop-webhooks-admin-overview&id=<?php echo $endpoint->id ?>"><?php echo $endpoint->url;?></a></td>
                </tr>
            <?php endforeach;?>
        </table>
    <?php else:?>
        <p>
            You haven't added any webhook endpoints yet. <a href="?page=rooftop-webhooks-admin-overview&new=true">Add a new endpoint</a>.
        </p>
    <?php endif; ?>

    <?php apply_filters('webhook_details_overview', '') ?>
</div>
