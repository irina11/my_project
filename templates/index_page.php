<?php require_once 'header.php'; ?>



<?php if (!isLoggedUser()): ?>

    <?php require_once 'auth_page.php'; ?>

<?php else: ?>
    
    Wellcome, friend, you are autorized user!
    <img id="pictureSmile" src="image/smile-17.gif" >
    <input type="button" onclick="chimg()">
    
    
    <a href="?action=add">Добавить пост</a>
    <a href="?action=logout">Отмена авторизации</a>

<?php endif ?>

<?php require_once 'header_end.php'; ?>    



<?php if (isset($notificationsList)): ?>
    <ul>
    <?php foreach($notificationsList as $message): ?>
        <li><?php echo $message; ?></li>
    <?php endforeach ?>
    </ul>
<?php endif ?>

<div id="content">
    
    <?php if ($flag == 1): ?> 
       <a href="?">Вернуться на главную...</a>
       <pr></pr>
       <?php $flag = 0 ?>   
    <?php endif ?>
 
    
    <?php if (isset($postsList) && $postsList): ?>
        <?php foreach($postsList as $post): ?>
            <div class="post">
                <div class="post-title">
                    <a class="post-title" href="?action=view&post_id=<?php echo $post['id'] ?>" ><?php echo $post['title'] ?></a>
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
                            <a class="post-tags" href="?action=tag&tag=<?php echo $tag ?>" ><?php echo $tag ?></a><?php if ($tagNum < count($post['tags']) - 1): ?>, <?php endif ?>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>

                <?php if (isLoggedUser()): ?>
                <div class="post-options">
                    <ul>
                        <li><a href="?action=edit&post_id=<?php echo $post['id'] ?>">Редактировать</a></li>
                        <li>
                            <form action="?action=delete" method="POST">
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
        Постов не обнаружено... <?php if (isLoggedUser()): ?><a href="?action=add">Написать пост</a><?php endif ?>
    <?php endif ?>
</div>

<?php require_once 'footer.php'; ?>