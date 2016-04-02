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
        $qb->where('a.prize != :prize AND a.type = 0 AND a.createTime < :createTime');
        $qb->setParameter(':prize', 0);
        $qb->setParameter(':createTime', new \DateTime('now'));
        $list = $qb->getQuery()->getResult();
        foreach ($list as $v){
            $output->writeln($v->getPrize().','.$v->getMobile());
            Helper\SMS::send($em, array(
                'mobile'=>$v->getMobile(),
                'info'=>$v,
                'prize'=>$v->getPrize(),
            ));
        }
        $output->writeln('message send ok');
    }
}