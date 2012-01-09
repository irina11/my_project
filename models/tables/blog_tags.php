<?php

class blog_tags extends TableGateway {

	public function __construct($db) {
		parent:: __construct($db, 'blog_tags');
	}
	
	public function blog_tagsSelectSpecific1($where) {
		$Sql = 'SELECT t.tag, pt.post_id FROM blog_tags t LEFT JOIN blog_posts_tags pt ON t.id = pt.tag_id '.parent::formationWhere('IN',$where);
		return parent::resultSelect($Sql);
	}

}

?>
'SELECT t.tag, pt.post_id FROM `blog_tags` t LEFT JOIN `blog_posts_tags` pt ON t.`id` = pt.`tag_id` 