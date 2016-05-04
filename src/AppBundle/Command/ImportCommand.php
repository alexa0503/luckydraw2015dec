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
use Imagine\Imagick\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

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
        ini_set('memory_limit','512M');
        set_time_limit(0);
        $em = $this->getContainer()->get('doctrine')->getManager();
        //$em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:Info');
        $queryBuilder = $repository->createQueryBuilder('a');
        $queryBuilder->where('a.id > 38306');
        $em->getConnection()
            ->getWrappedConnection()
            ->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $query = $queryBuilder->getQuery();
        $info = $query->iterate();
        $batchSize = 30;
        $i = 0;

        //$info = $em->getRepository('AppBundle:Info')->findAll();
        $file_path = preg_replace('/app$/si', 'web/uploads/', $this->getContainer()->get('kernel')->getRootDir());
        $imagine = new Imagine();
        foreach($info as $row){
            $v = $row[0];
            if($v->getHeadImg() != '' && $v->getHeadImg() != 'default.png' && file_exists($file_path.$v->getHeadImg())){
                var_dump($file_path.$v->getHeadImg());
                $image = $imagine->open($file_path.$v->getHeadImg());
                $size = $image->getSize();
                if( $size->getHeight() > 600 || $size->getWidth() > 600){
                    $image->thumbnail(new Box(600, 600),ImageInterface::THUMBNAIL_INSET)->save($file_path.$v->getHeadImg());
                }
                $image = $imagine->open($file_path.$v->getHeadImg());
                $image->thumbnail(new Box(100, 100),ImageInterface::THUMBNAIL_INSET)->save($file_path.'thumb/'.$v->getHeadImg());
                $image->effects()->grayscale();
                $image->save($file_path.'gray/'.$v->getHeadImg());
                //$response->setContent();
                $output->writeln($v->getId().",".$v->getHeadImg());
            }
            if (($i % $batchSize) == 0)
            {
                $em->flush(); // if you need to update something
                $em->clear(); // frees memory. BEWARE: former entity references are lost.
            }
            ++$i;
        }
        /*
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
        */
    }
}