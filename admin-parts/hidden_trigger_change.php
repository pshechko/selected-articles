<?php if (!empty($args['name']) && !empty($args['id'])): ?>
    <input type="hidden" value="0"
           id="<?= $args['id'] ?>"
           name="<?= $args['name'] ?>">
    <?php
 endif;