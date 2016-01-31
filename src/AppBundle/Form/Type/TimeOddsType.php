<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\Form\Extension\Core\Type\TimeType;

class TimeOddsType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('initTime', 'time', array(
				'label' => '开始时间',
				'widget' => 'choice',
				'with_seconds' => true,
			))
			->add('deadline', 'time', array(
				'label' => '结束时间',
				'widget' => 'choice',
				'with_seconds' => true,
			))
			->add('winOdds', 'text', array(
				'label' => '中奖几率(小余等于1的小数)',
			))
			->add('save', 'submit', array('label' => '保存'))
		;
	}
	public function getName()
	{
		return 'timeodds';
	}
}