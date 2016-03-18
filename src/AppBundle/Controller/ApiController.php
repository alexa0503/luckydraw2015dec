<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Helper;
use AppBundle\Entity;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Cookie;
use AppBundle\ImageHandle;

class ApiController extends Controller
{
	//protected $keywords = array('');
	/**
	 * @Route("/form", name="api_form")
	 */
	public function formAction(Request $request)
	{
		$session = $request->getSession();
		$result = array('ret'=>1002,'msg'=>'来源不正确');
		if($request->getMethod() == 'POST'){
			if( null == $request->get('username')){
				$result['ret'] = 1004;
				$result['msg'] = '用户名不能为空';
			}
			elseif( null == $request->get('mobile')){
				$result['ret'] = 1005;
				$result['msg'] = '手机号不能为空';
			}
			/*
			elseif( null == $request->get('wishText')){
				$result['ret'] = 1008;
				$result['msg'] = '心愿清单不能为空';
			}
			*/
			elseif( !preg_match('/^1\d{10}$/', $request->get('mobile'))){
				$result['ret'] = 1006;
				$result['msg'] = '手机格式不正确';
			}
			else{
				$username = $request->get('username');
				$mobile = $request->get('mobile');
				$wish_text = $request->get('wishText');
				
				$em = $this->getDoctrine()->getManager();
				$em->getConnection()->beginTransaction();
				try{
					/*
					$repo = $em->getRepository('AppBundle:Info');
					$qb = $repo->createQueryBuilder('a');
					$qb->select('COUNT(a)');
					$qb->where('a.mobile = :mobile');
					$qb->setParameter('mobile', $mobile);
					$count = $qb->getQuery()->getSingleScalarResult();
					*/
					$count = 0;
					$hasImage = false;
					if($count <= 0){
						$image = $this->get('image.handle');
						if( $request->get('isWechat') == '1'){
							$token = file_get_contents('http://campaign.slek.com.cn/wxtoken/token.php');
							if($image->getImageFromWechat($request->get('imageId'),$token)){
								$hasImage = true;
							}
						}
						else{
							if( null != $request->files->get('headImg') && $image->upload($request->files->get('headImg'))){
								$hasImage = true;
							}
						}

						if( !$hasImage ){
							$image_path = 'default.png';
							//$result['ret'] = 1007;
							//$result['msg'] = '图片上传不正确';
						}
						else{
							$image_path = $image->create();
						}
						$is_active = true;
						$file = $this->file_path = preg_replace('/app$/si', 'web/', $this->get('kernel')->getRootDir()).'keyword.txt';
						$keywords = explode(',', file_get_contents($file));
						foreach ($keywords as $keyword) {
							$pattern = '/('.$keyword.')/i';
							if(preg_match($pattern, $wish_text)){
								$is_active = false;
								break;
							}
						}

						$info = new Entity\Info();
						$info->setUsername($username);
						$info->setMobile($mobile);
						$info->setHeadImg($image_path);
						$info->setWishText($wish_text);
						$info->setCreateIp($request->getClientIp());
						$info->setCreateTime(new \DateTime('now'));
						$info->setIsActive($is_active);
						$em->persist($info);
						$em->flush();
						$em->getConnection()->commit();
						$session->set('id',$info->getId());
						$result['ret'] = 0;
						$result['msg'] = '';
						//'http://'.$request->getHost().'/uploads/'.$value->getHeadImg()
						//$cacheManager->getBrowserPath('uploads/'.$value->getHeadImg(), 'thumb1')
						
						$cacheManager = $this->container->get('liip_imagine.cache.manager');
						//$result['data'] = array('id'=>$info->getId(),'username'=>$username,'headImg'=>$cacheManager->getBrowserPath('uploads/'.$info->getHeadImg(), 'thumb1'));
						$result['data'] = array('id'=>$info->getId(),'username'=>$username,'headImg'=>'http://'.$request->getHost().'/uploads/'.$info->getHeadImg());
					}
					else{
						$result['ret'] = 1100;
						$result['msg'] = '该手机已经被注册';
					}
				}
				catch (Exception $e) {
					$em->getConnection()->rollback();
					$result['ret'] = 1001;
					$result['msg'] = $e->getMessage();
				}
			}
		}
		if( $result['ret'] === 0 && null !== $request->get('url')){
			//var_dump(urldecode($request->get('url')).'/'.$info->getId());
			return $this->redirect(urldecode($request->get('url')).'/'.$info->getId());
		}
		elseif( $result['ret'] !== 0 && null !== $request->get('failUrl')){
			return $this->redirect(urldecode($request->get('failUrl')).'?info='.urlencode($result['msg']));
		}

		$response = new Response();
		if( null == $request->get('callback'))
			$response->setContent(json_encode($result));
		else
			$response->setContent($callback.'('.json_encode($result).')');
		$response->headers->set('Access-Control-Allow-Origin', '*');
		return $response;
	}
	/**
	 * @Route("/search", name="api_search")
	 */
	public function searchAction(Request $request)
	{
		$page = null === $request->get('page') || (int)$request->get('page') < 1 ? 1 : (int)$request->get('page');
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Info');
		$qb = $repo->createQueryBuilder('a');
		$qb->where('a.isActive = 1');
		if( null !== $request->get('mobile')){
			$qb->andWhere('a.mobile LIKE :mobile');
			$qb->setParameter(':mobile', '%'.$request->get('mobile').'%');
		}
		if( null !== $request->get('username')){
			$qb->andWhere('a.username LIKE :username');
			$qb->setParameter(':username', '%'.$request->get('username').'%');
		}
		$limit = 8;
		$offset = ($page-1)*$limit;
		if( null == $request->get('order')){
			$order = array('likeNum','desc');
		}
		else{
			$order = explode('.', $request->get('order'));
			if( isset($order[1]) && !in_array(strtolower($order[1]), array('desc','asc')))
				$order[1] = 'desc';
			if( isset($order[0]) && !in_array($order[0], array('username','createTime','mobile','likeNum')))
				$order[0] = 'createTime';
		}
		$qb->orderBy('a.'.$order[0],strtoupper($order[1]));
		$qb->setMaxResults($limit);
		$qb->setFirstResult($offset);
		$info = $qb->getQuery()->getResult();

		$repo = $em->getRepository('AppBundle:Info');
		$qb = $repo->createQueryBuilder('a');
		$qb->select('COUNT(a)');
		$count = $qb->getQuery()->getSingleScalarResult();

		$data = array();
		$cacheManager = $this->container->get('liip_imagine.cache.manager');
		foreach ($info as $value) {
			$data[] = array(
				'id' => $value->getId(),
				'username' => $value->getUsername(),
				'mobile' => $value->getMobile(),
				'likeNum' => $value->getLikeNum(),
				'wishText' => $value->getWishText(),
				'city' => $this->getCity($value->getCreateIp()),
				'headImg' => 'http://'.$request->getHost().'/uploads/'.$value->getHeadImg(),
				'thumb' => 'http://'.$request->getHost().'/uploads/'.$value->getHeadImg(),
				//'thumb' => $cacheManager->getBrowserPath('uploads/'.$value->getHeadImg(), 'thumb1'),
				'grayHeadImg' => 'http://'.$request->getHost().'/uploads/gray/'.$value->getHeadImg(),
				'sum' => $count,
				'top' => $this->getRank($value->getLikeNum()),
			);
		}
		$result = array(
			'ret' => 0,
			'data' => $data,
		);
		$callback = $request->get('callback') ? : 'callback';
		//return new Response($callback.'('.json_encode($result).')');
		$response = new Response();
		if( null == $request->get('callback'))
			$response->setContent(json_encode($result));
		else
			$response->setContent($callback.'('.json_encode($result).')');
		$response->headers->set('Access-Control-Allow-Origin', '*');
		return $response;
	}
	/**
	 * @Route("/info/{id}", name="api_info")
	 */
	public function infoAction(Request $request,$id = null)
	{
		if( null == $id){
			$result = array(
				'ret' => 1001,
				'msg' => '没有您要的数据',
			);
		}
		else{
			$info = $this->getDoctrine()->getRepository('AppBundle:Info')->find($id);
			if( $info == null || $info->getIsActive() == false ){
				$result = array(
					'ret' => 1001,
					'msg' => '没有您要的数据',
				);
				$info = $this->getDoctrine()->getRepository('AppBundle:Info')->find(1);
			}


			$cacheManager = $this->container->get('liip_imagine.cache.manager');
			$data = array(
				'id' => $info->getId(),
				'username' => $info->getUsername(),
				'mobile' => $info->getMobile(),
				'likeNum' => $info->getLikeNum(),
				'wishText' => $info->getWishText(),
				'city' => $this->getCity($info->getCreateIp()),
				'headImg' => 'http://'.$request->getHost().'/uploads/'.$info->getHeadImg(),
				'thumb' => 'http://'.$request->getHost().'/uploads/'.$info->getHeadImg(),
				//'thumb' => $cacheManager->getBrowserPath('uploads/'.$info->getHeadImg(), 'thumb1'),
				'grayHeadImg' => 'http://'.$request->getHost().'/uploads/gray/'.$info->getHeadImg(),
				'top' => $this->getRank($info->getLikeNum()),
			);
			$result = array(
				'ret' => 0,
				'data' => $data,
			);
		}

		$callback = $request->get('callback') ? : 'callback';
		//return new Response($callback.'('.json_encode($result).')');
		$response = new Response();
		if( null == $request->get('callback'))
			$response->setContent(json_encode($result));
		else
			$response->setContent($callback.'('.json_encode($result).')');
		$response->headers->set('Access-Control-Allow-Origin', '*');
		return $response;
	}
	/**
	 * @Route("/like/{id}", name="api_like")
	 */
	public function likeAction(Request $request,$id = null)
	{
		$em = $this->getDoctrine()->getManager();
		$em->getConnection()->beginTransaction();
		try{
			$info = $em->getRepository('AppBundle:Info')->find($id);
			if( $info == null ){
				$result = array(
					'ret' => 1001,
					'msg' => '该信息不存在',
				);
			}
			else{
				$create_ip = $request->getClientIp();
				$timestamp = time();
				$create_time1 = date('Y-m-d 00:00:00',$timestamp);
				$create_time2 = date('Y-m-d 00:00:00', strtotime('+1 day', $timestamp));

				$repo = $em->getRepository('AppBundle:LikeLog');
				$qb = $repo->createQueryBuilder('a');
				$qb->select('COUNT(a)');
				$qb->where('a.createIp = :createIp AND a.createTime >= :createTime1 and a.createTime < :createTime2');
				$qb->setParameter('createIp', $create_ip);
				$qb->setParameter(':createTime1', new \DateTime($create_time1), \Doctrine\DBAL\Types\Type::DATETIME);
				$qb->setParameter(':createTime2', new \DateTime($create_time2), \Doctrine\DBAL\Types\Type::DATETIME);
				$count = $qb->getQuery()->getSingleScalarResult();

				if( $count < 999999){
					$info->increaseLikeNum();
					$em->persist($info);
					$like_log = new Entity\LikeLog();
					$like_log->setInfo($info);
					$like_log->setCreateIp($create_ip);
					$like_log->setCreateTime(new \DateTime('now'));
					$em->persist($like_log);
					$em->flush();
					$result = array(
						'ret' => 0,
						'msg' => '',
						'num' => $info->getLikeNum(),
					);
				}
				else{
					$result = array(
						'ret' => 1200,
						'msg' => '投票已超出限制~',
					);
				}
				
			}
			$em->getConnection()->commit();
		}
		catch (Exception $e) {
			$em->getConnection()->rollback();
			$result = array(
				'ret' => 2001,
				'msg' => $e->getMessage(),
			);
		}
		
		$callback = $request->get('callback') ? : 'callback';
		//return new Response($callback.'('.json_encode($result).')');
		$response = new Response();
		if( null == $request->get('callback'))
			$response->setContent(json_encode($result));
		else
			$response->setContent($callback.'('.json_encode($result).')');
		$response->headers->set('Access-Control-Allow-Origin', '*');
		return $response;
	}
	/**
	 * @Route("/lottery", name="api_lottery")
	 */
	public function lotteryAction(Request $request)
	{
		$award = array(5,42,0,32,42,162,300,362);
		$award_rule = array(2,0,1,0,0,0,0);//0为每周平均数量,1为每月平均数量,2为每双月平均数量
		$award_average = array(1,1,1,1,1,3,7,8);
		$session = $request->getSession();
		$timestamp = time();
		if( null == $session->get('id')){
			$result = array(
				'ret' => 3001,
				'msg' => '您没有抽奖资格',
			);
		}
		elseif( $timestamp > strtotime('2016-12-31 23:59:59') || $timestamp < strtotime('2016-03-15 12:00')){
			$result = array(
				'ret' => 4001,
				'msg' => '活动未开始或者已结束',
			);
		}
		else{
			$em = $this->getDoctrine()->getManager();
			$em->getConnection()->beginTransaction();
			try{
				$info = $em->getRepository('AppBundle:Info')->find($session->get('id'));

				if( $info == null ){
					$result = array(
						'ret' => 1001,
						'msg' => '该信息不存在',
					);
				}
				else{
					$mobile = $info->getMobile();
					#当天已抽过将
					$repo = $em->getRepository('AppBundle:Info');
					$qb = $repo->createQueryBuilder('a');
					$qb->select('COUNT(a)');
					$qb->where('a.mobile = :mobile AND a.createTime >= :createTime1 AND a.createTime < :createTime2 AND a.hasLottery = :hasLottery');
					$qb->setParameter('mobile', $mobile);
					$qb->setParameter('hasLottery', true);
					
					$date1 = date('Y-m-d',$timestamp);
					$date2 = date('Y-m-d', strtotime('+1 day', $timestamp));
					$qb->setParameter(':createTime1', new \DateTime($date1), \Doctrine\DBAL\Types\Type::DATETIME);
					$qb->setParameter(':createTime2', new \DateTime($date2), \Doctrine\DBAL\Types\Type::DATETIME);
					$count1 = $qb->getQuery()->getSingleScalarResult();

					#该手机号已中将
					$repo = $em->getRepository('AppBundle:Info');
					$qb = $repo->createQueryBuilder('a');
					$qb->select('COUNT(a)');
					$qb->where('a.mobile = :mobile AND a.prize > 0');
					$qb->setParameter('mobile', $mobile);
					$count2 = $qb->getQuery()->getSingleScalarResult();

					$rand1 = rand(1,10);
					$rand2 = rand(1,10);
					$prize = $rand1 == $rand2 ? rand(1,8) : 0;
					#该奖品已发数量
					$repo = $em->getRepository('AppBundle:Info');
					$qb = $repo->createQueryBuilder('a');
					$qb->select('COUNT(a)');
					$qb->where('a.prize = :prize');
					$qb->setParameter(':prize', $prize);
					$count3 = $qb->getQuery()->getSingleScalarResult();
					#今天已抽奖,已中奖,奖品已发完
					if($count1 >0 || $count2 >0){
						$prize = $info->getPrize();
					}
					if($count3 >= $award[$prize-1]){
						$prize = 0;
					}
					elseif($prize != 0){
						#该奖品平均发放情况
						if($award_rule[$prize-1] == 0){
							$w = date('w');
							$date1 = date('Y-m-d 00:00:00', $timestamp-$w*24*3600);
							$date2 = date('Y-m-d 23:59:59', strtotime($date1) + 7*24*3600 -1);
						}
						elseif($award_rule[$prize-1] == 1){
							$t = date('t', $timestamp);
							$date1 = date('Y-m-01 00:00:00', $timestamp);
							$date2 = date('Y-m-', $timestamp).$t.' 23:59:59';
						}
						else{
							$n = date('n', $timestamp);
							if($n%2 == 1){
								$timestamp1 = $timestamp;
								$temp_date = date('Y').'-'.(date('n')+1).'-01';
								$timestamp2 = strtotime($temp_date);
								$t = date('t', $timestamp2);
							}
							else{
								$t = date('t', $timestamp);
								$timestamp1 = $timestamp - $t*24*3600;
								$timestamp2 = $timestamp;
							}
							$date1 = date('Y-m-01 00:00:00', $timestamp1);
							$date2 = date('Y-m-', $timestamp2).$t.' 23:59:59';
						}
						$repo = $em->getRepository('AppBundle:Info');
						$qb = $repo->createQueryBuilder('a');
						$qb->select('COUNT(a)');
						$qb->where('a.prize = :prize AND a.createTime >= :createTime1 AND a.createTime <= :createTime2');
						$qb->setParameter(':prize', $prize);
						$qb->setParameter(':createTime1', new \DateTime($date1), \Doctrine\DBAL\Types\Type::DATETIME);
						$qb->setParameter(':createTime2', new \DateTime($date2), \Doctrine\DBAL\Types\Type::DATETIME);
						$count4 = $qb->getQuery()->getSingleScalarResult();
						if($count4 >= $award_average[$prize-1]){
							$prize = 0;
						}
					}
					$info->setPrize($prize);
					$info->setHasLottery(1);
					$em->persist($info);
					$em->flush();
					$result = array(
						'ret' => 0,
						'msg' => '',
						'data' => array('prize'=>$prize),
					);
				}
				$em->getConnection()->commit();
			}
			catch (Exception $e) {
				$em->getConnection()->rollback();
				$result = array(
					'ret' => 2001,
					'msg' => $e->getMessage(),
				);
			}
		}
		$callback = $request->get('callback') ? : 'callback';
		//return new Response($callback.'('.json_encode($result).')');
		$response = new Response();
		if( null == $request->get('callback'))
			$response->setContent(json_encode($result));
		else
			$response->setContent($callback.'('.json_encode($result).')');
		$response->headers->set('Access-Control-Allow-Origin', '*');
		return $response;
	}
	/**
	 * @Route("/count", name="api_count")
	 */
	public function countAction(Request $request,$id = null)
	{
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Info');
		$qb = $repo->createQueryBuilder('a');
		$qb->select('COUNT(DISTINCT a.mobile)');
		$count = $qb->getQuery()->getSingleScalarResult();
		$result = array(
			'ret' => 0,
			'data' => array('count'=>$count),
		);
		$callback = $request->get('callback') ? : 'callback';
		//return new Response($callback.'('.json_encode($result).')');
		$response = new Response();
		if( null == $request->get('callback'))
			$response->setContent(json_encode($result));
		else
			$response->setContent($callback.'('.json_encode($result).')');
		$response->headers->set('Access-Control-Allow-Origin', '*');
		return $response;
	}

	/**
	 * @Route("/sign", name="api_wechat_sign")
	 */
	public function getWechatSign(Request $request)
	{
		if( null == $request->get('url')){
			$result = json_encode(array('ret'=>1001,'msg'=>'url不能为空~'));
		}
		else{
			$url = 'http://campaign.slek.com.cn/wxtoken/ticket.php?url='.urlencode($request->get('url'));
			$result = file_get_contents($url);
		}
		$response = new Response();
		if( null == $request->get('callback'))
			$response->setContent($result);
		else
			$response->setContent($callback.'('.$result.')');
		$response->headers->set('Access-Control-Allow-Origin', '*');
		return $response;
	}
	protected function getRank($likeNum)
	{
		$repository = $this->getDoctrine()->getRepository('AppBundle:Info');
		$query = $repository
			->createQueryBuilder('a')
			->select('count(a)')
			->where('a.likeNum > :likeNum')
			->setParameter('likeNum', $likeNum)
			->orderBy('a.likeNum','ASC')
			->getQuery();
		return $query->getSingleScalarResult() + 1;
	}
	protected function getCity($ip)
	{
		$url = 'http://api.map.baidu.com/location/ip';
		$data = array(
			'ip' => $ip,
			'ak' => '6GO8GPtURunrgj5cc5sCyfdt'
		);
		$result = json_decode(Helper\HttpClient::get($url.'?'.http_build_query($data)),true);
		if($result['status'] == 0)
			return $result['content']['address_detail']['city'];
		else
			return '--';
	}
}
