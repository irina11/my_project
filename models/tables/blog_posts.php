<?php
class blog_posts extends TableGateway
{
    public function __construct($db) 
    {
     parent:: __construct($db,'blog_posts');    
    }
	
	public function blog_postsSelectSpecific1($where,$order) {
		$Sql = 'SELECT p.*
                FROM blog_posts p
                LEFT JOIN blog_posts_tags pt ON pt.post_id = p.id
                LEFT JOIN blog_tags t ON t.id = pt.tag_id'.parent::formationWhere('',$where).$parent::formationOrder($order);
		return parent::resultSelect($Sql);
	}
}
?>
