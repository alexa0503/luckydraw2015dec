<?php
/**
 * 抽奖规则
 */
namespace AppBundle\Helper;
class Lottery
{
    protected $em;
    protected $timestamp;
    protected $code = null;
    protected $rand1;
    protected $rand2;
    protected $year;
    protected $month;
    protected $day;
    protected $hour;
    protected $week;
    protected $pre_prize;

    public function __construct($em, $timestamp, $code = null)
    {
        $this->em = $em;
        $this->timestamp = $timestamp;
        $this->code = $code;
        //$rand_max = $code != null ? 5 : 4;
        $rand_max = 20;
        $this->rand1 = rand(1, $rand_max);
        $this->rand2 = rand(1, $rand_max);
        $this->month = date('n', $timestamp);
        $this->day = date('j', $timestamp);
        $this->hour = date('G', $timestamp);
        $this->week = date('w', $timestamp);
        $this->year = date('Y', $timestamp);
        $this->pre_prize = rand(1, 8);//预抽奖,不考虑规则
        //return $this->execute();
    }

    public function execute()
    {
        #抽奖截至时间
        if ($this->year != 2016) {
            return 0;
        }
        $em = $this->em;
        $rand1 = $this->rand1;
        $rand2 = $this->rand2;
        $hour = $this->hour;
        $week = $this->week;
        $pre_prize = $this->pre_prize;
        $timestamp = $this->timestamp;
        $code = $this->code;
        //21点前未中
        if ($rand1 != $rand2 && $hour < 21) {
            return 0;
        }

        //本周总抽奖数
        $num = $week == 0 ? 7 : $week - 1;
        $date1 = date('Y-m-d 00:00:00', $timestamp - $num * 24 * 3600);
        $date2 = date('Y-m-d 23:59:59', $timestamp - ($num - 6) * 24 * 3600);
        $repo = $em->getRepository('AppBundle:Info');
        $qb = $repo->createQueryBuilder('a');
        $qb->select('COUNT(a)');
        $qb->where('a.prize !=0 AND a.type = 0 AND a.lotteryTime >= :createTime1 AND a.lotteryTime <= :createTime2');
        $qb->setParameter(':createTime1', new \DateTime($date1), \Doctrine\DBAL\Types\Type::DATETIME);
        $qb->setParameter(':createTime2', new \DateTime($date2), \Doctrine\DBAL\Types\Type::DATETIME);
        $week_num1 = $qb->getQuery()->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE)->getSingleScalarResult();

        $repo = $em->getRepository('AppBundle:LotteryLog');
        $qb = $repo->createQueryBuilder('a');
        $qb->select('COUNT(a)');
        $qb->where('a.prize !=0 AND a.createTime >= :createTime1 AND a.createTime <= :createTime2');
        $qb->setParameter(':createTime1', new \DateTime($date1), \Doctrine\DBAL\Types\Type::DATETIME);
        $qb->setParameter(':createTime2', new \DateTime($date2), \Doctrine\DBAL\Types\Type::DATETIME);
        $week_num2 = $qb->getQuery()->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE)->getSingleScalarResult();

        //var_dump($week_num1, $week_num2, $date1, $date2);
        if (preg_match('/^3\d{6}(0\d{4}|10[01]\d{2}|10200)$/i', $code)) {
            $lottery_type = 1;
        } elseif (preg_match('/^3\d{11}$/i', $code) && (int)substr($code, 4) >= 10080201 && (int)substr($code, 4) <= 10087700) {
            $lottery_type = 2;
        } else {
            $lottery_type = 0;
        }
        #本周普通情况总抽奖数已达20
        if ($week_num1 + $week_num2 >= 20 && $lottery_type == 0) {
            return 0;
        }

        $date1 = date('Y-m-d 00:00:00', $timestamp);
        $date2 = date('Y-m-d 23:59:59', $timestamp);

        if ($lottery_type > 0) {
            $prize = $this->getPrizeFromCode($lottery_type);
            $repo = $em->getRepository('AppBundle:LotteryLog');
            $qb = $repo->createQueryBuilder('a');
            $qb->select('COUNT(a)');
            $qb->where('a.prize = :prize AND a.createTime >= :createTime1 AND a.createTime <= :createTime2 AND a.code >= :code1 AND a.code <= :code2');
            $qb->setParameter(':prize', $prize);
            $qb->setParameter(':createTime1', new \DateTime($date1), \Doctrine\DBAL\Types\Type::DATETIME);
            $qb->setParameter(':createTime2', new \DateTime($date2), \Doctrine\DBAL\Types\Type::DATETIME);
            if ($lottery_type == 2) {
                $qb->setParameter(':code1', 160201);
                $qb->setParameter(':code2', 167700);
            } else {
                $qb->setParameter(':code1', 100001);
                $qb->setParameter(':code2', 110200);
            }
            $total_num = $qb->getQuery()->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE)->getSingleScalarResult();
            if ($total_num >= 1)
                $prize = 0;

            if( $prize == 1 || $prize == 5){
                $prize = 0;
            }
            return $prize;
        } else {
            $repo = $em->getRepository('AppBundle:Info');
            $qb = $repo->createQueryBuilder('a');
            $qb->select('COUNT(a)');
            $qb->where('a.prize = :prize AND a.type = 0 AND a.lotteryTime >= :createTime1 AND a.lotteryTime <= :createTime2');
            $qb->setParameter(':prize', $pre_prize);
            $qb->setParameter(':createTime1', new \DateTime($date1), \Doctrine\DBAL\Types\Type::DATETIME);
            $qb->setParameter(':createTime2', new \DateTime($date2), \Doctrine\DBAL\Types\Type::DATETIME);
            $num1 = $qb->getQuery()->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE)->getSingleScalarResult();

            $repo = $em->getRepository('AppBundle:LotteryLog');
            $qb = $repo->createQueryBuilder('a');
            $qb->select('COUNT(a)');
            $qb->where('a.prize = :prize AND a.createTime >= :createTime1 AND a.createTime <= :createTime2');
            $qb->setParameter(':prize', $pre_prize);
            $qb->setParameter(':createTime1', new \DateTime($date1), \Doctrine\DBAL\Types\Type::DATETIME);
            $qb->setParameter(':createTime2', new \DateTime($date2), \Doctrine\DBAL\Types\Type::DATETIME);
            $num2 = $qb->getQuery()->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE)->getSingleScalarResult();
            $total_num = $num1 + $num2;

            if ($total_num >= 1)
                return 0;
            $prize = $this->getPrize();

            if( $prize == 1 || $prize == 5){
                $prize = 0;
            }
            return $prize;
        }
    }

    ///正常情况下抽奖
    public function getPrize()
    {
        $rand1 = $this->rand1;
        $rand2 = $this->rand2;
        $month = $this->month;
        $day = $this->day;
        $hour = $this->hour;
        $week = $this->week;
        $pre_prize = $this->pre_prize;
        $prize = 0;
        switch ($pre_prize) {
            case 1:
                #中奖日,双月15号
                if ($month % 2 == 0 && $day == 15) {
                    if ($rand1 == $rand2 || $hour >= 21) {
                        $prize = $pre_prize;
                    }
                }
                break;

            case 2:
                #每周周三13点以后
                if ($week == 3 && $hour  >= 13) {
                    if ($rand1 == $rand2 || $hour >= 21) {
                        $prize = $pre_prize;
                    }
                }
                break;

            case 3:
                #每月15日
                if ($day == 15) {
                    if ($rand1 == $rand2 || $hour >= 21) {
                        $prize = $pre_prize;
                    }
                }
                break;

            case 4:
                #每周周二12点
                if ($week == 2 && $hour >= 12) {
                    if ($rand1 == $rand2 || $hour >= 21) {
                        $prize = $pre_prize;
                    }
                }
                break;

            case 5:
                #每周周四15点
                if ($week == 4 && $hour >= 15) {
                    if ($rand1 == $rand2 || $hour >= 21) {
                        $prize = $pre_prize;
                    }
                }
                break;

            case 6:
                #每周周一三五
                if ($week == 1 || $week == 3 || $week == 5) {
                    if ($rand1 == $rand2 || $hour >= 21) {
                        $prize = $pre_prize;
                    }
                }
                break;

            default:
                #每天一个
                if ($rand1 == $rand2 || $hour >= 21) {
                    $prize = $pre_prize;
                }
                break;
        }
        return $prize;
    }

    ////通过抽奖码抽奖
    public function getPrizeFromCode($type)
    {
        $month = $this->month;
        $day = $this->day;
        $hour = $this->hour;
        $prize = 0;
        switch ($type) {
            case 1:
                # 唯品会抽奖码
                if ($hour >= 12) {
                    #5.30 ipad
                    if ($month == 5 && $day == 30) {
                        $prize = 2;
                    } #6.2 6.9 iwatch
                    elseif ($month == 6 && ($day == 2 || $day == 9)) {
                        $prize = 4;
                    } #6.6 6.15 ipad
                    elseif ($month == 6 && ($day == 6 || $day == 15)) {
                        $prize = 2;
                    } #5.30 金项链
                    elseif ($month == 6 && $day == 12) {
                        $prize = 5;
                    }
                }
                break;

            case 2:
                #ecommerce 新增抽奖码
                if ($hour >= 12) {
                    #6.30 ipad
                    if ($month == 6 && $day == 30) {
                        $prize = 2;
                    } #7.28 金项链
                    elseif ($month == 7 && $day == 28) {
                        $prize = 5;
                    } #8.25 ipad
                    elseif ($month == 8 && $day == 25) {
                        $prize = 2;
                    } #9.30 金项链
                    elseif ($month == 9 && $day == 30) {
                        $prize = 5;
                    }
                }
                break;

            default:
                # code...
                break;
        }
        return $prize;
    }
}
