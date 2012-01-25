<!DOCTYPE html>
<html>
    <head>
        <title>Блог</title>
        <link rel="icon" type="image/jpeg" href="image/746.jpeg" sizes="12x12"/>
        <link rel="stylesheet" type="text/css" href="templates/style.css" />
        <script src="js/chat.js"></script>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    </head>
    <body>
        <div id="header">        
            <div id="logo"> 
                <a href="?" ><img  src="image/746.jpeg" alt="Почему-то нет рисунка"><- на стартовую</a>
            </div> 
            
                <?php if(isset($header)) {echo $header;} ?>
            
            <div class="clear"></div> 
        </div>
        <hr />
        <?php if (isset($notificationsList)): ?>
            <ul>
                <?php foreach ($notificationsList as $message): ?>
                    <li><?php echo $message; ?></li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>
        <div id="content">

            <?php if (!isset ($flag)): ?> 
                <a href="?cntr=posts&action=listPosts">Вернуться на главную...</a>
                <pr></pr>
            <?php endif ?>
            
                <?php if(isset($content)) {echo $content;} ?>
                
        </div>
    </body>
</html>