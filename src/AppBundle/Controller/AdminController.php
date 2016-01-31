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
use AppBundle\Form\Type\TimeOddsType;
use AppBundle\Form\Type\AwardType;

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
	 * @Route("/admin/log", name="admin_lottery_log")
	 */
	public function logAction(Request $request)
	{
		$repository = $this->getDoctrine()->getRepository('AppBundle:LotteryLog');
		$queryBuilder = $repository->createQueryBuilder('a');
		
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
	 * @Route("/admin/timeodds", name="admin_timeodds")
	 */
	public function timeoddsAction(Request $request)
	{
		$repository = $this->getDoctrine()->getRepository('AppBundle:TimeOdds');
		$queryBuilder = $repository->createQueryBuilder('a');
		
		$query = $queryBuilder->getQuery();
		$paginator  = $this->get('knp_paginator');

		$pagination = $paginator->paginate(
			$query,
			$request->query->get('page', 1),/*page number*/
			$this->pageSize
			);
		return $this->render('AppBundle:admin:timeodds.html.twig', array('pagination'=>$pagination));
	}
	/**
	 * @Route("/admin/timeodds/add", name="admin_timeodds_add")
	 */
	public function timeoddsAddAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$timeodds = new Entity\TimeOdds();
		$form = $this->createForm(new TimeOddsType(), $timeodds);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$data = $form->getData();
			$em->persist($timeodds);
			$em->flush();
			return $this->redirectToRoute('admin_timeodds');
		}
		return $this->render('AppBundle:admin:timeoddsForm.html.twig', array(
			'form' => $form->createView(),
			));
	}
	/**
	 * @Route("/admin/timeodds/edit/{id}", name="admin_timeodds_edit")
	 */
	public function timeoddsEditAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$timeodds = $em->getRepository('AppBundle:TimeOdds')->find($id);
		$form = $this->createForm(new TimeOddsType(), $timeodds);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$data = $form->getData();

			$em->persist($timeodds);
			$em->flush();
			return $this->redirectToRoute('admin_timeodds');
		}
		return $this->render('AppBundle:admin:timeoddsForm.html.twig', array(
			'form' => $form->createView(),
			));
	}
	/**
	 * @Route("/admin/timeodds/delete/{id}", name="admin_timeodds_delete")
	 */
	public function timeoddsDeleteAction(Request $request, $id)
	{
		$em = $this->getDoctrine()->getManager();
		$timeodds = $em->getRepository('AppBundle:TimeOdds')->find($id);
		$em->remove($timeodds);
		$em->flush();
		return new Response(json_encode(array('ret'=>0, 'msg'=>'')));
	}

	/**
	 * @Route("/admin/sn", name="admin_sn")
	 */
	public function snAction(Request $request)
	{
		$repository = $this->getDoctrine()->getRepository('AppBundle:SnLog');
		$queryBuilder = $repository->createQueryBuilder('a');
		
		$query = $queryBuilder->getQuery();
		$paginator  = $this->get('knp_paginator');

		$pagination = $paginator->paginate(
			$query,
			$request->query->get('page', 1),/*page number*/
			$this->pageSize
			);
		return $this->render('AppBundle:admin:sn.html.twig', array('pagination'=>$pagination));
	}

	/**
	 * @Route("/admin/award", name="admin_award")
	 */
	public function awardAction(Request $request)
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
	 * @Route("/admin/award/add", name="admin_award_add")
	 */
	public function awardAddAction(Request $request)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$award = new Entity\Award();
		$form = $this->createForm(new AwardType(), $award);
		$form->handleRequest($request);
		if ($form->isValid()) {
			$data = $form->getData();
			$em->persist($award);
			$em->flush();
			return $this->redirectToRoute('admin_award');
		}
		return $this->render('AppBundle:admin:awardForm.html.twig', array(
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
		return $this->render('AppBundle:admin:awardForm.html.twig', array(
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
	 * @Route("/admin/info", name="admin_info")
	 */
	public function infoAction(Request $request)
	{
		$repository = $this->getDoctrine()->getRepository('AppBundle:LotteryLog');
		$queryBuilder = $repository->createQueryBuilder('l')
		->leftJoin('l.user', 'u')
		->leftJoin('u.info', 'i')
		->where("l.hasWin = 1");
		
		$query = $queryBuilder->getQuery();
		$paginator  = $this->get('knp_paginator');

		$pagination = $paginator->paginate(
			$query,
			$request->query->get('page', 1),/*page number*/
			$this->pageSize
			);
		return $this->render('AppBundle:admin:info.html.twig', array('pagination'=>$pagination));
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
