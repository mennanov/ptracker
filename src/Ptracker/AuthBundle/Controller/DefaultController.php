<?php

namespace Ptracker\AuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
// models
use Ptracker\AuthBundle\Entity\User;
// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class DefaultController extends Controller {

    public function indexAction($name) {
        return $this->render('PtrackerAuthBundle:Default:index.html.twig', array('name' => $name));
    }

    /**
     * 
     * @Template()
     */
    public function loginAction() {
        $request = $this->getRequest();
        $session = $request->getSession();

        // получаем ошибки авторизации, если они есть
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array('error' => $error, 'last_username' => $session->get(SecurityContext::LAST_USERNAME));
    }

    /**
     * 
     * @Template()
     */
    public function registerAction(Request $request) {
        $repo = $this->getDoctrine()->getRepository('PtrackerAuthBundle:User');
        $user = new User();
        $form = $this->createFormBuilder($user, array(
                    'validation_groups' => array('registration')
                ))
                ->add('username', 'text')
                ->add('name', 'text')
                ->add('email', 'email')
                ->add('password', 'password')
                ->getForm();
        // если форма отправлена
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                // 

                
            }
        }
        $form = $form->createView();
        return compact('form');
    }

}
