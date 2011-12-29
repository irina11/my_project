<form id="form" action="?action=login" method="POST">
        
        <?php if (isset($authErrors) && $authErrors): ?>
            <?php echo $authErrors ?>
        <?php endif ?>
    
        <div>
            <label for="user-login">Логин</label><br />
            <input type="text" id="user-login" name="user[login]" value="" />
        </div>
    
        <div>
            <label for="user-pass">Пароль</label><br />
            <input type="password" id="user-pass" name="user[pass]" value="" />
        </div>
    
        <div>
            <input type="submit" name="btn-auth" value="Ok" />
        </div>
    
</form>