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
use Symfony\Component\Filesystem\Filesystem;
#use Symfony\Component\Validator\Constraints\Image;

class DefaultController extends Controller
{
  public function getUser()
  {
		$session = $this->get('session');
		if(null != $session->get('user_id')){
			$user = $this->getDoctrine()->getRepository('AppBundle:WechatUser')->find($session->get('user_id'));
		}
		else{
			$user = $this->getDoctrine()->getRepository('AppBundle:WechatUser')->findOneByOpenId($session->get('open_id'));
		}
		return $user;
  }
	/**
	 * @Route("/", name="_index")
	 */
	public function indexAction(Request $request)
	{
		return $this->render('AppBundle:default:index.html.twig');
	}
	/**
	 * @Route("/finish/{id}", name="_finish")
	 */
	public function finishAction(Request $request, $id = null)
	{
		$session = $request->getSession();
		if( null == $id){
			return $this->redirect($this->generateUrl('_index'));
		}
		$photo = $this->getDoctrine()->getRepository('AppBundle:Photo')->find($id);
		if( null == $photo){
			return $this->redirect($this->generateUrl('_index'));
		}
		$session->set('wx_share_url', 'http://'.$request->getHost().$this->generateUrl('_photo', array('id'=>$photo->getId())));
		return $this->render('AppBundle:default:finish.html.twig',array('photo'=>$photo));
	}
	/**
	 * @Route("/photo/{id}", name="_photo")
	 */
	public function photoAction(Request $request, $id = null)
	{
		if( null == $id){
			return $this->redirect($this->generateUrl('_index'));
		}
		$photo = $this->getDoctrine()->getRepository('AppBundle:Photo')->find($id);
		if( null == $photo){
			return $this->redirect($this->generateUrl('_index'));
		}
		
    if( $photo->getUser() == $this->getUser()){
    	$repository = $this->getDoctrine()->getRepository('AppBundle:WechatUser');
	    $query = $repository
	    	->createQueryBuilder('a')
	    	->setMaxResults(3)
	    	->where('a.favourNum != 0')
	    	->orderBy('a.favourNum','DESC')
	    	->getQuery();
	    $users = $query->getResult();

	    $repository = $this->getDoctrine()->getRepository('AppBundle:Photo');
	    $query = $repository
	    	->createQueryBuilder('a')
	    	->select('count(a)')
	    	->where('a.user = :user AND a.id <= :id')
	    	->setParameter('user', $this->getUser())
	    	->setParameter('id', $id)
	    	->getQuery();
	    $rank = $query->getSingleScalarResult();

	    $repository = $this->getDoctrine()->getRepository('AppBundle:WechatUser');
	    $query = $repository
	    	->createQueryBuilder('a')
	    	->select('count(a)')
	    	->where('a.favourNum < :favourNum')
	    	->setParameter('favourNum', $photo->getFavourNum())
	    	->orderBy('a.favourNum','DESC')
	    	->getQuery();
	    $favour_rank = $query->getSingleScalarResult() + 1;

			return $this->render('AppBundle:default:photo.html.twig', array('photo'=>$photo,'users'=>$users,'rank'=>$rank,'favourRank'=>$favour_rank));
    }
    else{
    	return $this->render('AppBundle:default:share.html.twig', array('photo'=>$photo));
    }
	}
	/**
	 * @Route("/upload/{t}", name="_upload")
	 */
	public function uploadAction(Request $request, $t = 'b')
	{
		if($t == 'a'){
			$title = '';
			$type = 0;
			$num = (int)$request->get('num');
			$file_name = 'photoA'.$num.'.png';

		}
		else{
			$uploadedFile = $request->files->get('image');
			if( $uploadedFile == null ||
				!in_array( strtolower($uploadedFile->getClientOriginalExtension()), array('png','jpg','jpeg','gif'))
			)
			{
				return new Response('只能上传照片喔~');
			}
			elseif( !$uploadedFile->isValid()){
				return new Response($uploadedFile->getErrorMessage());
			}
			else{
				$file_path = preg_replace('/app$/si', 'web/uploads', $this->get('kernel')->getRootDir());
				//$file_path = $request->getBasePath().$this->path;
				$file_name = date('YmdHis').rand(1000,9999).'.'.$uploadedFile->getClientOriginalExtension();
				$fs = new Filesystem();

				if( !$fs->exists( $file_path ) )
				{
					try {
						$fs->mkdir( $file_path );
					} catch (IOException $e) {
						return new Response('服务器发生错误，无法创建文件夹：'.$e->getPath());
						//$result['msg'] = '服务器发生错误，无法创建文件夹：'.$e->getPath();
					}
				}

				$uploadedFile->move($file_path, $file_name);
				$scale = $request->get('scale');
				$pos_x = (int)$request->get('pos_x');
				$pos_y = (int)$request->get('pos_y');
				$num = $request->get('num');
				$title = trim($request->get('title'));

				$img_url = $file_path.'/'.$file_name;
				$imagine = new Imagine();
				$exif = @exif_read_data($img_url, 0, true);
		    if (isset($exif['IFD0']['Orientation'])) {
		        $photo = $imagine->open($img_url);
		        if ($exif['IFD0']['Orientation'] == 6) {
		          $photo->rotate(90)->save($img_url);
		        } elseif ($exif['IFD0']['Orientation'] == 3) {
		          $photo->rotate(180)->save($img_url);
		        }
		    }

		    $photo = $imagine->open($img_url);
		    $size = $photo->getSize();
				$width = 640;
				$height = 1039;

		    $w1 = $width*$scale;
		    $h1 = $w1*$size->getHeight()/$size->getWidth();
		    $imagine->open($img_url)
					->resize(new Box($w1, $h1))
					->save($img_url);
				$imagine->open($img_url)
					->resize(new Box($w1, $h1))
					->save($img_url);

				#左移裁切
		    if($pos_x < 0 ){
					$size = $imagine->open($img_url)->getSize();
		    	$imagine->open($img_url)
						->crop(new Point(abs($pos_x),0),new Box($size->getWidth()-abs($pos_x), $size->getHeight()))
						->save($img_url);
		    }

		    #上移裁切
		    if($pos_y < 0 ){
					$size = $imagine->open($img_url)->getSize();
		    	$imagine->open($img_url)
						->crop(new Point(0,abs($pos_y)),new Box($size->getWidth(), $size->getHeight()-abs($pos_y)))
						->save($img_url);
		    }

		    #右移拼贴
		    if($pos_x > 0){
					$photo = $imagine->open($img_url);
			    $size = $photo->getSize();
		    	$collage_width = $pos_x + $size->getWidth() > $width ? $pos_x + $size->getWidth() : $width;
		    	$collage_height = $size->getHeight();
		    	$collage = $imagine->create(new Box($collage_width, $collage_height));
		    	$collage->paste($photo, new Point($pos_x, 0))
		        ->save($img_url);
		    }

		    #下移拼贴
		    if($pos_y > 0){
					$photo = $imagine->open($img_url);
			    $size = $photo->getSize();
		    	$collage_width = $size->getWidth();
		    	$collage_height = $pos_y + $size->getHeight() > $width ? $pos_y + $size->getHeight() : $height;
		    	$collage = $imagine->create(new Box($collage_width, $collage_height));
		    	$collage->paste($photo, new Point(0, $pos_y))
		        ->save($img_url);
		    }

		    #超出剪切
		    $photo = $imagine->open($img_url);
			  $size = $photo->getSize();
			  $_width = $size->getWidth();
		    if($size->getWidth() > $width){
		    	$_width = $width;
		    	$imagine->open($img_url)
						->crop(new Point(0,0),new Box($_width, $size->getHeight()))
						->save($img_url);
		    }
		    if($size->getHeight() > $height){
		    	$imagine->open($img_url)
		    		->crop(new Point(0,0),new Box($_width, $height))
						->save($img_url);
		    }

		    $photo = $imagine->open($img_url);
		    $collage = $imagine->create(new Box($width, $height));
		    $collage->paste($photo, new Point(0, 0))
		      ->save($img_url);

		    $collage = $imagine->open('bundles/app/default/images/photoB'.$num.'.png');
				$photo = $imagine->open($img_url);
		    $photo->paste($collage, new Point(0, 0))
		    	->save($img_url);
		   	$type = 1;

		    
		    /*
		    $photo = $imagine->open($img_url);
		    $color = new \Imagine\Image\Color;
		    $font = new \Imagine\Gd\Font('',30, $color);
		    $photo->draw()->text('tesintg',$font,new Point(0,0),0);
		    */

			}
		}
			

	  $em = $this->getDoctrine()->getManager();
		$em->getConnection()->beginTransaction();
		try{
	    $photo = new Entity\Photo();
	    $user = $this->getUser();
	    $photo->setImgUrl($file_name);
	    $photo->setUser($user);
	    $photo->setTitle($title);
	    $photo->setType($type);
	    $photo->setFavourNum(0);
			$photo->setCreateIp($request->getClientIp());
			$photo->setCreateTime(new \DateTime('now'));
			$em->persist($photo);
			$em->flush();
			$em->getConnection()->commit();
			return $this->redirect($this->generateUrl('_finish',array('id'=>$photo->getId())));
		}
		catch (Exception $e) {
			$em->getConnection()->rollback();
			return new Response($e->getMessage());
			//$json['ret'] = 1001;
			//$json['msg']= $e->getMessage();
		}
	}

  /**
   * @Route("/callback", name="_callback")
   */
  public function wechatAction(Request $request)
  {
    $session = $request->getSession();
    $code = $request->query->get('code');
    //$state = $request->query->get('state');
    $app_id = $this->container->getParameter('wechat_appid');
    $secret = $this->container->getParameter('wechat_secret');
    $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $app_id . "&secret=" . $secret . "&code=$code&grant_type=authorization_code";
    $data = Helper\HttpClient::get($url);
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
}
