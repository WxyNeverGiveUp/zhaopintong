<?php

class NewsTest extends CDbTestCase
{
	public $fixtures=array(
		'news'=>'News',
	);

    public function testAdd()
	{
		$news = new News();
		$news->title = "aaa";
		$news->content = "bbb";
		$this->assertEquals(array(), $news->save());
	}
}
