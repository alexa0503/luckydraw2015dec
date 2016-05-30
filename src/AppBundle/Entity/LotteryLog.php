<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="t_lottery_log")
 */
class LotteryLog
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Info", inversedBy="lotteryLog")
     * @ORM\JoinColumn(name="info_id", referencedColumnName="id", nullable=true)
     */
    private $info;
    /**
     * @ORM\OneToOne(targetEntity="Code", inversedBy="lotteryLog")
     * @ORM\JoinColumn(name="code_id", referencedColumnName="id")
     */
    private $code;
    /**
     * @ORM\Column(name="code", type="string", length=20)
     */
    private $codeTxt;
    /**
     * @ORM\Column(name="prize", type="integer")
     */
    private $prize;
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
     * Set codeTxt
     *
     * @param string $codeTxt
     * @return LotteryLog
     */
    public function setCodeTxt($codeTxt)
    {
        $this->codeTxt = $codeTxt;

        return $this;
    }

    /**
     * Get codeTxt
     *
     * @return string
     */
    public function getCodeTxt()
    {
        return $this->codeTxt;
    }

    /**
     * Set prize
     *
     * @param integer $prize
     * @return LotteryLog
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
     * @return LotteryLog
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
     * @return LotteryLog
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
     * Set info
     *
     * @param \AppBundle\Entity\Info $info
     * @return LotteryLog
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
     * Set code
     *
     * @param \AppBundle\Entity\Code $code
     * @return LotteryLog
     */
    public function setCode(\AppBundle\Entity\Code $code = null)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return \AppBundle\Entity\Code
     */
    public function getCode()
    {
        return $this->code;
    }
}
