<?php

namespace App\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * Contact.
 *
 * @ORM\Table(name="app_enquiry")
 * @ORM\Entity
 */
class Contact
{
    use TimestampableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="email", type="string")
     */
    protected $email;

    /**
     * @var string
     * @ORM\Column(name="subject", type="string")
     */
    protected $subject;

    /**
     * @var string
     * @ORM\Column(name="message", type="text")
     */
    protected $message;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_address", type="string", length=45)
     */
    protected $ipAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent", type="string", length=255)
     */
    protected $httpUserAgent;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set subject.
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message.
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set ipAddress.
     *
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * @return string
     */
    public function getHttpUserAgent()
    {
        return $this->httpUserAgent;
    }

    /**
     * Set httpUserAgent.
     *
     * @param string $httpUserAgent
     */
    public function setHttpUserAgent($httpUserAgent)
    {
        $this->httpUserAgent = $httpUserAgent;

        return $this;
    }
}
