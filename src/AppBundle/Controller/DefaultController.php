<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Helper;
#use AppBundle\Weibo;

#use Symfony\Component\Validator\Constraints\Image;

class DefaultController extends Controller
{
    protected $blacklist = array('1042', '17421');
    /**
     * @Route("/", name="default")
     */
    public function defaultAction(Request $request)
    {
        $params = $request->query->all();

        return $this->redirect($this->generateUrl('_index').'?'.http_build_query($params));
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
        //$qb->where('a.isActive = 1 AND a.type = 0');
        $qb->select('COUNT(a)');
        $count = $qb->getQuery()->getSingleScalarResult();

        $session->set('wx_share_wish', null);
        $session->set('wx_share_url', 'http://'.$request->getHost().$this->generateUrl('_index'));
        $session->set('wx_share_img', 'http://'.$request->getHost().'/luckydraw2015dec/bundles/app/default/images/share.jpg');

        return $this->render('AppBundle:default:index.html.twig', array('count' => $count));
    }
    /**
     * @Route("/mobile/error", name="_error")
     */
    public function errorAction(Request $request)
    {
        $errorInfo = $request->get('info') ? urldecode($request->get('info')) : '';
        $url = null != $request->get('url') ? urldecode($request->get('url')) : $this->generateUrl('_index');

        return $this->render('AppBundle:default:error.html.twig', array('errorInfo' => $errorInfo, 'url' => $url));
    }
    /**
     * @Route("/mobile/success/{id}", name="_success")
     */
    public function successAction(Request $request, $id = null)
    {
        $session = $request->getSession();
        if (null == $id) {
            return $this->redirect($this->generateUrl('_index'));
        }

        $info = $this->getDoctrine()->getRepository('AppBundle:Info')->find($id);
        if ($info == null || $info->getIsActive() == false) {
            $info = $this->getDoctrine()->getRepository('AppBundle:Info')->find(1);
        }

        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        //$session->set('wx_share_wish', trim(str_replace(PHP_EOL, '', $info->getWishText())));
        $session->set('wx_share_wish', preg_replace('/\s*/', '', $info->getWishText()));
        $session->set('wx_share_url', 'http://'.$request->getHost().$this->generateUrl('_info', array('id' => $id)));
        //$session->set('wx_share_img',$cacheManager->getBrowserPath('uploads/'.$info->getHeadImg(), 'thumb2'));

        $share_img = 'http://'.$request->getHost().$this->container->get('templating.helper.assets')->getUrl('/uploads/thumb/'.$info->getHeadImg());
        $session->set('wx_share_img', $share_img);

        return $this->render('AppBundle:default:success.html.twig', array('success' => true));
    }
    /**
     * @Route("/mobile/info/{id}", name="_info")
     */
    public function infoAction(Request $request, $id = null)
    {
        if (in_array($id, $this->blacklist)) {
            return new Response('', 500);
        }
        $session = $request->getSession();
        if (null == $id) {
            return $this->redirect($this->generateUrl('_index'));
        }
        $info = $this->getDoctrine()->getRepository('AppBundle:Info')->find($id);
        if ($info == null || $info->getIsActive() == false) {
            $info = $this->getDoctrine()->getRepository('AppBundle:Info')->find(1);
        }
        //$session->set('wx_share_wish', trim(str_replace(PHP_EOL, '', $info->getWishText())));
        $session->set('wx_share_wish', preg_replace('/\s*/', '', $info->getWishText()));
        $session->set('wx_share_url', 'http://'.$request->getHost().$this->generateUrl('_info', array('id' => $id)));
        $share_img = 'http://'.$request->getHost().$this->container->get('templating.helper.assets')->getUrl('/uploads/thumb/'.$info->getHeadImg());
        $session->set('wx_share_img', $share_img);

        return $this->render('AppBundle:default:info.html.twig', array('info' => $info));
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
        $qb->orderBy('a.likeNum', 'desc');
        $qb->setMaxResults(20);
        $list = $qb->getQuery()->getResult();
        $session->set('wx_share_wish', null);
        $session->set('wx_share_url', 'http://'.$request->getHost().$this->generateUrl('_index'));
        $session->set('wx_share_img', 'http://'.$request->getHost().'/bundles/app/default/images/share.jpg');

        return $this->render('AppBundle:default:top.html.twig', array('list' => $list));
    }

    /**
     * @Route("/mobile/sid", name="_sid")
     */
    public function testAction(Request $request)
    {
        $session = $request->getSession();
        $session->set('_test', '2323232323');
        $sid = $session->getId();
        $result = array('ret' => 0, 'sid' => $sid);
        $callback = $request->get('callback') ?: 'callback';
        $response = new Response();
        if (null == $request->get('callback')) {
            $response->setContent(json_encode($result));
        } else {
            $response->setContent($callback.'('.json_encode($result).')');
        }
        //$response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
        //return new JsonResponse(array('ret'=>0,'sid'=>$sid));
    }
    /**
     * @Route("/mobile/t", name="_t")
     */
    public function tAction(Request $request)
    {
        return $this->render('AppBundle:default:t.html.twig');
    }
}
