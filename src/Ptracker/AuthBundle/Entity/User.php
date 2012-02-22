<?php

namespace Ptracker\AuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Ptracker\AuthBundle\Entity\User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ptracker\AuthBundle\Entity\UserRepository")
 * @UniqueEntity( fields = {"username", "email"} )
 */
class User implements AdvancedUserInterface {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\OneToMany(targetEntity="Ptracker\TasksBundle\Entity\Task", mappedBy="user")
     */
    public $tasks;
    
    /**
     * @ORM\OneToMany(targetEntity="Ptracker\TasksBundle\Entity\Comment", mappedBy="user")
     */
    public $comments;

    /**
     * @ORM\Column(name="username", type="string", length=25, unique=true)
     * @Assert\Regex(
     *     pattern="/^\w+$/",
     *     match=true,
     *     message="Username must be a valid \w+ regexp username :)",
     *     groups={"registration"}     
     * )
     * @Assert\NotBlank(groups={"registration"})
     */
    public $username;

    /**
     * @ORM\Column(name="salt", type="string", length=40)
     */
    public $salt;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank(groups={"registration"})
     */
    public $name;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @Assert\NotBlank(groups={"registration", "authentication"})
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true,
     *     groups={"registration", "authentication"}
     * )
     */
    public $email;

    /**
     * @var string $password
     *
     * @ORM\Column(name="password", type="string", length=40)
     * @Assert\NotBlank(groups={"registration", "authentication"})
     */
    public $password;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    public $isActive;

    public function __construct() {
        $this->isActive = true;
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->tasks = new ArrayCollection();
    }

    public function getRoles() {
        return array('ROLE_USER');
    }

    public function equals(UserInterface $user) {
        return $user->getUsername() === $this->username;
    }

    public function eraseCredentials() {
        
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function getIsActive() {
        return $this->isActive;
    }

    public function setIsActive($isActive) {
        $this->isActive = (boolean) $isActive;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword() {
        return $this->password;
    }

    public function isAccountNonExpired() {
        return true;
    }

    public function isAccountNonLocked() {
        return true;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

    public function isEnabled() {
        return $this->isActive;
    }

    /**
     * Set salt
     *
     * @param string $salt
     */
    public function setSalt($salt) {
        $this->salt = $salt;
    }

    /**
     * Add tasks
     *
     * @param Ptracker\AuthBundle\Entity\PtrackerTasksBundle:Task $tasks
     */
    public function addTask(\Ptracker\TasksBundle\Entity\Task $tasks) {
        $this->tasks[] = $tasks;
    }

    /**
     * Get tasks
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getTasks() {
        return $this->tasks;
    }


    /**
     * Add comments
     *
     * @param Ptracker\TasksBundle\Entity\Comment $comments
     */
    public function addComment(\Ptracker\TasksBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;
    }

    /**
     * Get comments
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }
}