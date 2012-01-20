<form id="form" action="?cntr=users&action=login" method="POST">


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

    <?php if (isset($authErrors) && $authErrors): ?>
        <?php echo $authErrors ?>
    <?php endif ?>


</form>