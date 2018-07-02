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

            //   ->addArgument('action', InputArgument::REQUIRED, 'What type of action do you want to execute?')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(['test']);
        $dateMonday = date('Y-m-d', strtotime('monday last week'));
        $dateSunday = date('Y-m-d', strtotime('sunday last week'));
        $fileName = 'pinata_applicants_'.$dateMonday.'-'.$dateSunday.'csv';
        $fileDir = 'tmp/pinata_applicants_2018-07-02-2018-07-08.csv';

        $email = new PHPMailer();
       // $email->isSMTP(false);
        $email->From      = 'jeraldfeller@gmail.com';
        $email->Subject   = 'Job Applicants';
        $email->Body      =  'for ' . $dateMonday . ' to ' . $dateSunday;
        $email->AddAddress( 'jeraldfeller@gmail.com' );
        $file_to_attach = $fileDir;
        $email->AddAttachment( $file_to_attach , 'pinata_applicants_2018-07-02-2018-07-08.csv' );
        $return = $email->Send();

        var_dump($return);

    }

}