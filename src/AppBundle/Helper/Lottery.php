<?php
/**
 * Created by PhpStorm.
 * User: Alexa
 * Date: 16/3/20
 * Time: 下午8:27
 */
namespace AppBundle\Helper;
class Lottery
{
    static public function execute($em, $timestamp)
    {
        $award = self::getConfig();
        #默认中奖几率
        $rand_max = 10;
        $rand1 = rand(1, $rand_max);
        $rand2 = rand(1, $rand_max);
        $prize = $rand1 == $rand2 ? rand(1,8) : 0;
        #当前周中奖数量
        $w = date('w');
        $date1 = date('Y-m-d 00:00:00', $timestamp-$w*24*3600);
        $date2 = date('Y-m-d 23:59:59', strtotime($date1) + 7*24*3600 -1);
        $repo = $em->getRepository('AppBundle:Info');
        $qb = $repo->createQueryBuilder('a');
        $qb->select('COUNT(a)');
        $qb->where('a.prize != 0 AND a.createTime >= :createTime1 AND a.createTime <= :createTime2');
        $qb->setParameter(':createTime1', new \DateTime($date1), \Doctrine\DBAL\Types\Type::DATETIME);
        $qb->setParameter(':createTime2', new \DateTime($date2), \Doctrine\DBAL\Types\Type::DATETIME);
        $num1 = $qb->getQuery()->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE)->getSingleScalarResult();
        //var_dump($num1);
        if( $prize > 0 && $num1 < 20){
            $repo = $em->getRepository('AppBundle:Info');
            $qb = $repo->createQueryBuilder('a');
            $qb->select('COUNT(a)');
            $qb->where('a.prize = :prize');
            $qb->setParameter(':prize', $prize);
            $count = $qb->getQuery()->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE)->getSingleScalarResult();
            if($count >= $award[0][$prize-1]){
                $prize = 0;
            }
            else{
                //中奖规则
                if($award[1][$prize-1] == 0){
                    $w = date('w');
                    $date1 = date('Y-m-d 00:00:00', $timestamp-$w*24*3600);
                    $date2 = date('Y-m-d 23:59:59', strtotime($date1) + 7*24*3600 -1);
                }
                elseif($award[1][$prize-1] == 1){
                    $t = date('t', $timestamp);
                    $date1 = date('Y-m-01 00:00:00', $timestamp);
                    $date2 = date('Y-m-', $timestamp).$t.' 23:59:59';
                }
                else{
                    $n = date('n', $timestamp);
                    if($n%2 == 1){
                        $timestamp1 = $timestamp;
                        $temp_date = date('Y').'-'.(date('n')+1).'-01';
                        $timestamp2 = strtotime($temp_date);
                        $t = date('t', $timestamp2);
                    }
                    else{
                        $t = date('t', $timestamp);
                        $timestamp1 = $timestamp - $t*24*3600;
                        $timestamp2 = $timestamp;
                    }
                    $date1 = date('Y-m-01 00:00:00', $timestamp1);
                    $date2 = date('Y-m-', $timestamp2).$t.' 23:59:59';
                }
                #当前周期已发完
                $repo = $em->getRepository('AppBundle:Info');
                $qb = $repo->createQueryBuilder('a');
                $qb->select('COUNT(a)');
                $qb->where('a.prize = :prize AND a.createTime >= :createTime1 AND a.createTime <= :createTime2');
                $qb->setParameter(':prize', $prize);
                $qb->setParameter(':createTime1', new \DateTime($date1), \Doctrine\DBAL\Types\Type::DATETIME);
                $qb->setParameter(':createTime2', new \DateTime($date2), \Doctrine\DBAL\Types\Type::DATETIME);
                $num = $qb->getQuery()->setLockMode(\Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE)->getSingleScalarResult();

                if($num >= $award[2][$prize-1]){
                    $prize = 0;
                }
            }
        }
        else{
            $prize = 0;
        }

        return $prize;

    }
    static private function getConfig()
    {
        $pool = array(5,42,0,32,42,162,300,362);//奖池
        $rule = array(2,0,1,0,0,0,0);//0为每周平均数量,1为每月平均数量,2为每双月平均数量
        $allocate = array(0,0,0,0,0,0,0,0);//平均分配数量
        //$allocate = array(1,1,1,1,1,3,7,8);//平均分配数量
        return array($pool,$rule,$allocate);
    }
}