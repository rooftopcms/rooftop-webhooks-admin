<div class="wrap">
    <h1>Edit Endpoint</h1>

    <form action="?page=rooftop-webhooks-admin-overview" method="POST" id="webhook">
        <input type="hidden" name="id" value="<?php echo $endpoint->id ?>"/>
        <table class="form-table">
            <tr>
                <th scope="row">
                    URL
                </th>
                <td>
                    <input type="text" name="url" size="50" value="<?php echo $endpoint->url ?>"/>
                </td>
            </tr>
        </table>

        <p class="submit">
            <input type="submit" value="Save" class="button button-primary" />
        </p>
    </form>

    <form action="?page=rooftop-webhooks-admin-overview" method="POST" id="webhook">
        <input name="method" value="delete" type="hidden"/>
        <input name="id" value="<?php echo $endpoint->id;?>" type="hidden"/>
        <p class="submit">
            <input value="Delete" class="delete button-secondary" type="submit" onclick="return confirm('Are you sure?')"/>
        </p>
    </form>

</div>