<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
#use Symfony\Component\Form\Extension\Core\Type\DateType;

class StoryType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('username', 'text', array(
				'label' => '姓名',
			))
			->add('likeNum', 'text', array(
				'label' => '赞数',
			))
			->add('headImg', 'file', array(
				'label' => '头像',
				'data_class' => null,
				'required' => false,
				'attr' => array('value' => $builder->getData()->getHeadImg(),'class'=>'preview')
			))
		 ->add('uploadedFiles', 'file', array(
				'label' => '图库',
        'multiple' => true, 
        'data_class' => null,
				'required' => false,
				//'attr' => array('value' => $builder->getData()->getHeadImg(),'class'=>'preview')
      ))
			->add('wishTitle', 'textarea', array(
				'label' => '心愿清单标题',
			))
			->add('wishText', 'textarea', array(
				'label' => '心愿清单描述',
			))

			->add('save', 'submit', array('label' => '保存'))
		;
	}
	public function getName()
	{
		return 'story';
	}
}