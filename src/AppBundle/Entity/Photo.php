<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="t_photo")
 */
class Photo
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
   
    /**
     * @ORM\ManyToOne(targetEntity="WechatUser", inversedBy="photo")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    /**
     * @ORM\Column(name="title",  type="string", length=200)
     */
    private $title;
    /**
     * @ORM\Column(name="img_url",  type="string", length=200)
     */
    private $imgUrl;
    /**
     * @ORM\Column(name="type",  type="integer")
     */
    private $type = 0;
    /**
     * @ORM\Column(name="favour_num",  type="integer")
     */
    private $favourNum = 0;
    /**
     * @ORM\Column(name="create_time",  type="datetime")
     */
    private $createTime;
    /**
     * @ORM\Column(name="create_ip", type="string", length=60)
     */
    private $createIp;


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
     * Set title
     *
     * @param string $title
     * @return Photo
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Photo
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

    /**
     * Set favourNum
     *
     * @param integer $favourNum
     * @return Photo
     */
    public function setFavourNum($favourNum)
    {
        $this->favourNum = $favourNum;

        return $this;
    }

    /**
     * Get favourNum
     *
     * @return integer 
     */
    public function getFavourNum()
    {
        return $this->favourNum;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return Photo
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
     * Set createIp
     *
     * @param string $createIp
     * @return Photo
     */
    public function setCreateIp($createIp)
    {
        $this->createIp = $createIp;

        return $this;
    }

    /**
     * Get createIp
     *
     * @return string 
     */
    public function getCreateIp()
    {
        return $this->createIp;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\WechatUser $user
     * @return Photo
     */
    public function setUser(\AppBundle\Entity\WechatUser $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\WechatUser 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set imgUrl
     *
     * @param string $imgUrl
     * @return Photo
     */
    public function setImgUrl($imgUrl)
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    /**
     * Get imgUrl
     *
     * @return string 
     */
    public function getImgUrl()
    {
        return $this->imgUrl;
    }
}
