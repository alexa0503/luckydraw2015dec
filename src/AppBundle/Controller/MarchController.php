<?php
/**
 * Created by PhpStorm.
 * User: Alexa
 * Date: 16/3/20
 * Time: 下午6:34
 */
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Helper;
use AppBundle\Wechat;
use AppBundle\Entity;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\HttpFoundation\Cookie;

class MarchController extends Controller
{
    /**
     * @Route("/march", name="march_index")
     */
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        $session->set('wx_share_url','http://'.$request->getHost().$this->generateUrl('march_index'));
        $session->set('wx_share_img','http://'.$request->getHost().'/luckydraw2015dec/bundles/app/default/images/share.jpg');
        return $this->render('AppBundle:march:index.html.twig');
    }
}
