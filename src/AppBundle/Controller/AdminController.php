<?php
namespace AppBundle\Controller;

//use Guzzle\Http\Message\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use AppBundle\Entity;
use Symfony\Component\Validator\Constraints\Time;
use AppBundle\Form\Type\InfoType;

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
	 * @Route("/admin/info", name="admin_info")
	 */
	public function infoAction(Request $request)
	{
		$repository = $this->getDoctrine()->getRepository('AppBundle:Award');
		$queryBuilder = $repository->createQueryBuilder('a');
		
		$query = $queryBuilder->getQuery();
		$paginator  = $this->get('knp_paginator');

		$pagination = $paginator->paginate(
			$query,
			$request->query->get('page', 1),/*page number*/
			$this->pageSize
		);
		return $this->render('AppBundle:admin:award.html.twig', array('pagination'=>$pagination));
	}
	/**
	 * @Route("/admin/info/add", name="admin_info_add")
	 */
	public function infoAddAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$info = new Entity\Info();
		$form = $this->createForm(new InfoType(), $info);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$data = $form->getData();
			$image_path = null;
			$file = $data->getHeadImg();
			$image = $this->get('image.handle');
			if( $image->upload($file)){
				$image_path = $image->create();
			}
			var_dump($image_path);
			$info->setHeadImg($image_path);
			$info->setCreateTime(new \DateTime("now"));
			$info->setCreateIp($this->container->get('request')->getClientIp());

			$em->persist($info);
			$em->flush();
			return $this->redirectToRoute('admin_info');
		}
		return $this->render('AppBundle:admin:form.html.twig', array(
			'form' => $form->createView(),
			));
	}
	/**
	 * @Route("/admin/award/edit/{id}", name="admin_award_edit")
	 */
	public function awardEditAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$award = $em->getRepository('AppBundle:Award')->find($id);
		$form = $this->createForm(new AwardType(), $award);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$data = $form->getData();

			$em->persist($award);
			$em->flush();
			return $this->redirectToRoute('admin_award');
		}
		return $this->render('AppBundle:admin:form.html.twig', array(
			'form' => $form->createView(),
			));
	}
	/**
	 * @Route("/admin/award/delete/{id}", name="admin_award_delete")
	 */
	public function awardDeleteAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$award = $em->getRepository('AppBundle:Award')->find($id);
		$em->remove($award);
		$em->flush();
		return new Response(json_encode(array('ret'=>0, 'msg'=>'')));
	}
	/**
	 * @Route("/admin/export/", name="admin_export")
	 */
	public function exportAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$repository = $em->getRepository('AppBundle:Info');
		$queryBuilder = $repository->createQueryBuilder('a');
		$info = $queryBuilder->getQuery()->getResult();
		//$output = '';
		$arr = array(
			'id,对应微信昵称,姓名,手机,地址,中奖信息,抽奖时间,抽奖IP'
			);
		foreach($info as $v){
			$_string = $v->getId().','.$v->getUser()->getNickname().','.$v->getUsername().','.$v->getMobile().','.$v->getMobile().','.$v->getAddress().',';
			if( null != $v->getUser()->getLotteryLogs() ){
				foreach ($v->getUser()->getLotteryLogs() as $log) {
					if($log->getHasWin() == true){
						$_string .= $log->getType().'类型-'.$log->getAwardType().'等奖 | ';
					}
				}
			}
			//var_dump($v->getCreateTime());
			$_string .= ','.$v->getCreateTime()->format('Y-m-d H:i:s').','.$v->getCreateIp();
			$arr[] = $_string;
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
