<form action="?cntr=posts&action=add_edit" method="POST">
       
        <input type="hidden" name="post[post_id]" value="<?php echo $post['id'] ?>" />
        
        <div>
            <label for="post-title">Тема</label><br />
            <input type="text" id="post-title" name="post[title]" value="<?php echo $post['title'] ?>" />
        </div>
        
        <div>
            <label for="post-content">Содержание</label><br />
            <textarea id="post-content" cols="30" rows="20" name="post[content]"><?php echo $post['content'] ?></textarea>
        </div>
        
        <div>
            <label for="post-tags">Теги</label><br />
            <input type="text" id="post-tags" name="post[tags]" value="<?php echo $post['tags'] ?>" /><br />
            <span class="spanTags">теги разделяются запятыми</span>
        </div>
        
        <div>
            <input type="submit" name="btn-add-post" value="Сохранить" />
        </div>
    </form>

?>
