<?php
/**
 * Created by PhpStorm.
 * User: Alexa
 * Date: 15/6/4
 * Time: 下午3:16
 */
namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Httpkernel\Event\FilterControllerEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Wechat;
#use AppBundle\Weibo;
use Doctrine\ORM\EntityManager;

class OAuthListener
{
	protected $container;
	protected $router;
	protected $em;
	public function __construct($router, \Symfony\Component\DependencyInjection\Container $container,EntityManager $em)
	{
		$this->container = $container;
		$this->router = $router;
		$this->em = $em;
	}
	/*
	public function onKernelController(FilterControllerEvent $event)
	{
		//$controller = $event->getController();
		// 此处controller可以被该换成任何PHP可回调函数
		//$event->setController($controller);
	}
	*/
	public function onKernelRequest(GetResponseEvent $event)
	{
		return;
		/*
		$request = $event->getRequest();
		$session = $request->getSession();
		if($request->getClientIp() == '127.0.0.1'){
			$session->set('open_id', 'o2-sBj0oOQJCIq6yR7I9HtrqxZcY');
			$session->set('user_id', 1);
			//var_dump('http://'.$request->getHost().$this->container->getParameter('wechat_img_url'));
		}
		else{
			if( $session->get('open_id') === null 
				&& $request->attributes->get('_route') !== '_callback' 
				&& stripos($request->attributes->get('_controller'), 'DefaultController') !== false
			){
				$app_id = $this->container->getParameter('wechat_appid');
				$session->set('redirect_url', $request->getUri());
				$state = '';
				$callback_url = $request->getUriForPath('/callback');
				//$callback_url = $this->router->generate('_callback','');
				$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$app_id."&redirect_uri=".$callback_url."&response_type=code&scope=snsapi_userinfo&state=$state#wechat_redirect";
				$event->setResponse(new RedirectResponse($url));
			}
			$appId = $this->container->getParameter('wechat_appid');
			$appSecret = $this->container->getParameter('wechat_secret');
			$wechat = new Wechat\Wechat($appId, $appSecret);
			$wx = (Object)$wechat->getSignPackage();
			$desc = array(
				'别人都抢红包玩，我只想造个福和你玩。',
				'我呕心沥血为你造了个福，笑纳了呗~',
				'如果你收到过这样的祝福，一定要收藏。'
			);

			$session->set('wechat_desc', $desc[rand(0,2)]);
			if( null != $session->get('open_id')){
				$em = $this->em;
				$user = $em->getRepository('AppBundle:WechatUser')->findOneByOpenId($session->get('open_id'));
				$desc[] = $user->getNickname().'想和你一起造福飞欧洲，你造吗？';
				$session->set('wechat_desc', $desc[rand(0,3)]);
			}
			$session->set('wechat_title', '开启造福之旅 赢取汉莎机票');
			$session->set('wechat_img_url', 'http://'.$request->getHost().$this->container->getParameter('wechat_img_url'));
			$session->set('wx_share_url', $request->getUriForPath('/'));
			$session->set('wx_app_id', $wx->appId);
			$session->set('wx_timestamp', $wx->timestamp);
			$session->set('wx_nonce_str', $wx->nonceStr);
			$session->set('wx_signature', $wx->signature);
		}
		*/
		
	}
	/*
	public function onKernelResponse(FilterResponseEvent $event)
	{
		$response = $event->getResponse();
		$request = $event->getRequest();
		if ($request->query->get('option') == 3) {
			$response->headers->setCookie(new Cookie("test", 1));
		}
	}
	*/
}