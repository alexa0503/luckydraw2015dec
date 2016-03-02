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
#use AppBundle\Weibo;
#use Imagine\Gd\Imagine;
#use Imagine\Image\Box;
#use Imagine\Image\Point;
#use Imagine\Image\ImageInterface;
#use Imagine\Image\Palette;
#use Symfony\Component\Filesystem\Filesystem;
#use Symfony\Component\Validator\Constraints\Image;

class ApiController extends Controller
{
	/**
	 * @Route("/form", name="api_form")
	 */
	public function formAction(Request $request)
	{
    $session = $request->getSession();
		$result = array('ret'=>1002,'msg'=>'来源不正确');
		if($request->getMethod() == 'POST'){
			if( null == $request->files->get('headImg')){
				$result['ret'] = 1003;
				$result['msg'] = '头像不能为空';
			}
			elseif( null == $request->get('username')){
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
	        $repo = $em->getRepository('AppBundle:Info');
	        $qb = $repo->createQueryBuilder('a');
	        $qb->select('COUNT(a)');
	        $qb->where('a.mobile = :mobile');
	        $qb->setParameter('mobile', $mobile);
	        $count = $qb->getQuery()->getSingleScalarResult();
	        if($count <= 0){
	        	$image = $this->get('image.handle');
						if( !$image->upload($request->files->get('headImg'))){
							$result['ret'] = 1007;
							$result['msg'] = '头像的格式不正确';
						}
						else{
							$image_path = $image->create();
		        	$info = new Entity\Info();
							$info->setUsername($username);
							$info->setMobile($mobile);
							$info->setHeadImg($image_path);
							$info->setWishText($wish_text);
				      $info->setCreateIp($request->getClientIp());
				      $info->setCreateTime(new \DateTime('now'));
							$em->persist($info);
			        $em->flush();
				      $em->getConnection()->commit();
							$session->set('id',$info->getId());
							$result['ret'] = 0;
							$result['msg'] = '';
						}
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
			return $this->redirect(urldecode($request->get('url')));
		}
		elseif( $result['ret'] !== 0 && null !== $request->get('failUrl')){
			return $this->redirect(urldecode($request->get('failUrl')).'?info='.urlencode($result['msg']));
		}

		return new Response(json_encode($result));
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
		if( null !== $request->get('mobile')){
			$qb->andWhere('a.mobile LIKE :mobile');
			$qb->setParameter(':mobile', '%'.$request->get('mobile').'%');
		}
		if( null !== $request->get('username')){
			$qb->andWhere('a.username LIKE :username');
			$qb->setParameter(':username', '%'.$request->get('username').'%');
		}
		$limit = 20;
		$offset = ($page-1)*$limit;
		if( null == $request->get('order')){
			$order = array('username','desc');
		}
		else{
			$order = explode('.', $request->get('order'));
			if( isset($order[1]) && !in_array(strtolower($order[1]), array('desc','asc')))
				$order[1] = 'desc';
			if( isset($order[0]) && !in_array($order[0], array('username','createTime','mobile')))
				$order[0] = 'createTime';
		}
		$qb->orderBy('a.'.$order[0],strtoupper($order[1]));
		$qb->setMaxResults($limit);
		$qb->setFirstResult($offset);
    $info = $qb->getQuery()->getResult();
    
    $data = array();
    $cacheManager = $this->container->get('liip_imagine.cache.manager');
    foreach ($info as $value) {
    	$data[] = array(
    		'id' => $value->getId(),
    		'username' => $value->getUsername(),
    		'mobile' => $value->getMobile(),
    		'likeNum' => $value->getLikeNum(),
    		'wishText' => $value->getWishText(),
    		'headImg' => 'http://'.$request->getHost().'/uploads/'.$value->getHeadImg(),
    		'thumb' => $cacheManager->getBrowserPath('uploads/'.$value->getHeadImg(), 'thumb1'),
    		'grayHeadImg' => 'http://'.$request->getHost().'/uploads/gray/'.$value->getHeadImg(),
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
		$response->headers->set('Access-Control-Allow-Origin', 'http://api.dev.com');
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
		$info = $this->getDoctrine()->getRepository('AppBundle:Info')->find($id);
		if( $info == null ){
			$result = array(
	    	'ret' => 1001,
	    	'msg' => '没有您要的数据',
	    );
		}
		else{
			$cacheManager = $this->container->get('liip_imagine.cache.manager');
			$data = array(
				'username' => $info->getUsername(),
	  		'mobile' => $info->getMobile(),
    		'likeNum' => $info->getLikeNum(),
    		'wishText' => $info->getWishText(),
    		'headImg' => 'http://'.$request->getHost().'/uploads/'.$info->getHeadImg(),
    		'thumb' => $cacheManager->getBrowserPath('uploads/'.$info->getHeadImg(), 'thumb1'),
    		'grayHeadImg' => 'http://'.$request->getHost().'/uploads/gray/'.$info->getHeadImg(),
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
		$response->headers->set('Access-Control-Allow-Origin', 'http://api.dev.com');
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

		    $repo = $em->getRepository('AppBundle:LikeLog');
				$qb = $repo->createQueryBuilder('a');
	      $qb->select('COUNT(a)');
	      $qb->where('a.createIp = :createIp');
	      $qb->setParameter('createIp', $create_ip);
	      $count = $qb->getQuery()->getSingleScalarResult();

	      if( $count < 1){
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
			    );
	      }
	      else{
	      	$result = array(
			    	'ret' => 1200,
			    	'msg' => '您已经投过票了',
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
		$response->headers->set('Access-Control-Allow-Origin', 'http://api.dev.com');
		return $response;
	}
	/**
	 * @Route("/lottery", name="api_lottery")
	 */
	public function lotteryAction(Request $request)
	{
		$session = $request->getSession();
		if( null == $session->get('id')){
			$result = array(
	    	'ret' => 3001,
	    	'msg' => '您没有抽奖资格',
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
				elseif( $info->getHasLottery() == true){
					$result = array(
			    	'ret' => 1002,
			    	'msg' => '您已经抽过将了哦~',
			    );
				}
				else{
					$em = $this->getDoctrine()->getManager();
					$rand1 = rand(1,2);
					$rand2 = rand(1,2);
					$prize = $rand1 == $rand2 ? rand(1,4) : 0;
					$info->setHasLottery(true);
					$info->setPrize($prize);
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
		$response->headers->set('Access-Control-Allow-Origin', 'http://api.dev.com');
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
    $qb->select('COUNT(a)');
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
		$response->headers->set('Access-Control-Allow-Origin', 'http://api.dev.com');
		return $response;
	}
}
