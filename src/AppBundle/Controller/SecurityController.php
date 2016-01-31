<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
	public function loginAction()
	{
		/*
		$factory = $this->get('security.encoder_factory');
		$user = new \AppBundle\Entity\User();
		   
		$encoder = $factory->getEncoder($user);
		$password = $encoder->encodePassword('admin123', $user->getSalt());
		$user->setPassword($password);
		var_dump($password,$user->getSalt());
		*/
		// get the login error if there is one
		if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $this->get('request')->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
		} else {
			$error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
		}
		
		return $this->render('AppBundle:admin:login.html.twig', array(
			// last username entered by the user
			'last_username' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
			'error'         => $error,
			));
	}
	public function forgetpwdAction()
	{

	}
} 