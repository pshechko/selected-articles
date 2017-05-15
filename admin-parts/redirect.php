<div>
    <label for="<?= $args['id'] ?>">
        <?php _e('Redirect url:') ?>
    </label>

    <input class="widefat"
           id="<?= $args['id'] ?>"
           name="<?= $args['name'] ?>"
           type="text" <?php if (!empty($args['url'])): ?>
           value="<?= $args['url'] ?>" <?php endif ?>
           placeholder="Leave empty to hide the button" />
</div>

<br />
