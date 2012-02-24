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
use Ptracker\TasksBundle\Entity\Comment;

class DefaultController extends Controller {

    public $statuses = array('new', 'started', 'finished', 'accepted', 'rejected');
    public $started_status = 1;
    
    /**
     * @Template
     */
    public function indexAction(Request $request) {
        $repo = $this->getDoctrine()->getRepository('PtrackerTasksBundle:Task');
        $user_repo = $this->getDoctrine()->getRepository('PtrackerAuthBundle:User');
        $conditions = array();
        if($request->query->get('responsible')) {
            $conditions['responsible_user_id'] = $request->query->get('responsible');
        }
        if(strlen($request->query->get('status')) > 0) {
            $conditions['status'] = $request->query->get('status');
        }
        $tasks = $repo->findBy($conditions);
        if(!empty($tasks)) {
            foreach($tasks as &$task) {
                $task->owner = $user_repo->find($task->getUserId());
                $task->responsible = $user_repo->find($task->getResponsibleUserId());
            }
        }
        $users = $this->getDoctrine()->getRepository('PtrackerAuthBundle:User')->findByIsActive(true);
        $statuses = $this->statuses;
        return compact('tasks', 'users', 'statuses');
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
            $task->setStatus(0);
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
            $user = $this->get('security.context')->getToken()->getUser();
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
            if ($this->getRequest()->getMethod() == 'POST') {
                $new_comment = new Comment();
                $new_comment->setText($this->getRequest()->request->get('comment'));
                $new_comment->setTaskId($task->getId());
                $new_comment->setUser($user);
                $new_comment->setCreatedAt(new \DateTime);
                $validator = $this->get('validator');
                $errors = $validator->validate($new_comment);
                if (count($errors) == 0) {
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($new_comment);
                    $em->flush(); 
                    return $this->redirect($this->generateUrl('tasks_view', array('id' => $task->getId())));
                }
            }
            $comments = $this->getDoctrine()->getRepository('PtrackerTasksBundle:Comment')->findBy(array(
                'task_id' => $task->getId()
            ), array('id' => 'DESC'));
            if(!empty($comments)) {
                foreach($comments as &$comment) {
                    $comment->author = $this->getDoctrine()->getRepository('PtrackerAuthBundle:User')->find($comment->getUserId());
                }
            }
            return compact('task', 'owner', 'responsible', 'users', 'statuses', 'nextstatus', 'comments');
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
    
    /**
     * @Template
     */
    public function editAction($id) {
        $request = $this->getRequest();
        $user_repository = $this->getDoctrine()->getRepository('PtrackerAuthBundle:User');
        $user = $this->get('security.context')->getToken()->getUser();
        $errors = array();
        // users for <select>
        $users = $user_repository->findByIsActive(true);
        $task = $this->getDoctrine()->getRepository('PtrackerTasksBundle:Task')->find($id);
        if(!$task) {
            return $this->redirect($this->generateUrl('tasks'));
        }
        $responsible = $task->getResponsibleUser();
        if ($request->getMethod() == 'POST' && !empty($task)) {
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
        return compact('users', 'errors', 'task', 'responsible');
    }

}
