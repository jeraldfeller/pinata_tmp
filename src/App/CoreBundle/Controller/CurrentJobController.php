<?php

namespace App\CoreBundle\Controller;

use App\CoreBundle\Entity\Timeline;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CurrentJobController extends Controller
{
    public function currentJobAction(Request $request)
    {
        $submit = 0;
        if(isset($_GET['fname']) && isset($_GET['sname']) && isset($_GET['email']) && isset($_GET['availFrom']) && isset($_GET['availTo'])){
            $dateMonday = date('Y-m-d', strtotime('monday this week'));
            $dateSunday = date('Y-m-d', strtotime('sunday this week'));
            $csv = 'pinata_applicants_'.$dateMonday.'-'.$dateSunday.'.csv';
            if(!file_exists('../tmp/'.$csv)){
                $data[] = implode('","', array(
                    'Record',
                    'Firstname',
                    'Surname',
                    'Contact Phone Number',
                    'Contact E-mail',
                    'Available From',
                    'Available To',
                    'Wamuran',
                    'Mareeba',
                    'Katherine',
                    'Darwin',
                    'Stanthorpe',
                    'Farm 5',
                    'I am eligible to work in Australia',
                    'I am happy to share my contact details with third party labour providers',
                    'Other'
                ));
                $record = 1;
            }else{
                $fp = file('../tmp/'.$csv, FILE_SKIP_EMPTY_LINES);
                $record = count($fp);
            }
            $data[] = implode('","', array(
                $record,
                stripslashes(str_replace(',', ' ', trim(preg_replace('/\s+/', ' ', html_entity_decode($_GET['fname']))))),
                stripslashes(str_replace(',', ' ', trim(preg_replace('/\s+/', ' ', html_entity_decode($_GET['sname']))))),
                stripslashes(str_replace(',', ' ', trim(preg_replace('/\s+/', ' ', html_entity_decode($_GET['phone']))))),
                stripslashes(str_replace(',', ' ', trim(preg_replace('/\s+/', ' ', html_entity_decode($_GET['email']))))),
                $_GET['availFrom'],
                $_GET['availTo'],
                $_GET['wamuran'],
                $_GET['mareeba'],
                $_GET['katherine'],
                $_GET['darwin'],
                $_GET['stanthorpe'],
                $_GET['farm5'],
                $_GET['eligible'],
                $_GET['share'],
                stripslashes(str_replace(',', ' ', trim(preg_replace('/\s+/', ' ', html_entity_decode($_GET['other']))))),
            ));

            $file = fopen('../tmp/'.$csv,"a");
            foreach ($data as $line){
                fputcsv($file, explode('","',$line));
            }
            fclose($file);
            $submit = 1;
        }

        if(isset($_GET['s'])){
            $submit = 2;
        }
        return $this->render('@AppCore/JobForm/index.html.twig', array('submit' => $submit
        ));

    }


    /**
     *@Route("/current-jobs-submit", name="currentJobs")
     */
    public function currentJobSubmitAction(){
        $data = json_decode($_POST['param'], true);

        var_dump($data);

        return new Response(true);
    }


}
