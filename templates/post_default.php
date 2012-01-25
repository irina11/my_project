<?php if (isset($postsList) && $postsList): ?>
        <?php foreach($postsList as $post): ?>
            <div class="post">
                <div class="post-title">
                    <a class="post-title" href="?cntr=posts&action=view&post_id=<?php echo $post['id'] ?>" ><?php echo $post['title'] ?></a>
                </div>
                <div class="post-content">
                    <p class="Text">
                        <?php echo $post['content'] ?>
                    </p>    
                </div>
                <div class="post-date"><?php echo $post['date'] ?></div>

                <?php if (isset($post['tags'])): ?>
                    <div class="post-tags">
                        <?php foreach($post['tags'] as $tagNum => $tag): ?>
                            <a class="post-tags" href="?cntr=posts&action=tag&tag=<?php echo $tag ?>" ><?php echo $tag ?></a><?php if ($tagNum < count($post['tags']) - 1): ?>, <?php endif ?>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>

                <?php if ($isLoggedUser): ?>
                <div class="post-options">
                    <ul>
                        <li><a href="?cntr=posts&action=edit&post_id=<?php echo $post['id'] ?>">Редактировать</a></li>
                        <li>
                            <form action="?cntr=posts&action=delete" method="POST">
                                <input type="hidden" name="post_id" value="<?php echo $post['id'] ?>" />
                                <input type="submit" value="Удалить" />
                            </form>
                        </li>
                    </ul>
                </div>
                <?php endif ?>
            </div>
            <p></p>
            <p></p>
        <?php endforeach ?>
    <?php else: ?>
        Постов не обнаружено... 
    <?php endif ?>