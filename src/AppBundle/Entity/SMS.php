<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="t_sms_log")
 */
class SMS
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(name="address",type="string", length=200, nullable=true)
     */
    protected $address;
    /**
     * @ORM\Column(name="prize_id",type="integer")
     */
    protected $prize;
    /**
     * @ORM\OneToOne(targetEntity="Info", inversedBy="sms")
     * @ORM\JoinColumn(name="info_id", referencedColumnName="id", nullable=true)
     */
    private $info;
    /**
     * @ORM\Column(name="mobile",type="string", length=20)
     */
    protected $mobile;
    /**
     * @ORM\Column(name="info_type",type="integer")
     */
    protected $type;
    /**
     * @ORM\Column(name="content",type="string", length=1000)
     */
    protected $content;
    /**
     * @ORM\Column(name="create_time",  type="datetime")
     */
    private $createTime;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return SMS
     */
    public function setAddress($address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     * @return SMS
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Get mobile
     *
     * @return string 
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return SMS
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;

        return $this;
    }

    /**
     * Get createTime
     *
     * @return \DateTime 
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }


    /**
     * Set prize
     *
     * @param integer $prize
     * @return SMS
     */
    public function setPrize($prize)
    {
        $this->prize = $prize;

        return $this;
    }

    /**
     * Get prize
     *
     * @return integer 
     */
    public function getPrize()
    {
        return $this->prize;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return SMS
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set info
     *
     * @param \AppBundle\Entity\Info $info
     * @return SMS
     */
    public function setInfo(\AppBundle\Entity\Info $info = null)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return \AppBundle\Entity\Info 
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return SMS
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }
}
