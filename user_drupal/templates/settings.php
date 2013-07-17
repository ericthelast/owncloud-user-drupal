<form id="drupal" action="#" method="post">
    <fieldset class="personalblock">
        <legend><strong>Drupal 7 REST configuration (for user authentication)</strong></legend>
        <p>
            <label for="drupal_rest_uri"><?php echo $l->t('drupal_rest_uri');?></label>
            <input type="text" id="drupal_rest_uri" name="drupal_rest_uri"
                value="<?php echo $_['drupal_rest_uri']; ?>" />

            <label for="drupal_rest_username"><?php echo $l->t('drupal_rest_username');?></label>
            <input type="text" id="drupal_rest_username" name="drupal_rest_username" 
                value="<?php echo $_['drupal_rest_username']; ?>" />

            <label for="drupal_rest_password"><?php echo $l->t('drupal_rest_password');?></label>
            <input type="text" id="drupal_rest_password" name="drupal_rest_password" 
                value="<?php echo $_['drupal_rest_password']; ?>" />
        </p>
        <input type="submit" value="Save" />
    </fieldset>
</form>
