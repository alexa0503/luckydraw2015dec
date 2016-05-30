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
    private $id;
    /**
     * @ORM\Column(name="username",type="string", length=40)
     */
    private $username;
    /**
     * @ORM\Column(name="mobile",type="string", length=40)
     */
    private $mobile;
    /**
     * @ORM\Column(name="head_img",type="string", length=40)
     */
    private $headImg;
    /**
     * @ORM\Column(name="wish_text",type="string", length=500, nullable = true)
     */
    private $wishText;
    /**
     * @ORM\Column(name="like_num",type="integer")
     */
    private $likeNum = 0;
    /**
     * @ORM\Column(name="has_lottery",type="boolean")
     */
    private $hasLottery = 0;
    /**
     * @ORM\Column(name="prize",type="integer")
     */
    private $prize = 0;
    /**
     * @ORM\OneToMany(targetEntity="LikeLog", mappedBy="info")
     */
    private $likeLogs;
    /**
     * @ORM\Column(name="create_time",type="datetime")
     */
    private $createTime;
    /**
     * @ORM\Column(name="create_ip",type="string", length=60)
     */
    private $createIp;
    /**
     * @ORM\Column(name="is_active",type="boolean")
     */
    private $isActive = 1;
    /**
     * @ORM\Column(name="code",type="string", length=60, nullable=true)
     */
    private $code = null;
    /**
     * @ORM\Column(name="address",type="string", length=200, nullable=true)
     */
    private $address = null;
    /**
     * @ORM\Column(name="type",type="integer")
     */
    private $type = 0;
    /**
     * @ORM\OneToOne(targetEntity="LotteryLog", mappedBy="info")
     */
    private $lotteryLog;
    /**
     * @ORM\OneToOne(targetEntity="SMS", mappedBy="info")
     */
    private $sms;
    /**
     * @ORM\Column(name="lottery_time",type="datetime", nullable=true)
     */
    private $lotteryTime;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->likeLogs = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

    /**
     * Set code
     *
     * @param string $code
     * @return Info
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Info
     */
    public function setAddress($address)
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
     * Set type
     *
     * @param integer $type
     * @return Info
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
     * Add likeLogs
     *
     * @param \AppBundle\Entity\LikeLog $likeLogs
     * @return Info
     */
    public function addLikeLog(\AppBundle\Entity\LikeLog $likeLogs)
    {
        $this->likeLogs[] = $likeLogs;

        return $this;
    }

    /**
     * Remove likeLogs
     *
     * @param \AppBundle\Entity\LikeLog $likeLogs
     */
    public function removeLikeLog(\AppBundle\Entity\LikeLog $likeLogs)
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

    /**
     * Set lotteryLog
     *
     * @param \AppBundle\Entity\LotteryLog $lotteryLog
     * @return Info
     */
    public function setLotteryLog(\AppBundle\Entity\LotteryLog $lotteryLog = null)
    {
        $this->lotteryLog = $lotteryLog;

        return $this;
    }

    /**
     * Get lotteryLog
     *
     * @return \AppBundle\Entity\LotteryLog 
     */
    public function getLotteryLog()
    {
        return $this->lotteryLog;
    }
    public function increaseLikeNum()
    {
        ++$this->likeNum;
        return $this;
    }

    /**
     * Set sms
     *
     * @param \AppBundle\Entity\SMS $sms
     * @return Info
     */
    public function setSms(\AppBundle\Entity\SMS $sms = null)
    {
        $this->sms = $sms;

        return $this;
    }

    /**
     * Get sms
     *
     * @return \AppBundle\Entity\SMS 
     */
    public function getSms()
    {
        return $this->sms;
    }

    /**
     * Add sms
     *
     * @param \AppBundle\Entity\SMS $sms
     * @return Info
     */
    public function addSm(\AppBundle\Entity\SMS $sms)
    {
        $this->sms[] = $sms;

        return $this;
    }

    /**
     * Remove sms
     *
     * @param \AppBundle\Entity\SMS $sms
     */
    public function removeSm(\AppBundle\Entity\SMS $sms)
    {
        $this->sms->removeElement($sms);
    }

    /**
     * Set lotteryTime
     *
     * @param \DateTime $lotteryTime
     * @return Info
     */
    public function setLotteryTime($lotteryTime)
    {
        $this->lotteryTime = $lotteryTime;

        return $this;
    }

    /**
     * Get lotteryTime
     *
     * @return \DateTime 
     */
    public function getLotteryTime()
    {
        return $this->lotteryTime;
    }
}
