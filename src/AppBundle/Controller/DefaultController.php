<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Helper;
use AppBundle\Wechat;
use AppBundle\Entity;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Cookie;
#use AppBundle\Weibo;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use Imagine\Image\Palette;
use Imagine\Imagick\Drawer;
use Imagine\Imagick\Font;
use Symfony\Component\Filesystem\Filesystem;
 use Imagine\Image\Color;

#use Symfony\Component\Validator\Constraints\Image;

class DefaultController extends Controller
{
	/**
	 * @Route("/", name="default")
	 */
	public function defaultAction(Request $request)
	{
		return $this->redirect($this->generateUrl('_index'));
		//return $this->render('AppBundle:default:index.html.twig');
	}
	/**
	 * @Route("/mobile", name="_index")
	 */
	public function indexAction(Request $request)
	{
		$session = $request->getSession();
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Info');
		$qb = $repo->createQueryBuilder('a');
		$qb->select('COUNT(DISTINCT a.mobile)');
		//var_dump($qb->getQuery());
		$count = $qb->getQuery()->getSingleScalarResult();
		$session->set('wx_share_url','http://'.$request->getHost().$this->generateUrl('_index'));
		$session->set('wx_share_img','http://'.$request->getHost().'/bundles/app/default/images/share.jpg');
		return $this->render('AppBundle:default:index.html.twig',array('count'=>$count));
	}
	/**
	 * @Route("/mobile/error", name="_error")
	 */
	public function errorAction(Request $request)
	{
		$errorInfo = $request->get('info') ? urldecode($request->get('info')) : '';
		$url = null != $request->get('url') ? urldecode($request->get('url')) : $this->generateUrl('_index');
		return $this->render('AppBundle:default:error.html.twig',array('errorInfo'=>$errorInfo,'url'=>$url));
	}
	/**
	 * @Route("/mobile/success/{id}", name="_success")
	 */
	public function successAction(Request $request, $id = null)
	{
		$session = $request->getSession();
		if( null == $id ){
			return $this->redirect($this->generateUrl('_index'));
		}

		$info = $this->getDoctrine()->getRepository('AppBundle:Info')->find($id);
		if( $info == null || $info->getIsActive() == false ){
			$info = $this->getDoctrine()->getRepository('AppBundle:Info')->find(1);
		}

		$cacheManager = $this->container->get('liip_imagine.cache.manager');
		$session->set('wx_share_url','http://'.$request->getHost().$this->generateUrl('_info',array('id'=>$id)));
		//$session->set('wx_share_img',$cacheManager->getBrowserPath('uploads/'.$info->getHeadImg(), 'thumb2'));
		
		$share_img = 'http://'.$request->getHost().$this->container->get('templating.helper.assets')->getUrl('/luckydraw2015dec/uploads/'.$info->getHeadImg());
		$session->set('wx_share_img', $share_img);
		return $this->render('AppBundle:default:success.html.twig', array('success'=>true));
	}
	/**
	 * @Route("/mobile/info/{id}", name="_info")
	 */
	public function infoAction(Request $request, $id = null)
	{
		$session = $request->getSession();
		if( null == $id){
			return $this->redirect($this->generateUrl('_index'));
		}
		$info = $this->getDoctrine()->getRepository('AppBundle:Info')->find($id);
		if( $info == null || $info->getIsActive() == false ){
			$info = $this->getDoctrine()->getRepository('AppBundle:Info')->find(1);
		}
		$session->set('wx_share_url','http://'.$request->getHost().$this->generateUrl('_info',array('id'=>$id)));
		$share_img = 'http://'.$request->getHost().$this->container->get('templating.helper.assets')->getUrl('/uploads/'.$info->getHeadImg());
		$session->set('wx_share_img', $share_img);
		return $this->render('AppBundle:default:info.html.twig', array('info'=>$info));
	}
	/**
	 * @Route("/mobile/top", name="_top")
	 */
	public function topAction(Request $request)
	{
		$session = $request->getSession();
		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Story');
		$qb = $repo->createQueryBuilder('a');
		$qb->orderBy('a.likeNum','desc');
		$qb->setMaxResults(20);
		$list = $qb->getQuery()->getResult();
		$session->set('wx_share_url','http://'.$request->getHost().$this->generateUrl('_index'));
		$session->set('wx_share_img','http://'.$request->getHost().'/bundles/app/default/images/share.jpg');
		return $this->render('AppBundle:default:top.html.twig',array('list'=>$list));
	}
  /**
   * @Route("/callback", name="_callback")
   */
  /*
  public function wechatAction(Request $request)
  {
	$session = $request->getSession();
	$code = $request->query->get('code');
	//$state = $request->query->get('state');
	$app_id = $this->container->getParameter('wechat_appid');
	$secret = $this->container->getParameter('wechat_secret');
	$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $app_id . "&secret=" . $secret . "&code=$code&grant_type=authorization_code";
	$data = Helper\HttpClient::get($url);
	//var_dump($data);
	$token = json_decode($data);
	//$session->set('open_id', null);
	if ( isset($token->errcode) && $token->errcode != 0) {
		return new Response('something bad !');
	}

	$wechat_token = $token->access_token;
	$wechat_openid = $token->openid;
	$url = "https://api.weixin.qq.com/sns/userinfo?access_token={$wechat_token}&openid={$wechat_openid}";
	$data = Helper\HttpClient::get($url);
	$user_data = json_decode($data);

	$em = $this->getDoctrine()->getManager();
	$em->getConnection()->beginTransaction();
	try{
		$session->set('open_id', $user_data->openid);
		$repo = $em->getRepository('AppBundle:WechatUser');
		$qb = $repo->createQueryBuilder('a');
		$qb->select('COUNT(a)');
		$qb->where('a.openId = :openId');
		$qb->setParameter('openId', $user_data->openid);
		$count = $qb->getQuery()->getSingleScalarResult();
		if($count <= 0){
			$wechat_user = new Entity\WechatUser();
			$wechat_user->setOpenId($wechat_openid);
			$wechat_user->setNickName($user_data->nickname);
			$wechat_user->setCity($user_data->city);
			$wechat_user->setGender($user_data->sex);
			$wechat_user->setProvince($user_data->province);
			$wechat_user->setCountry($user_data->country);
			$wechat_user->setHeadImg($user_data->headimgurl);
			$wechat_user->setCreateIp($request->getClientIp());
			$wechat_user->setCreateTime(new \DateTime('now'));
			$em->persist($wechat_user);
			$em->flush();
		}
		else{
			$wechat_user = $em->getRepository('AppBundle:WechatUser')->findOneBy(array('openId' => $wechat_openid));
			$session->set('user_id', $wechat_user->getId());
		}

		$redirect_url = $session->get('redirect_url') == null ? $this->generateUrl('_index') : $session->get('redirect_url');
		$em->getConnection()->commit();
		return $this->redirect($redirect_url);
	}
	catch (Exception $e) {
		$em->getConnection()->rollback();
		return new Response($e->getMessage());
	}
  }
  */
}
