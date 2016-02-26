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
			elseif( !preg_match('/^1\d{10}$/', $request->get('mobile'))){
				$result['ret'] = 1006;
				$result['msg'] = '手机格式不正确';
			}
			else{
				$username = $request->get('username');
				$mobile = $request->get('mobile');
				
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
				      $info->setCreateIp($request->getClientIp());
				      $info->setCreateTime(new \DateTime('now'));
							$em->persist($info);
			        $em->flush();
				      $em->getConnection()->commit();
							$session->set('mobile',$mobile);
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
		$qb->setMaxResults($limit);
		$qb->setFirstResult($offset);
    $info = $qb->getQuery()->getResult();
    
    $data = array();
    $cacheManager = $this->container->get('liip_imagine.cache.manager');
    foreach ($info as $value) {
    	$data[] = array(
    		'username' => $value->getUsername(),
    		'mobile' => $value->getMobile(),
    		'headImg' => $cacheManager->getBrowserPath('uploads/'.$value->getHeadImg(), 'thumb1'),
    	);
    }
    $result = array(
    	'ret' => 0,
    	'data' => $data,
    );
    $callback = $request->get('callback') ? : 'callback';
		return new Response($callback.'('.json_encode($result).')');
	}
	/**
	 * @Route("/info/{id}", name="api_info")
	 */
	public function infoAction(Request $request,$id = null)
	{
		if( null === $id){
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
	  		'headImg' => $cacheManager->getBrowserPath('uploads/'.$info->getHeadImg(), 'thumb1'),
			);
			$result = array(
	    	'ret' => 0,
	    	'data' => $data,
	    );
		}
    $callback = $request->get('callback') ? : 'callback';
		return new Response($callback.'('.json_encode($result).')');
	}
	/**
	 * @Route("/like/{id}", name="api_like")
	 */
	public function likeAction(Request $request,$id = null)
	{
		$info = $this->getDoctrine()->getRepository('AppBundle:Info')->find($id);
		if( $info == null ){
			$result = array(
	    	'ret' => 1001,
	    	'msg' => '该信息不存在',
	    );
		}
		else{
			$result = array(
	    	'ret' => 0,
	    	'msg' => '',
	    );
		}
		$callback = $request->get('callback') ? : 'callback';
		return new Response($callback.'('.json_encode($result).')');
	}
	/**
	 * @Route("/lottery/{id}", name="api_lottery")
	 */
	public function lotteryAction(Request $request,$id = null)
	{
		$info = $this->getDoctrine()->getRepository('AppBundle:Info')->find($id);
		if( $info == null ){
			$result = array(
	    	'ret' => 1001,
	    	'msg' => '该信息不存在',
	    );
		}
		else{
			$result = array(
	    	'ret' => 0,
	    	'msg' => '',
	    );
		}
		$callback = $request->get('callback') ? : 'callback';
		return new Response($callback.'('.json_encode($result).')');
	}
}
