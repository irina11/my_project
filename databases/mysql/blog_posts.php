<?php
class blog_posts extends TableGateway
{
    public function __construct($db) 
    {
     parent:: __construct($db,'blog_posts');    
    }
}
?>
