<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
#use Symfony\Component\Form\Extension\Core\Type\DateType;

class AwardType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('lotteryDate', 'date', array(
				'label' => '抽奖日期',
				'widget' => 'choice',
				'format' => 'yyyy-MM-dd',
			))
			->add('lotteryNum', 'text', array(
				'label' => '可抽奖数(整数)',
			))
			->add('winNum', 'text', array(
				'label' => '已中奖数(整数)',
			))
			->add('awardType', 'text', array(
				'label' => '奖品类型(整数)',
			))
			->add('lotteryType', 'choice', array(
				'label' => '抽奖方式',
				'choices' => array('a'=>'靠人品','b'=>'靠装备')
			))

			->add('save', 'submit', array('label' => '保存'))
		;
	}
	public function getName()
	{
		return 'storage';
	}
}