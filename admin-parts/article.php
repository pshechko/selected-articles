<li class="ui-state-default article" 
    seleced-articles-role="article"
    <?php if (!empty($args['date'])): ?> date="<?= $args['date']; ?>" <?php endif; ?>>
    <div class="inner" <?php if (!empty($args['thumbnail'])) echo 'style="background-image: url(' . $args['thumbnail'] . ')"' ?>>
        <input type="hidden"
               class="selected-id"
               seleced-articles-role="input"
               <?php if (!empty($args['input-id'])): ?> id="<?= $args['input-id'] ?>" <?php endif; ?>
               <?php if (!empty($args['article-id'])): ?> article-id="<?= $args['article-id'] ?>" <?php endif; ?>
               <?php if (!empty($args['value'])): ?> value="<?= $args['value'] ?>" <?php endif; ?>
               <?php if (!empty($args['name'])): ?> name="<?= $args['name'] ?>" <?php endif; ?>
               />
               <?php if (!empty($args['title'])): ?>
            <span class="title" seleced-articles-role="title"><?= $args['title'] ?></span>
        <?php endif; ?>
        <?php if (!empty($args['editlinlk'])): ?>
            <span class='article_width edit'>
                <a target='_blank' href='<?= $args['editlinlk'] ?>' title="<?php _e("Edit Article", "woothemes"); ?>">&#x270E;</a>
            </span>
        <?php endif; ?>
        <span class='article_width select' seleced-articles-role="select" title="<?php _e("Select this article", "woothemes"); ?>">
            <a target='_blank' href='#'>&#x2713;</a>
        </span>
        <span class='article_width remove edit' seleced-articles-role="remove" title="<?php _e("Remove this article from selected", "woothemes"); ?>">
            <a target='_blank' href='#'>&#x1F7AB;</a>
        </span>
    </div>
</li>
