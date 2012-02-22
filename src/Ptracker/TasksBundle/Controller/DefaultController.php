<?php

namespace Ptracker\TasksBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
// @Template
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
// models
use Ptracker\AuthBundle\Entity\User;
use Ptracker\TasksBundle\Entity\Task;

class DefaultController extends Controller {

    public $statuses = array('new', 'started', 'finished', 'accepted', 'rejected');
    public $started_status = 1;
    
    /**
     * @Template
     */
    public function indexAction() {

        return array();
    }

    /**
     * @Template
     */
    public function addAction(Request $request) {
        $user_repository = $this->getDoctrine()->getRepository('PtrackerAuthBundle:User');
        $user = $this->get('security.context')->getToken()->getUser();
        $errors = array();
        // users for <select>
        $users = $user_repository->findByIsActive(true);
        if ($request->getMethod() == 'POST' && !empty($user)) {
            $task = new Task();
            $task->setTitle($request->request->get('title'));
            $task->setDescription(htmlspecialchars($request->request->get('description'), ENT_QUOTES, 'UTF-8'));
            $task->setPoints($request->request->get('points'));
            $task->setUser($user);
            $task->setCreatedAt(new \DateTime);

            // check if responsible user exists
            $responsible_user = $user_repository->find($request->request->get('responsible'));
            if ($responsible_user) {
                $task->setResponsibleUser($responsible_user);
                $validator = $this->get('validator');
                $errors = $validator->validate($task);
                if (count($errors) > 0) {
                    return compact('errors', 'users');
                } else {
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($task);
                    $em->flush();
                    return $this->redirect($this->generateUrl('tasks_view', array('id' => $task->getId())));
                }
            }
        }
        return compact('users', 'errors');
    }

    /**
     * @Template
     */
    public function viewAction($id = 0) {
        $task = $this->getDoctrine()
                ->getRepository('PtrackerTasksBundle:Task')
                ->find($id);
        if ($task) {
            // get owner user
            $owner = $task->getUser();
            // get responsible user
            $responsible = $task->getResponsibleUser();
            global $router;
            $router = $this->get('router');
            $users = $this->getDoctrine()
                    ->getRepository('PtrackerAuthBundle:User')
                    ->findByIsActive(true);
            $statuses = $this->statuses;
            // calculate next status
            $nextstatus = $task->getStatus() < count($statuses) - 1 ? $task->getStatus() + 1 : $this->started_status;
            return compact('task', 'owner', 'responsible', 'users', 'statuses', 'nextstatus');
        } else {
            return $this->redirect($this->generateUrl('tasks'));
        }
    }

    public function change_responsibleAction($id, $responsible) {
        $em = $this->getDoctrine()->getEntityManager();
        $result = 'failed';
        $task = $em->getRepository('PtrackerTasksBundle:Task')->find($id);
        if ($task) {
            // check responsible user
            $user = $em->getRepository('PtrackerAuthBundle:User')->find($responsible);
            if ($user) {
                $task->setResponsibleUser($user);
                $em->flush();
                $result = 'success';
            }
        }
        return new Response(json_encode($result));
    }

    public function change_statusAction($id, $status) {
        $em = $this->getDoctrine()->getEntityManager();
        $result = 'failed';
        $task = $em->getRepository('PtrackerTasksBundle:Task')->find($id);
        if ($task) {
            // check next status
            if (!empty($this->statuses[$status]) && $status - $task->getStatus() == 1 || in_array($status, array($this->started_status, 3, 4))) {
                $task->setStatus($status);
                $em->flush();
                $result = 'success';
            }
        }
        return new Response(json_encode($result));
    }

}
