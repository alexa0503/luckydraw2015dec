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
}
