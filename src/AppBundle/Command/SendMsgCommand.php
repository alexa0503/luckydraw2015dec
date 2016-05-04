<?php
/**
 * Created by PhpStorm.
 * User: Alexa
 * Date: 16/3/20
 * Time: 下午10:00
 */
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Entity;
use AppBundle\Helper;

class SendMsgCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:send')
            ->setDescription('Greet someone')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Who do you want to greet?'
            )
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $repo = $em->getRepository('AppBundle:Info');
        $qb = $repo->createQueryBuilder('a');
        $qb->where('(a.id < 59201 OR a.id > 59215) AND a.prize != :prize AND a.type = 0 AND a.createTime >= :createTime1 AND a.createTime <= :createTime2');
        $qb->setParameter(':prize', 0);
        $qb->setParameter(':createTime1', new \DateTime('2016-04-04 00:00:00'));
        $qb->setParameter(':createTime2', new \DateTime('2016-04-19 23:59:59'));
        $list = $qb->getQuery()->getResult();
        $i = 0;
        foreach ($list as $v){
            if( $v->getSms() != null && empty($v->getSms()->getAddress())){
                $output->writeln($v->getPrize().','.$v->getId().','.$v->getUsername().','.$v->getMobile());

                $result = Helper\SMS::send($em, array(
                    'mobile'=>$v->getMobile(),
                    'info'=>$v,
                    'prize'=>$v->getPrize(),
                    'type'=>$v->getType(),
                ));
                ++$i;
                var_dump($result);
            }
            //$output->writeln($result);
        }
        $output->writeln($i.' message send ok');
    }
}