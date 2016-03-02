<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
#use Symfony\Component\Form\Extension\Core\Type\DateType;

class InfoType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('username', 'text', array(
				'label' => '姓名',
			))
			->add('mobile', 'text', array(
				'label' => '手机',
			))
			->add('headImg', 'file', array(
				'label' => '头像',
				'data_class' => null,
				'required' => false,
				'attr' => array('value' => $builder->getData()->getHeadImg(),'class'=>'preview')
			))
			->add('wishText', 'textarea', array(
				'label' => '心愿清单',
			))

			->add('save', 'submit', array('label' => '保存'))
		;
	}
	public function getName()
	{
		return 'info';
	}
}