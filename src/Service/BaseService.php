<?php


namespace App\Service;


use Swift;
use DateTime;
use App\Entity\User;
use App\Entity\Action;
use App\Entity\Contrat;
use App\Entity\Interimaire;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

class BaseService
{
    private $mailer;
    protected $em;
    protected $interimaireMapping;
    protected $tokenStorage;
    protected $cc=array('ddiatou1@gmail.com','vieva03@gmail.com');
    public function __construct(\Swift_Mailer $mailer, EntityManagerInterface $em,TokenStorageInterface $tokenStorage)
    {
        $this->mailer=$mailer;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    public function sendMail($data)
    {
         $message = (new \Swift_Message())
            ->setFrom(array('no-reply@orange-sonatel.com'=>'ACTIVITE DCIRE'))
            ->setTo($data['to'])
            ->setBody($data['body'] ,
            'text/html' );

         if (isset($data['cc'])){
             $message->setCc($data['cc']);
         }
         $this->mailer->send($message);    
    }
    
    public function Date2Semaine($date=null)
    { 
        if(!$date){
            $date_test= new \DateTime();
        }else{
            $date_test= $date;
        }
        //dd($date_test->format("W"));
        return $date_test->format("W");
    }

    public function Date2Mois($date=null)
    { 
        if(!$date){
            $date_test= new \DateTime();
        }else{
            $date_test= $date;
        }
        //dd($date_test->format("W"));
        return $date_test->format("m");
    }


   

}