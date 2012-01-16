<?php require_once 'header.php'; ?>
<?php require_once 'header_end.php'; ?>

<div id="content">
    
    <a href="?">Вернуться на главную...</a>
    <br> 
    <?php $flag = 0 ?> 
    <form action="?action=add" method="POST">
        <div>
            <label for="post-title">Тема</label><br />
            <input type="text" id="post-title" name="post[title]" />
        </div>
        
        <div>
            <label for="post-content">Содержание</label><br />
            <textarea id="post-content" cols="30" rows="20" name="post[content]"></textarea>
        </div>
        
        <div>
            <label for="post-tags">Теги</label><br />
            <input type="text" id="post-tags" name="post[tags]" /><br />
            <span class="spanTags">теги разделяются запятыми</span>
        </div>
        
                
        <div>
            <input type="submit" name="btn-add-post" value="Добавить пост" />
        </div>
    </form>

</div>

<?php require_once 'footer.php'; ?>