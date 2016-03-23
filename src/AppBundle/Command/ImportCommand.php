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

class ImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:import')
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
        $filename = preg_replace('/app$/si', 'web/', $this->getContainer()->get('kernel')->getRootDir())."1.csv";
        $handle = fopen($filename, "r");
        $em = $this->getContainer()->get('doctrine')->getManager();
        //$n = 0;
        $i =0;
        while(!feof($handle)) {
            $buffer = fgets($handle);
            if($i >= 100000)
                break;
            $code = new Entity\Code();
            $code->setCode(trim($buffer));
            $code->setIsActive(0);
            $em->persist($code);
            if($i%1000 == 999){
                $em->flush();
                $output->writeln(($i+1).'条已完成~');
            }
            ++$i;
        }
        fclose($handle);
        $output->writeln('ok');
    }
}