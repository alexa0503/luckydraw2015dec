<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    protected $blacklist = array('1042', '17421');

    /**
     * @Route("/form", name="api_form")
     */
    public function formAction(Request $request)
    {
        $session = $request->getSession();
        $result = array('ret' => 1002, 'msg' => '来源不正确');
        if ($request->getMethod() == 'POST') {
            if (null == $request->get('username') || empty(trim($request->get('username'), " \t\n\r\0\x0B"))) {
                $result['ret'] = 1004;
                $result['msg'] = '用户名不能为空';
            } elseif (null == $request->get('mobile')) {
                $result['ret'] = 1005;
                $result['msg'] = '手机号不能为空';
            } elseif (null == $request->get('wishText') || empty(trim($request->get('wishText'), " \t\n\r\0\x0B"))) {
                $result['ret'] = 1008;
                $result['msg'] = '心愿清单不能为空';
            } elseif (!preg_match('/^1\d{10}$/', $request->get('mobile'))) {
                $result['ret'] = 1006;
                $result['msg'] = '手机格式不正确';
            } else {
                $username = trim(strip_tags($request->get('username')), " \t\n\r\0\x0B");
                $mobile = trim(strip_tags($request->get('mobile')), " \t\n\r\0\x0B");
                $wish_text = trim(strip_tags($request->get('wishText')), " \t\n\r\0\x0B");

                $em = $this->getDoctrine()->getManager();
                $em->getConnection()->beginTransaction();
                try {
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
                    if ($count <= 0) {
                        $image = $this->get('image.handle');
                        if ($request->get('isWechat') == '1') {
                            $token = file_get_contents('http://campaign.slek.com.cn/wxtoken/token.php');
                            if ($image->getImageFromWechat($request->get('imageId'), $token)) {
                                $hasImage = true;
                            }
                        } else {
                            if (null != $request->files->get('headImg') && $image->upload($request->files->get('headImg'))) {
                                $hasImage = true;
                            }
                        }

                        if (!$hasImage) {
                            $image_path = 'default.png';
                            //$result['ret'] = 1007;
                            //$result['msg'] = '图片上传不正确';
                        } else {
                            $image_path = $image->create();
                        }
                        $is_active = true;
                        $file = $this->file_path = preg_replace('/app$/si', 'web/', $this->get('kernel')->getRootDir()) . 'keyword.txt';
                        $keywords = explode(',', file_get_contents($file));
                        foreach ($keywords as $keyword) {
                            $pattern = '/(' . $keyword . ')/i';
                            if (preg_match($pattern, $wish_text) || preg_match($pattern, $username)) {
                                $is_active = false;
                                break;
                            }
                        }

                        $text = preg_replace('/\s|[\x00-\x1f]/i', '', $wish_text);
                        if (!empty($text)) {
                            $info = new Entity\Info();
                            $info->setUsername($username);
                            $info->setMobile($mobile);
                            $info->setHeadImg($image_path);
                            $info->setWishText($wish_text);
                            $info->setCreateIp($request->getClientIp());
                            $info->setCreateTime(new \DateTime('now'));
                            $info->setIsActive($is_active);
                            $info->setType(0);
                            if (!empty($info->getWishText())) {
                                $em->persist($info);
                                $em->flush();
                                $em->getConnection()->commit();
                                $session->set('id', $info->getId());
                                $result['ret'] = 0;
                                $result['msg'] = '';
                                $result['data'] = array('id' => $info->getId(), 'username' => $username, 'headImg' => 'http://' . $request->getHost() . '/uploads/' . $info->getHeadImg());
                            } else {
                                $result['ret'] = 1200;
                                $result['msg'] = '含有非法字符';
                            }

                        } else {
                            $result['ret'] = 1200;
                            $result['msg'] = '含有非法字符';
                        }
                    } else {
                        $result['ret'] = 1100;
                        $result['msg'] = '该手机已经被注册';
                    }
                } catch (Exception $e) {
                    $em->getConnection()->rollback();
                    $result['ret'] = 1001;
                    $result['msg'] = $e->getMessage();
                }
            }
        }
        if ($result['ret'] === 0 && null !== $request->get('url')) {
            //var_dump(urldecode($request->get('url')).'/'.$info->getId());
            return $this->redirect(urldecode($request->get('url')) . '/' . $info->getId());
        } elseif ($result['ret'] !== 0 && null !== $request->get('failUrl')) {
            return $this->redirect(urldecode($request->get('failUrl')) . '?info=' . urlencode($result['msg']));
        }

        $callback = $request->get('callback') ?: 'callback';
        $response = new Response();
        if (null == $request->get('callback'))
            $response->setContent(json_encode($result));
        else
            $response->setContent($callback . '(' . json_encode($result) . ')');
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
        $qb->where('a.isActive = 1 AND a.type = 0');
        if (null !== $request->get('mobile')) {
            $qb->andWhere('a.mobile LIKE :mobile');
            $qb->setParameter(':mobile', '%' . $request->get('mobile') . '%');
        }
        if (null !== $request->get('username')) {
            $qb->andWhere('a.username LIKE :username');
            $qb->setParameter(':username', '%' . $request->get('username') . '%');
        }
        $qb->andWhere('a.username != :username1');
        $qb->setParameter(':username1', '');
        $qb->andWhere('a.wishText != :wishText');
        $qb->setParameter(':wishText', '');
        $limit = 8;
        $offset = ($page - 1) * $limit;
        if (null == $request->get('order')) {
            $order = array('likeNum', 'desc');
        } else {
            $order = explode('.', $request->get('order'));
            if (isset($order[1]) && !in_array(strtolower($order[1]), array('desc', 'asc')))
                $order[1] = 'desc';
            if (isset($order[0]) && !in_array($order[0], array('username', 'createTime', 'mobile', 'likeNum')))
                $order[0] = 'createTime';
        }
        $qb->orderBy('a.' . $order[0], strtoupper($order[1]));
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
                'headImg' => 'http://' . $request->getHost() . '/uploads/' . $value->getHeadImg(),
                'thumb' => 'http://' . $request->getHost() . '/uploads/thumb/' . $value->getHeadImg(),
                //'thumb' => $cacheManager->getBrowserPath('uploads/'.$value->getHeadImg(), 'thumb1'),
                'grayHeadImg' => 'http://' . $request->getHost() . '/uploads/gray/' . $value->getHeadImg(),
                'sum' => $count,
                'top' => $this->getRank($value->getLikeNum()),
            );
        }
        $result = array(
            'ret' => 0,
            'data' => $data,
        );
        $callback = $request->get('callback') ?: 'callback';
        //return new Response($callback.'('.json_encode($result).')');
        $response = new Response();
        if (null == $request->get('callback'))
            $response->setContent(json_encode($result));
        else
            $response->setContent($callback . '(' . json_encode($result) . ')');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * @Route("/info/{id}", name="api_info")
     */
    public function infoAction(Request $request, $id = null)
    {
        if (null == $id || in_array($id, $this->blacklist)) {
            $result = array(
                'ret' => 1001,
                'msg' => '没有您要的数据',
            );
        } else {
            $info = $this->getDoctrine()->getRepository('AppBundle:Info')->find($id);
            if ($info == null || $info->getIsActive() == false || $info->getType() != 0) {

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
                'headImg' => 'http://' . $request->getHost() . '/uploads/' . $info->getHeadImg(),
                'thumb' => 'http://' . $request->getHost() . '/uploads/thumb/' . $info->getHeadImg(),
                //'thumb' => $cacheManager->getBrowserPath('uploads/'.$info->getHeadImg(), 'thumb1'),
                'grayHeadImg' => 'http://' . $request->getHost() . '/uploads/gray/' . $info->getHeadImg(),
                'top' => $this->getRank($info->getLikeNum()),
            );
            $result = array(
                'ret' => 0,
                'data' => $data,
            );
        }

        $callback = $request->get('callback') ?: 'callback';
        //return new Response($callback.'('.json_encode($result).')');
        $response = new Response();
        if (null == $request->get('callback'))
            $response->setContent(json_encode($result));
        else
            $response->setContent($callback . '(' . json_encode($result) . ')');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * @Route("/like/{id}", name="api_like")
     */
    public function likeAction(Request $request, $id = null)
    {
        if (in_array($id, $this->blacklist)) {
            return new Response('', 500);
        }
        $em = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();
        try {
            $info = $em->getRepository('AppBundle:Info')->find($id);
            if ($info == null || $info->getType() != 0) {
                $result = array(
                    'ret' => 1001,
                    'msg' => '该信息不存在',
                );
            } else {
                $create_ip = $request->getClientIp();
                $timestamp = time();
                $create_time1 = date('Y-m-d 00:00:00', $timestamp);
                $create_time2 = date('Y-m-d 00:00:00', strtotime('+1 day', $timestamp));

                $repo = $em->getRepository('AppBundle:LikeLog');
                $qb = $repo->createQueryBuilder('a');
                $qb->select('COUNT(a)');
                $qb->where('a.createIp = :createIp AND a.createTime >= :createTime1 and a.createTime < :createTime2');
                $qb->setParameter('createIp', $create_ip);
                $qb->setParameter(':createTime1', new \DateTime($create_time1), \Doctrine\DBAL\Types\Type::DATETIME);
                $qb->setParameter(':createTime2', new \DateTime($create_time2), \Doctrine\DBAL\Types\Type::DATETIME);
                $count = $qb->getQuery()->getSingleScalarResult();

                if ($count < 5) {
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
                } else {
                    $result = array(
                        'ret' => 1200,
                        'msg' => '投票已超出限制~',
                    );
                }

            }
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollback();
            $result = array(
                'ret' => 2001,
                'msg' => $e->getMessage(),
            );
        }

        $callback = $request->get('callback') ?: 'callback';
        //return new Response($callback.'('.json_encode($result).')');
        $response = new Response();
        if (null == $request->get('callback'))
            $response->setContent(json_encode($result));
        else
            $response->setContent($callback . '(' . json_encode($result) . ')');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * @Route("/lottery", name="api_lottery")
     */
    public function lotteryAction(Request $request)
    {
        $session = $request->getSession();
        $timestamp = time();
        /*
        if ($this->get('kernel')->getEnvironment() == 'dev') {
            $id = rand(1, 70000);
            $session->set('id', $id);
        }
        */
        if (null == $session->get('id')) {
            $result = array(
                'ret' => 3001,
                'msg' => '您没有抽奖资格',
            );
        } elseif ($timestamp > strtotime('2016-12-31 23:59:59') || $timestamp < strtotime('2016-03-15 12:00')) {
            $result = array(
                'ret' => 4001,
                'msg' => '活动未开始或者已结束',
            );
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            try {
                $info = $em->getRepository('AppBundle:Info')->find($session->get('id'));
                if ($info == null || $info->getType() != 0) {
                    $result = array(
                        'ret' => 1001,
                        'msg' => '该信息不存在',
                    );
                } elseif ($info->getHasLottery() == true) {
                    $result = array(
                        'ret' => 1100,
                        'msg' => '该信息已抽奖~',
                    );
                } else {
                    $mobile = $info->getMobile();
                    #当天已抽奖
                    $repo = $em->getRepository('AppBundle:Info');
                    $qb = $repo->createQueryBuilder('a');
                    $qb->select('COUNT(a)');
                    $qb->where('a.mobile = :mobile AND a.lotteryTime >= :createTime1 AND a.lotteryTime < :createTime2 AND a.hasLottery = :hasLottery');
                    $qb->setParameter('mobile', $mobile);
                    $qb->setParameter('hasLottery', true);
                    $date1 = date('Y-m-d', $timestamp);
                    $date2 = date('Y-m-d', strtotime('+1 day', $timestamp));
                    $qb->setParameter(':createTime1', new \DateTime($date1), \Doctrine\DBAL\Types\Type::DATETIME);
                    $qb->setParameter(':createTime2', new \DateTime($date2), \Doctrine\DBAL\Types\Type::DATETIME);
                    $count1 = $qb->getQuery()->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE)->getSingleScalarResult();

                    #该手机号已中奖
                    $repo = $em->getRepository('AppBundle:Info');
                    $qb = $repo->createQueryBuilder('a');
                    $qb->select('COUNT(a)');
                    $qb->where('a.mobile = :mobile AND a.prize > 0');
                    $qb->setParameter('mobile', $mobile);
                    $count2 = $qb->getQuery()->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE)->getSingleScalarResult();

                    #今天已抽奖,已中奖
                    if ($count1 > 0 || $count2 > 0) {
                        $prize = 0;
                    } else {
                        $lottery = new Helper\Lottery($em, $timestamp);
                        $prize = $lottery->execute();
                    }
                    $info->setHasLottery(true);
                    $info->setLotteryTime(new \DateTime(date('Y-m-d H:i:s', $timestamp)));
                    $info->setPrize($prize);
                    $em->persist($info);
                    $em->flush();
                    $result = array(
                        'ret' => 0,
                        'msg' => '',
                        'data' => array('prize' => $prize, 'id' => $info->getId()),
                    );
                    #短信发送
                    if ($prize > 0 && $this->get('kernel')->getEnvironment() != 'dev') {
                        Helper\SMS::send($em, array(
                            'mobile' => $mobile,
                            'info' => $info,
                            'prize' => $prize,
                            'type' => $info->getType(),
                        ));
                    }
                }
                $em->getConnection()->commit();
            } catch (Exception $e) {
                $em->getConnection()->rollback();
                $result = array(
                    'ret' => 2001,
                    'msg' => $e->getMessage(),
                );
            }
        }
        $session->set('id', null);
        $callback = $request->get('callback') ?: 'callback';
        //return new Response($callback.'('.json_encode($result).')');
        $response = new Response();
        if (null == $request->get('callback'))
            $response->setContent(json_encode($result));
        else
            $response->setContent($callback . '(' . json_encode($result) . ')');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * @Route("/count", name="api_count")
     */
    public function countAction(Request $request, $id = null)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Info');
        $qb = $repo->createQueryBuilder('a');
        //$qb->where('a.type = 0');
        $qb->select('COUNT(a)');
        $count = $qb->getQuery()->getSingleScalarResult();
        $result = array(
            'ret' => 0,
            'data' => array('count' => $count),
        );
        $callback = $request->get('callback') ?: 'callback';
        //return new Response($callback.'('.json_encode($result).')');
        $response = new Response();
        if (null == $request->get('callback'))
            $response->setContent(json_encode($result));
        else
            $response->setContent($callback . '(' . json_encode($result) . ')');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * @Route("/sign", name="api_wechat_sign")
     */
    public function getWechatSign(Request $request)
    {
        if (null == $request->get('url')) {
            $result = json_encode(array('ret' => 1001, 'msg' => 'url不能为空~'));
        } else {
            $url = 'http://campaign.slek.com.cn/wxtoken/ticket.php?url=' . urlencode($request->get('url'));
            $result = file_get_contents($url);
        }
        $response = new Response();
        if (null == $request->get('callback'))
            $response->setContent($result);
        else
            $response->setContent($callback . '(' . $result . ')');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    protected function getRank($likeNum)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Info');
        $query = $repository
            ->createQueryBuilder('a')
            ->select('count(a)')
            ->where('a.likeNum > :likeNum AND a.type = 0')
            ->setParameter('likeNum', $likeNum)
            ->orderBy('a.likeNum', 'ASC')
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
        $result = json_decode(Helper\HttpClient::get($url . '?' . http_build_query($data)), true);
        if ($result['status'] == 0)
            return $result['content']['address_detail']['city'];
        else
            return '--';
    }

    /**
     * @Route("/march/lottery", name="march_lottery")
     */
    public function marchLotteryAction(Request $request)
    {
        $session = $request->getSession();
        $code_txt = $request->get('code');
        if (null == $code_txt) {
            $result = array('ret' => 1001, 'msg' => '请输入幸运心愿码~');
        } else {
            $timestamp = time();
            $em = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();
            try {
                $repo = $em->getRepository('AppBundle:Code');
                $qb = $repo->createQueryBuilder('a');
                $qb->where('a.code = :code');
                $qb->setParameter('code', $code_txt);
                $qb->select('COUNT(a)');
                $count = $qb->getQuery()->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE)->getSingleScalarResult();

                if ($count == 0) {
                    $result = array('ret' => 1002, 'msg' => '亲，你输入的幸运心愿码有误哦，请仔细检查重新输入~');
                } else {
                    $repo = $em->getRepository('AppBundle:Code');
                    $qb = $repo->createQueryBuilder('a');
                    $qb->where('a.code = :code');
                    $qb->setParameter('code', $code_txt);
                    $raw = $qb->getQuery()->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE)->getResult();
                    $code = $raw[0];
                    //$code = $em->getRepository('AppBundle:Code')->findOneBy(array('code'=>$request->get('code')));
                    if ($code->getIsActive() == 1) {
                        $result = array('ret' => 1003, 'msg' => '亲，您输入的幸运心愿码已经被使用过啦~');
                    } else {
                        $lottery = new Helper\Lottery($em, $timestamp, $code_txt);
                        $prize = $lottery->execute();
                        $code->setIsActive(1);
                        $em->persist($code);

                        $log = new Entity\LotteryLog();
                        $log->setCreateIp($request->getClientIp());
                        $log->setCreateTime(new \DateTime(date('Y-m-d H:i:s', $timestamp)));
                        $log->setInfo(null);
                        $log->setPrize($prize);
                        $log->setCode($code);
                        $log->setCodeTxt($code_txt);
                        $em->persist($log);

                        $em->flush();
                        $session->set('march_lottery_log', $log->getId());
                        //$session->set('prize', $prize);
                        $result = array('ret' => 0, 'msg' => '', 'session_id' => $session->getId(), 'prize' => $prize);
                    }
                }
                $em->getConnection()->commit();
            } catch (Exception $e) {
                $em->getConnection()->rollback();
                $result = array(
                    'ret' => 2001,
                    'msg' => $e->getMessage(),
                );
            }
        }
        $callback = $request->get('callback') ?: 'callback';
        $response = new Response();
        if (null == $request->get('callback'))
            $response->setContent(json_encode($result));
        else
            $response->setContent($callback . '(' . json_encode($result) . ')');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Origin', 'http://campaign.slek.com.cn');
        return $response;
        //return new JsonResponse($result);
    }

    /**
     * @Route("/march/post", name="march_post")
     */
    public function postAction(Request $request)
    {
        $session = $request->getSession();
        if (null == $session->get('march_lottery_log')) {
            $result = array('ret' => 1001, 'msg' => '您还没有中奖喔~');
        } else {
            $em = $this->getDoctrine()->getManager();
            $log = $em->getRepository("AppBundle:LotteryLog")->find($session->get('march_lottery_log'));
            if (null != $log) {
                $info = new Entity\Info();
                $info->setAddress($request->get('address'));
                $info->setUsername($request->get('username'));
                $info->setMobile($request->get('mobile'));
                $info->setHeadImg('');
                $info->setWishText('');
                $info->setCreateIp($request->getClientIp());
                $info->setCreateTime(new \DateTime('now'));
                $info->setLotteryTime($log->getCreateTime());
                $info->setIsActive(1);
                $info->setCode($log->getCodeTxt());
                $info->setType(1);
                $info->setPrize($log->getPrize());
                $info->setHasLottery(1);

                $log->setInfo($info);
                $em->persist($info);
                $em->persist($log);
                $em->flush();
            }
            $session->set('march_lottery_log', null);
            $result = array('ret' => 0, 'msg' => '');
        }
        $callback = $request->get('callback') ?: 'callback';
        $response = new Response();
        if (null == $request->get('callback'))
            $response->setContent(json_encode($result));
        else
            $response->setContent($callback . '(' . json_encode($result) . ')');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Origin', 'http://campaign.slek.com.cn');
        return $response;
        //return new JsonResponse($result);
    }

    /**
     * @Route("/sms", name="api_sms")
     */
    public function smsAction(Request $request)
    {
        //$input = mb_convert_encoding(urldecode($request->getContent()),'UTF-8','GBK');
        //$input = mb_convert_encoding(urldecode('username=miketest&reply=15618892632%2C%B2%E2%CA%D4%B5%D8%D6%B7%2C2016-03-28+00%3A55%3A30%3B '),'UTF-8','GBK');
        //$array = array();
        //parse_str($input, $array);
        //$reply = explode(',', $array['reply']);
        $mobile = $request->get('mobile');
        $message = urldecode($request->get('message'));
        if ($handle = @fopen('sms_01.log', 'a+')) {
            fwrite($handle, $mobile . ',' . $message . ',' . date('Y-m-d H:i:s') . "\n");
            fclose($handle);
        }

        $em = $this->getDoctrine()->getManager();
        $sms = $em->getRepository('AppBundle:SMS')->findOneBy(array('mobile' => $mobile, 'type' => 0));
        if (null != $sms) {
            $sms->setAddress($message);
            $em->persist($sms);
            $em->flush();
        }
        return new Response();
    }

    /**
     * @Route("/sms/send", name="api_sms_send")
     */
    /*
        public function smsSendAction(Request $request)
        {
                $em = $this->getDoctrine()->getManager();
        $result = array();
                $params = array(
            array('mobile'=>'15618892632','prize'=>0,'type'=>0,'info'=>null),
            //array('mobile'=>'18521595129','prize'=>0,'type'=>0,'info'=>null),
            array('mobile'=>'13812704388','prize'=>0,'type'=>0,'info'=>null),
            array('mobile'=>'18016458059','prize'=>0,'type'=>0,'info'=>null)
        );
        foreach ($params as $v){
            var_dump($v);
            $result[] = Helper\SMS::send($em,$v);
        }
        return new JsonResponse($result);
        }
    */
}
