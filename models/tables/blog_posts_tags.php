<?php
class blog_posts_tags extends TableGateway
{
    public function __construct($db) 
    {
     parent:: __construct($db,'blog_posts_tags');    
    }
}
?>
