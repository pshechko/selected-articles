<article class="post">
    <div class="media">
        <?php if (!empty($args['image'])): ?>
            <div class="media-left"> 
                <img src="<?= $args['image'] ?>" class="media-object">
            </div>
        <?php endif ?>
        <div class="media-body">
            <div class="meta"> 
                <?php if (!empty($args['date'])): ?>
                    <span class="date"><?= $date ?></span>
                <?php endif ?>
                <?php if (!empty($args['author'])): ?>
                    <span class="name"><?= $args['author'] ?></span>
                <?php endif ?>
            </div>
            <?php if (!empty($args['title'])): ?>
                <h3>
                    <a href="<?= empty($args['redirect'])
                            ? "#"
                            : $args['redirect'] ?>">
                        <?= $args['title'] ?>
                    </a>
                </h3>
            <?php endif ?>
        </div>
    </div>
</article>