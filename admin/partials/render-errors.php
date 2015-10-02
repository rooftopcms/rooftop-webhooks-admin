<?php if(isset($errors)):?>
    <ul>
    <?php foreach($errors as $attribute => $attribute_errors):?>
        <li>
            <ul>
                <?php foreach($attribute_errors as $error):?>
                    <li>
                        <?php echo $error;?>
                    </li>
                <?php endforeach;?>
            </ul>
        </li>
    <?php endforeach;?>
    </ul>
<?php endif;?>
