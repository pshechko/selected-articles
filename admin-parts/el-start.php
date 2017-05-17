<?php if (!empty($args['element'])): ?>
    <<?= $args['element'] ?> 
    <?php if (!empty($args['id'])): ?> id="<?= $args['id'] ?>" <?php endif; ?> 
    <?php if (!empty($args['class'])): ?>class="<?= $args['class'] ?>"<?php endif; ?>
    <?php
    if (!empty($args['attributes']) && is_array($args['attributes'])):
        foreach ($args['attributes'] as $attribute => $value):
            echo ' ' . $attribute . "='{$value}'";
        endforeach;
    endif;
    ?>
    >
<?php endif; ?>