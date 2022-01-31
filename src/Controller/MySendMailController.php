<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MySendMailController extends AbstractController
{
    /**
     * @Get("/", name="my_send_mail")
     */
    public function index(\Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Hello Email'))
        ->setFrom('diadiadev03dev@gmail.com')
        ->setTo('ddiatou1@gmail.com')
        ->setBody("Bonjour Djatou");

        $mailer->send($message);

        return $this->render('my_send_mail/index.html.twig');
    }
}
