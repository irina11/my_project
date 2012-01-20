<!DOCTYPE html>
<html>
    <head>
        <title>Блог</title>
        <link rel="icon" type="image/jpeg" href="image/746.jpeg" sizes="12x12"/>
        <link rel="stylesheet" type="text/css" href="templates/style.css" />
        <script type="text/javascript">
            function chimg()
            {
                var obj;
                obj=document.getElementById("pictureSmile");
                obj.src="image/smile-3.gif";
            } 
        </script>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    </head>
    <body>
        <div id="header">        
            <div id="logo"> 
                <img  src="image/746.jpeg" alt="Почему-то нет рисунка">
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
                <a href="?">Вернуться на главную...</a>
                <pr></pr>
            <?php endif ?>
            
                <?php if(isset($content)) {echo $content;} ?>
                
        </div>
    </body>
</html>