<?php
namespace AppBundle\Controller;

//use Guzzle\Http\Message\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Validator\Constraints\Time;
use AppBundle\Entity;
use AppBundle\Form\Type;
//use Symfony\Component\HttpFoundation\File\UploadedFile;

//use Liuggio\ExcelBundle;

//use Symfony\Component\Validator\Constraints\Page;

class AdminController extends Controller
{
	protected $pageSize = 30;
	/**
	 * @Route("/admin/", name="admin_index")
	 */
	public function indexAction()
	{
		return $this->render('AppBundle:admin:index.html.twig');
	}
	/**
	 * @Route("/admin/account/", name="admin_account")
	 */
	public function accountAction()
	{
		
	}
	

	/**
	 * @Route("/admin/story", name="admin_story")
	 */
	public function storyAction(Request $request)
	{
		$repository = $this->getDoctrine()->getRepository('AppBundle:Story');
		$queryBuilder = $repository->createQueryBuilder('a');
		
		$query = $queryBuilder->getQuery();
		$paginator  = $this->get('knp_paginator');

		$pagination = $paginator->paginate(
			$query,
			$request->query->get('page', 1),/*page number*/
			$this->pageSize
		);
		return $this->render('AppBundle:admin:story.html.twig', array('pagination'=>$pagination));
	}
	/**
	 * @Route("/admin/story/add", name="admin_story_add")
	 */
	public function storyAddAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$story = new Entity\Story();
		$form = $this->createForm(new Type\StoryType(), $story);
		//var_dump($request);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$data = $form->getData();
			$fileDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/story';
			$head_img = null;
			$file = $data->getHeadImg();
			if( null != $file ){
				$head_img = md5(uniqid()).'.'.$file->guessExtension();
				$file->move($fileDir, $head_img);
			}
			$story->setHeadImg($head_img);

			$story->setCreateTime(new \DateTime("now"));
			$story->setCreateIp($this->container->get('request')->getClientIp());

			$em->persist($story);
			$em->flush();
			return $this->redirectToRoute('admin_story');
		}
		return $this->render('AppBundle:admin:form.html.twig', array(
			'form' => $form->createView(),
			));
	}

	/**
	 * @Route("/admin/story/edit/{id}", name="admin_story_edit")
	 */
	public function storyEditAction(Request $request, $id = null)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$story = $em->getRepository('AppBundle:Story')->find($id);
		$head_img = $story->getHeadImg();
		$form = $this->createForm(new Type\StoryType(), $story);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$data = $form->getData();
			$fileDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/story';
			$file = $data->getHeadImg();
			if( null != $file ){
				$head_img = md5(uniqid()).'.'.$file->guessExtension();
				$file->move($fileDir, $head_img);
			}
			$upload_files = $data->getUploadedFiles();
			if( null != $upload_files[0]){
				$files = $story->getFiles();
				foreach ($files as $file) {
					$story->removeFile($file);
					$em->remove($file);
				}
			}
			$story->setHeadImg($head_img);
			//$story->setCreateTime(new \DateTime("now"));
			//$story->setCreateIp($this->container->get('request')->getClientIp());

			$em->persist($story);
			$em->flush();
			return $this->redirectToRoute('admin_story');
		}
		return $this->render('AppBundle:admin:form.html.twig', array(
			'form' => $form->createView(),
			));
	}

	/**
	 * @Route("/admin/story/delete/{id}", name="admin_story_delete")
	 */
	public function storyDeleteAction(Request $request, $id = null)
	{
		$em = $this->getDoctrine()->getManager();
		$story = $em->getRepository('AppBundle:Story')->find($id);
		$em->remove($story);
		$em->flush();
		return new Response(json_encode(array('ret'=>0, 'msg'=>'')));
	}

}
