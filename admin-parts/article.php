<li class="ui-state-default" >
    <div class="inner" <?php if (!empty($args['thumbnail'])) echo 'style="background-image: url(' . $args['thumbnail'] . ')"' ?>>
        <input type="hidden"
               class="selected-id"
               seleced-articles-role="input"
               <?php if (!empty($args['input-id'])): ?> id="<?= $args['input-id']; ?>" <?php endif; ?>
               <?php if (!empty($args['article-id'])): ?> article-id="<?= $args['article-id']; ?>" <?php endif; ?>
               <?php if (!empty($args['value'])): ?> value="<?= $args['value']; ?>" <?php endif; ?>
               <?php if (!empty($args['name'])): ?> name="<?= $args['name']; ?>" <?php endif; ?>
            />
        <?php if (!empty($args['title'])): ?>
            <span class="title"><?= $args['title']; ?></span>
        <?php endif; ?>
        <?php if (!empty($args['editlinlk'])): ?>
            <span class='article_width edit'>
                <a target='_blank' href='<?= $args['editlinlk']; ?>'>&#x270E;</a>
            </span>
        <?php endif; ?>
    </div>
</li>
