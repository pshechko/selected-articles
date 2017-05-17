<li>
    <a 
        <?php if (!empty($args['id'])): ?> href="#<?= $args['id']; ?>"<?php endif; ?>>
            <?php
            $title = empty($args['title']) ?
                    (
                    empty($args['slug']) ?
                    "Tab" :
                    ucfirst($args['slug'])
                    ) :
                    $args['title'];
            _e($title, 'woothemes');
            ?>
    </a>
</li>