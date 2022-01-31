<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Workflow;
use App\Service\BaseService;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use App\Controller\BaseController;
use App\Controller\UserController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WorkflowController extends BaseController
{
    private UserRepository $userRepo;
    private BaseService $baseService;
    private UserController $userController;

    public function __construct(UserRepository $userRepo, UserController $userController, BaseService $baseService, MailerInterface $mailer){
        $this->userRepo=$userRepo;
        $this->mailer=$mailer;
        $this->baseService=$baseService;
        $this->userController=$userController;

    }
     
  /**
     * @Post("admin/workflow")
     */
    public function sendworkflow(\Swift_Mailer $mailer, Request $request): Response
    {
        $userMails = $this->userRepo->listEmailUsers();
        dd($userMails);
        $message = (new \Swift_Message('Hello Email'))
        ->setFrom('diadiadev03dev@gmail.com')
        ->setTo('ddiatou1@gmail.com')
        ->setBody("Bonjour Djatou");
    
        $mailer->send($message);
       
    return $this->json(['status'=>200, "message"=>"workflow envoyé avec succés"]);

       

    }
    
}
