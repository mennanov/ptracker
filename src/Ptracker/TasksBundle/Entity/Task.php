<?php

namespace Ptracker\TasksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Ptracker\TasksBundle\Entity\Task
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ptracker\TasksBundle\Entity\TaskRepository")
 */
class Task {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var integer $user_id
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $user_id;

    /**
     * @var integer $responsible_user_id
     *
     * @ORM\Column(name="responsible_user_id", type="integer")
     */
    private $responsible_user_id;

    /**
     * @var smallint $points
     *
     * @ORM\Column(name="points", type="smallint")
     * @Assert\NotBlank()
     */
    private $points;

    /**
     * @var datetime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @var datetime $updated_at
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="\Ptracker\AuthBundle\Entity\User", inversedBy="tasks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="\Ptracker\AuthBundle\Entity\User", inversedBy="tasks")
     * @ORM\JoinColumn(name="responsible_user_id", referencedColumnName="id")
     */
    protected $responsible_user;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set user_id
     *
     * @param integer $userId
     */
    public function setUserId($userId) {
        $this->user_id = $userId;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId() {
        return $this->user_id;
    }

    /**
     * Set responsible_user_id
     *
     * @param integer $responsibleUserId
     */
    public function setResponsibleUserId($responsibleUserId) {
        $this->responsible_user_id = $responsibleUserId;
    }

    /**
     * Get responsible_user_id
     *
     * @return integer 
     */
    public function getResponsibleUserId() {
        return $this->responsible_user_id;
    }

    /**
     * Set points
     *
     * @param smallint $points
     */
    public function setPoints($points) {
        $this->points = $points;
    }

    /**
     * Get points
     *
     * @return smallint 
     */
    public function getPoints() {
        return $this->points;
    }

    /**
     * Set created_at
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt) {
        $this->created_at = $createdAt;
    }

    /**
     * Get created_at
     *
     * @return datetime 
     */
    public function getCreatedAt() {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt) {
        $this->updated_at = $updatedAt;
    }

    /**
     * Get updated_at
     *
     * @return datetime 
     */
    public function getUpdatedAt() {
        return $this->updated_at;
    }

    /**
     * Set user
     *
     * @param Ptracker\TasksBundle\Entity\PtrackerAuthBundle:User $user
     */
    public function setUser(\Ptracker\AuthBundle\Entity\User $user) {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return Ptracker\TasksBundle\Entity\PtrackerAuthBundle:User 
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set responsible_user
     *
     * @param Ptracker\TasksBundle\Entity\PtrackerAuthBundle:User $responsibleUser
     */
    public function setResponsibleUser(\Ptracker\AuthBundle\Entity\User $responsibleUser) {
        $this->responsible_user = $responsibleUser;
    }

    /**
     * Get responsible_user
     *
     * @return Ptracker\TasksBundle\Entity\PtrackerAuthBundle:User 
     */
    public function getResponsibleUser() {
        return $this->responsible_user;
    }

}