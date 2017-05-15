<?php
if (!empty($args['name']) && !empty($args['id'])):
    ?>
    <div>
        <label for="<?= $args['id']; ?>"><?php _e('Title:'); ?></label> 
        <input class="widefat" id="<?= $$args['id']; ?>" name="<?= $args['name']; ?>" type="text" value="<?= $args['title']; ?>" />
    </div>
    <?php
endif;
?>