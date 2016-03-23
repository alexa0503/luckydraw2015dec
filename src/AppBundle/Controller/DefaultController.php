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
		$qb->where('a.isActive = 1 AND a.type = 0');
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
}
