<?php
namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
	/**
	 * 后台主菜单
	 * @param FactoryInterface $factory
	 * @param array $options
	 * @return \Knp\Menu\ItemInterface
	 */
	public function mainMenu(FactoryInterface $factory, array $options)
	{
		$menu = $factory->createItem('root');
		$menu->setChildrenAttribute('class', 'nav nav-pills nav-stacked nav-bracket');
		$menu->setChildrenAttribute('id', 'leftmenu');

		$menu->addChild('Dashboard', array('route' => 'admin_index'));
		$story = $menu->addChild('故事展示', array('route' => 'admin_story'));
		$story->setAttribute('class', 'nav-parent');
		$story->setChildrenAttribute('class', 'children');
		$story->addChild('查看', array('route' => 'admin_story'));
		$story->addChild('添加', array('route' => 'admin_story_add'));
		//$one = $menu->addChild('上传心愿活动', array('route' => 'admin_info','routeParameters'=>array('type'=>0,'win'=>0)));
		//$one->setAttribute('class', 'nav-parent');
		//$one->setChildrenAttribute('class', 'children');

		$info = $menu->addChild('↫↫↫信息查看↬↬↬',array('route'=>'admin_menu'));
		$info->setAttribute('class', 'nav-parent');
		$info->setChildrenAttribute('class', 'children');
		$info->addChild('[上传心愿]用户信息', array('route' => 'admin_info','routeParameters'=>array('type'=>0,'win'=>0)));

		$info->addChild('[上传心愿]中奖用户', array('route' => 'admin_info','routeParameters'=>array('type'=>0,'win'=>1)));
		//$two = $menu->addChild('幸运心愿码活动', array('route' => 'admin_info','routeParameters'=>array('type'=>1,'win'=>0)));
		//$two->setAttribute('class', 'nav-parent');
		//$two->setChildrenAttribute('class', 'children');
		$info->addChild('[幸运心愿码]用户信息', array('route' => 'admin_info','routeParameters'=>array('type'=>1,'win'=>0)));
		$info->addChild('[幸运心愿码]中奖用户', array('route' => 'admin_info','routeParameters'=>array('type'=>1,'win'=>1)));
		$info->addChild('[幸运心愿码]抽奖日志', array('route' => 'admin_log'));
		$info->addChild('短信日志', array('route' => 'admin_sms_log'));

		$export = $menu->addChild('↫↫↫数据导出↬↬↬',array('route'=>'admin_export'));
		$export->setAttribute('class', 'nav-parent');
		$export->setChildrenAttribute('class', 'children');
		$export->addChild('〖上传心愿〗全部用户导出', array('route' => 'admin_export','routeParameters'=>array('type'=>0,'win'=>0)));
		$export->addChild('〖上传心愿〗中奖信息导出', array('route' => 'admin_export','routeParameters'=>array('type'=>0,'win'=>1)));
		$export->addChild('〖幸运心愿码〗全部用户导出', array('route' => 'admin_export','routeParameters'=>array('type'=>1,'win'=>0)));
		$export->addChild('〖幸运心愿码〗中奖信息导出', array('route' => 'admin_export','routeParameters'=>array('type'=>1,'win'=>1)));

		$menu->addChild('幸运码统计', array('route' => 'admin_codes_stat'));
		/*
		$storage = $menu->addChild('中奖时间段设置', array('route' => 'admin_timeodds'));
		$storage->setAttribute('class', 'nav-parent');
		$storage->setChildrenAttribute('class', 'children');
		$storage->addChild('查看', array('route' => 'admin_timeodds'));
		$storage->addChild('添加', array('route' => 'admin_timeodds_add'));
		$storage = $menu->addChild('奖池设置', array('route' => 'admin_award'));
		$storage->setAttribute('class', 'nav-parent');
		$storage->setChildrenAttribute('class', 'children');
		$storage->addChild('查看', array('route' => 'admin_award'));
		$storage->addChild('添加', array('route' => 'admin_award_add'));

		$menu->addChild('lottery_log', array('route' => 'admin_lottery_log', 'label' => '中奖日志'));
		$menu->addChild('info', array('route' => 'admin_info', 'label' => '用户信息'));
		$menu->addChild('sn', array('route' => 'admin_sn', 'label' => 'SN信息'));
		*/
		return $menu;
	}
}
