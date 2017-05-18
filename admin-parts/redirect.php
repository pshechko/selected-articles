<div>
    <label for="<?= $args['id'] ?>">
        <?= __('Redirect url', 'woothemes') . ":" ?>
    </label>

    <input class="widefat"
           id="<?= $args['id'] ?>"
           name="<?= $args['name'] ?>"
           type="text" <?php if (!empty($args['url'])): ?>
               value="<?= $args['url'] ?>" <?php endif; ?>
           placeholder="<?php _e("Leave empty to hide the button", "woothemes"); ?> " />
</div>

<br />
