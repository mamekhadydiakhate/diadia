<?php
namespace App\Controller;

// use Symfony\Component\HttpKernel\Controller\ErrorController as Controller;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExceptionController extends Controller
{
    protected $kernel;
    protected $twig;
    protected $container;

    public function __construct(HttpKernelInterface $kernel, \Twig_Environment $twig,$debug=true, ContainerInterface $container)
    {
        $this->kernel = $kernel;
        $this->twig = $twig;
        $this->debug = $debug;
    }

    public function sendMailError($mailer, $subject, $body, $to, $cc, $attach):bool
    {
        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom(array('no-reply@orange-sonatel.com'=>'GDI SONATEL'))
            ->setContentType('text/html')
            ->setTo($to)
            ->setCc($cc)
            //->setBcc($bcc)
            ->setBody($body)
            ->attach(\Swift_Attachment::fromPath($attach));
        try{
            $mailer->send($message);
            return true;
        } catch(\Swift_TransportException $e) {
            // $isSend = false;
            return false;
        }
    }
}
