<?php require_once 'header.php'; ?>


<?php if (!isLoggedUser()): ?>

    <?php require_once 'auth_page.php'; ?>

<?php else: ?>

    Wellcome, friend, you are autorized user!

    <a href="?action=add">Добавить пост</a>
    <a href="?action=logout">Отмена авторизации</a>

<?php endif ?>

<?php require_once 'header_end.php'; ?>





<div id="content">

    <a href="?">Вернуться на главную...</a>

    <?php $flag = 0 ?> 
    <?php if (count($post) == 0): ?>
        <span>Указанный пост не найден!!</span>
    <?php else: ?>
        <?php $postData = array_pop($post); ?>

        <div class="post">
            <div class="post-title">
                <a class="post-title" href="?action=view&post_id=<?php echo $postData['id'] ?>"><?php echo $postData['title'] ?></a>
            </div>
            <div class="post-content">
                <p class="Text">
                    <?php echo $postData['content'] ?>
                </p>    
            </div>
            <div class="post-date"><?php echo $postData['date'] ?></div>
            <?php if (isset($postData['tags'])): ?>
                <div class="post-tags">
                    <?php foreach ($postData['tags'] as $tagNum => $tag): ?>
                        <?php echo $tag ?><?php if ($tagNum < count($postData['tags']) - 1): ?>, <?php endif ?>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        </div>

    <?php endif ?>

</div>

<?php require_once 'footer.php'; ?>