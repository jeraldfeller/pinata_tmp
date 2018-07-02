<?php

namespace App\CoreBundle\Command\CurrentJob;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class CurrentJobCommand extends  ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "app/console")
            ->setName('job-application:send')

            // the short description shown while running "php app/console list"
            ->setDescription('This will send the csv file trough email')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('')

            ->addArgument('action', InputArgument::REQUIRED, 'two arguments needed, separated by comma. email, root_dir')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $arguments = explode(',', $input->getArgument('action'));
        $emailAddress = $arguments[0];
        $root = $arguments[1];
        $dateMonday = date('Y-m-d', strtotime('monday this week'));
        $dateSunday = date('Y-m-d', strtotime('sunday this week'));
        $fileName = 'pinata_applicants_'.$dateMonday.'-'.$dateSunday.'csv';
        $fileDir = $root.'/tmp/'.$fileName;

        $email = new PHPMailer();
       // $email->isSMTP(false);
        $email->From      = 'pinata@webmaster.com';
        $email->Subject   = 'Job Applicants';
        $email->Body      =  'for ' . $dateMonday . ' to ' . $dateSunday;
        $email->AddAddress( $emailAddress );
        $file_to_attach = $fileDir;
        $email->AddAttachment( $file_to_attach , $fileName );
        $return = $email->Send();

        if($return){
            $output->writeln(['File successfully sent']);
        }else{
            $output->writeln(['File failed to sent']);
        }
    }

}