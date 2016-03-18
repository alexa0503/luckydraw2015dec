<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="t_info")
 */
class Info
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(name="username",type="string", length=40)
     */
    protected $username;
    /**
     * @ORM\Column(name="mobile",type="string", length=40)
     */
    protected $mobile;
    /**
     * @ORM\Column(name="head_img",type="string", length=40)
     */
    protected $headImg;
    /**
     * @ORM\Column(name="wish_text",type="string", length=500, nullable = true)
     */
    protected $wishText;
    /**
     * @ORM\Column(name="like_num",type="integer")
     */
    protected $likeNum = 0;
    /**
     * @ORM\Column(name="has_lottery",type="boolean")
     */
    protected $hasLottery = 0;
    /**
     * @ORM\Column(name="prize",type="integer")
     */
    protected $prize = 0;
    /**
     * @ORM\OneToMany(targetEntity="LikeLog", mappedBy="photo")
     */
    private $likeLogs;
    /**
     * @ORM\Column(name="create_time",type="datetime")
     */
    protected $createTime;
    /**
     * @ORM\Column(name="create_ip",type="string", length=60)
     */
    protected $createIp;
    /**
     * @ORM\Column(name="is_active",type="boolean")
     */
    protected $isActive = 1;

 
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
     * Set username
     *
     * @param string $username
     * @return Info
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set mobile
     *
     * @param string $mobile
     * @return Info
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
     * Set headImg
     *
     * @param string $headImg
     * @return Info
     */
    public function setHeadImg($headImg)
    {
        $this->headImg = $headImg;

        return $this;
    }

    /**
     * Get headImg
     *
     * @return string 
     */
    public function getHeadImg()
    {
        return $this->headImg;
    }

    /**
     * Set createTime
     *
     * @param \DateTime $createTime
     * @return Info
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
     * @return Info
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
     * Constructor
     */
    public function __construct()
    {
        $this->infoLogs = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set likeNum
     *
     * @param integer $likeNum
     * @return Info
     */
    public function setLikeNum($likeNum)
    {
        $this->likeNum = $likeNum;

        return $this;
    }

    /**
     * Get likeNum
     *
     * @return integer 
     */
    public function getLikeNum()
    {
        return $this->likeNum;
    }

    /**
     * Set hasLottery
     *
     * @param boolean $hasLottery
     * @return Info
     */
    public function setHasLottery($hasLottery)
    {
        $this->hasLottery = $hasLottery;

        return $this;
    }

    /**
     * Get hasLottery
     *
     * @return boolean 
     */
    public function getHasLottery()
    {
        return $this->hasLottery;
    }

    /**
     * Set prize
     *
     * @param integer $prize
     * @return Info
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
     * Add infoLogs
     *
     * @param \AppBundle\Entity\info $infoLogs
     * @return Info
     */
    public function addInfoLog(\AppBundle\Entity\info $infoLogs)
    {
        $this->infoLogs[] = $infoLogs;

        return $this;
    }

    /**
     * Remove infoLogs
     *
     * @param \AppBundle\Entity\info $infoLogs
     */
    public function removeInfoLog(\AppBundle\Entity\info $infoLogs)
    {
        $this->infoLogs->removeElement($infoLogs);
    }

    /**
     * Get infoLogs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInfoLogs()
    {
        return $this->infoLogs;
    }

    /**
     * Add likeLogs
     *
     * @param \AppBundle\Entity\InfoLog $likeLogs
     * @return Info
     */
    public function addLikeLog(\AppBundle\Entity\InfoLog $likeLogs)
    {
        $this->likeLogs[] = $likeLogs;

        return $this;
    }

    /**
     * Remove likeLogs
     *
     * @param \AppBundle\Entity\InfoLog $likeLogs
     */
    public function removeLikeLog(\AppBundle\Entity\InfoLog $likeLogs)
    {
        $this->likeLogs->removeElement($likeLogs);
    }

    /**
     * Get likeLogs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLikeLogs()
    {
        return $this->likeLogs;
    }
    public function increaseLikeNum()
    {
        ++$this->likeNum;
        return $this;
    }

    /**
     * Set wishText
     *
     * @param string $wishText
     * @return Info
     */
    public function setWishText($wishText)
    {
        $this->wishText = $wishText;

        return $this;
    }

    /**
     * Get wishText
     *
     * @return string 
     */
    public function getWishText()
    {
        return $this->wishText;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Info
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
}
