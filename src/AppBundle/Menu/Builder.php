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
		$info = $menu->addChild('信息查看', array('route' => 'admin_info'));
		$info->setAttribute('class', 'nav-parent');
		$info->setChildrenAttribute('class', 'children');
		$info->addChild('查看', array('route' => 'admin_info'));
		$info->addChild('添加', array('route' => 'admin_info_add'));
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