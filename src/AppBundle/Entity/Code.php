<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="t_march_code")
 */
class Code
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(name="code",type="string", length=40)
     */
    protected $code;
    /**
     * @ORM\Column(name="is_active",type="boolean")
     */
    protected $isActive = 0;
    /**
     * @ORM\Column(name="batch",type="integer")
     */
    protected $batch = 1;
    /**
     * @ORM\Column(name="code_sort",type="integer")
     */
    protected $sort = 1;
    /**
     * @ORM\OneToOne(targetEntity="LotteryLog", mappedBy="info")
     */
    private $lotteryLog;

    private $num;



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
     * Set code
     *
     * @param string $code
     * @return Code
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
     * Set isActive
     *
     * @param boolean $isActive
     * @return Code
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
     * Set lotteryLog
     *
     * @param \AppBundle\Entity\LotteryLog $lotteryLog
     * @return Code
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

    /**
     * Set batch
     *
     * @param integer $batch
     * @return Code
     */
    public function setBatch($batch)
    {
        $this->batch = $batch;

        return $this;
    }

    /**
     * Get batch
     *
     * @return integer
     */
    public function getBatch()
    {
        return $this->batch;
    }

    /**
     * Set sort
     *
     * @param integer $sort
     * @return Code
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer
     */
    public function getSort()
    {
        return $this->sort;
    }
}
