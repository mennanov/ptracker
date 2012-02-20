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
                // с данными всё ок, добавляем пользователя
                $data = $form->getData();
                $user->setUsername($data->username);
                $user->setName($data->name);
                $user->setEmail($data->email);
                // получаем хэшированный пароль
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);
                $password = $encoder->encodePassword($data->password, $user->getSalt());
                $user->setPassword($password);
                // по умолчанию пользователь неактивен
                $user->setIsActive(false);
                // сохраняем
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();
                // если пользователь сохранился без проблем - отправляем письмо для подтверждения
                if ($user->getId()) {
                    $message = \Swift_Message::newInstance()
                            ->setSubject('Account confirmation')
                            ->setFrom('mennanov@gmail.com')
                            ->setTo($data->email)
                            ->setContentType('text/html')
                            ->setBody($this->renderView('PtrackerAuthBundle:Default:register_email.html.twig', array('salt' => $user->getSalt(), 'username' => $data->username)));
                    $this->get('mailer')->send($message);
                    $this->redirect($this->generateUrl('register', array('success' => true)));
                }
            }
        }
        $form = $form->createView();
        return compact('form');
    }

}
