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
		$request = $event->getRequest();
		$session = $request->getSession();
		if($request->getClientIp() == '127.0.0.1'){
			$session->set('open_id', 'o2-sBj0oOQJCIq6yR7I9HtrqxZcY');
			$session->set('user_id', 1);
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
			$session->set('wechat_title', $this->container->getParameter('wechat_title'));
			$session->set('wechat_desc', $this->container->getParameter('wechat_desc'));
			$session->set('wechat_img_url', $this->container->getParameter('wechat_img_url'));
			$session->set('wx_share_url', $request->getUriForPath('/'));
			$session->set('wx_app_id', $wx->appId);
			$session->set('wx_timestamp', $wx->timestamp);
			$session->set('wx_nonce_str', $wx->nonceStr);
			$session->set('wx_signature', $wx->signature);
		}
		
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