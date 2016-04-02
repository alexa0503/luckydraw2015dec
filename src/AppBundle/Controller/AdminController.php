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
		$user = new Entity\User();
		$factory = $this->get('security.encoder_factory');
		$encoder = $factory->getEncoder($user);
		$password = $encoder->encodePassword('pPvnwXThqHMHuvJX', $user->getSalt());
		return new Response($password);
	}
	
	/**
	 * @Route("/admin/info/{type}/{win}", name="admin_info")
	 */
	public function infoAction(Request $request,$type=null, $win=null)
	{
		$repository = $this->getDoctrine()->getRepository('AppBundle:Info');
		$queryBuilder = $repository->createQueryBuilder('a');
		$queryBuilder->where('a.type = :type AND a.createTime < :createTime');
        $queryBuilder->setParameter(':createTime', new \DateTime('now'), \Doctrine\DBAL\Types\Type::DATETIME);
		$queryBuilder->setParameter(':type',$type);
		if($win == 1){
            $queryBuilder->andWhere('a.prize != 0');
		}
		if( $request->get('order') == 'time.desc')
			$queryBuilder->orderBy('a.createTime','DESC');
		else
			$queryBuilder->orderBy('a.createTime','ASC');
		
		$query = $queryBuilder->getQuery();
		$paginator  = $this->get('knp_paginator');

		$pagination = $paginator->paginate(
			$query,
			$request->query->get('page', 1),/*page number*/
			$this->pageSize
		);
		if($type == 1)
			return $this->render('AppBundle:admin:info_1.html.twig', array('pagination'=>$pagination));
		else
			return $this->render('AppBundle:admin:info.html.twig', array('pagination'=>$pagination));
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

	/**
	 * @Route("/admin/info/active/{id}", name="admin_info_active")
	 */
	public function infoActiveAction(Request $request, $id = null)
	{
		$em = $this->getDoctrine()->getManager();
		$info = $em->getRepository('AppBundle:Info')->find($id);
		$is_active = $info->getIsActive() == 0 ? 1 : 0;
		$info->setIsActive($is_active);
		$em->persist($info);
		$em->flush();
		$msg = $is_active == 1 ? '关闭' : '开启';
		return new Response(json_encode(array('ret'=>0, 'msg'=>$msg)));
	}

	/**
	 * @Route("/admin/log", name="admin_log")
	 */
	public function logAction(Request $request)
	{
		$repository = $this->getDoctrine()->getRepository('AppBundle:LotteryLog');
		$queryBuilder = $repository->createQueryBuilder('a');

		if( $request->get('order') == 'time.desc')
			$queryBuilder->orderBy('a.createTime','DESC');
		else
			$queryBuilder->orderBy('a.createTime','ASC');

		$query = $queryBuilder->getQuery();
		$paginator  = $this->get('knp_paginator');

		$pagination = $paginator->paginate(
			$query,
			$request->query->get('page', 1),/*page number*/
			$this->pageSize
		);
		return $this->render('AppBundle:admin:log.html.twig', array('pagination'=>$pagination));
	}
	/**
	 * @Route("/admin/sms/log", name="admin_sms_log")
	 */
	public function smsLogAction(Request $request)
	{
		$repository = $this->getDoctrine()->getRepository('AppBundle:SMS');
		$queryBuilder = $repository->createQueryBuilder('a');

		if( $request->get('order') == 'time.desc')
			$queryBuilder->orderBy('a.createTime','DESC');
		else
			$queryBuilder->orderBy('a.createTime','ASC');

		$query = $queryBuilder->getQuery();
		$paginator  = $this->get('knp_paginator');

		$pagination = $paginator->paginate(
			$query,
			$request->query->get('page', 1),/*page number*/
			$this->pageSize
		);
		return $this->render('AppBundle:admin:smsLog.html.twig', array('pagination'=>$pagination));
	}
	/**
	 * @Route("/admin/export/{type}/{win}", name="admin_export")
	 */
	public function exportAction(Request $request, $type = null,$win = null)
	{
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('AppBundle:Info');
		$queryBuilder = $repository->createQueryBuilder('a');
        $queryBuilder->where('a.type = :type AND a.createTime < :createTime');
        $queryBuilder->setParameter(':createTime', new \DateTime('now'), \Doctrine\DBAL\Types\Type::DATETIME);
		$queryBuilder->setParameter(':type',$type);
        if($win == 1){
            $queryBuilder->andWhere('a.prize != 0');
        }
		$info = $queryBuilder->getQuery()->getResult();
		//$output = '';
		if($type == 1){
			$arr = array(
				'id,姓名,手机,地址,抽奖码,抽奖奖项,创建时间,创建IP'
			);
			foreach($info as $v){
				$_string = $v->getId().','.$v->getUsername().','.$v->getMobile().','.$v->getAddress().','.$v->getPrize();
				$_string .= ','.$v->getCreateTime()->format('Y-m-d H:i:s').','.$v->getCreateIp();
				$arr[] = $_string;
			}
		}
		else{
			$arr = array(
				'id,姓名,手机,头像,心愿,赞数,是否抽奖,抽奖奖项,创建时间,创建IP'
			);
			foreach($info as $v){
				$_string = $v->getId().','.$v->getUsername().','.$v->getMobile().',http://dev.slek.com.cn/uploads/'.$v->getHeadImg().',"'.trim($v->getWishText()).'",'.$v->getLikeNum().',';
				$_string .= $v->getHasLottery() == 1 ? '是,' : '否,';
				$_string .= $v->getHasLottery() == 0 ? '--' : $v->getPrize();
				$_string .= ','.$v->getCreateTime()->format('Y-m-d H:i:s').','.$v->getCreateIp();
				$arr[] = $_string;
			}
		}

		$output = implode("\n", $arr);

		//$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
		/*
		$phpExcelObject = new \PHPExcel();
		$phpExcelObject->getProperties()->setCreator("liuggio")
			->setLastModifiedBy("Giulio De Donato")
			->setTitle("Office 2005 XLSX Test Document")
			->setSubject("Office 2005 XLSX Test Document")
			->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
			->setKeywords("office 2005 openxml php")
			->setCategory("Test result file");
		$phpExcelObject->setActiveSheetIndex(0);
		foreach($logs as $v){
			$phpExcelObject->setCellValue('A1', $v->getId());
		}
		$phpExcelObject->getActiveSheet()->setTitle('Simple');
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$phpExcelObject->setActiveSheetIndex(0);

		// create the writer
		$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
		// create the response
		$response = $this->get('phpexcel')->createStreamedResponse($writer);
		// adding headers
		$dispositionHeader = $response->headers->makeDisposition(
			ResponseHeaderBag::DISPOSITION_ATTACHMENT,
			'stream-file.xls'
		);
		$response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');
		$response->headers->set('Content-Disposition', $dispositionHeader);
		*/

		$response = new Response($output);
		$response->headers->set('Content-Disposition', ':attachment; filename=data.csv');
		$response->headers->set('Content-Type', 'text/csv; charset=utf-8');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');
		return $response;
	}
}
