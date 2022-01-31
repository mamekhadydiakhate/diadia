<?php

namespace App\Controller;




use DateTime;
use App\Entity\User;
use App\Entity\Activite;
use App\Entity\Evenement;
use App\Entity\Structure;
use App\Entity\Difficulte;
use App\Entity\Periodicite;
use App\Annotation\QMLogger;
use App\Service\BaseService;
use App\Entity\TrancheHoraire;
use App\Controller\BaseController;
use App\Repository\UserRepository;
use App\Entity\PointDeCoordination;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mailer\Mailer;
use App\Repository\ActiviteRepository;
use App\Repository\StructureRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TrancheHoraireRepository;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ActiviteController extends BaseController
{
    private ActiviteRepository $activiteRepo;
    private UserRepository $userRepo;
    private StructureRepository $structurerepo;
    private SerializerInterface $serializer;
    private BaseService $baseService; 
    private $archive;

    public function __construct(EntityManagerInterface $manager, MailerInterface $mailer, ActiviteRepository $activiteRepo ,UserRepository $userRepo ,
                StructureRepository $structureRepo ,TrancheHoraireRepository $trancheHoraireRepo,BaseService $baseService)
    {
        
        $this->activiteRepo = $activiteRepo;
        $this->userRepo = $userRepo;
        $this->structureRepo = $structureRepo;
        $this->serializer = new Serializer();
        $this->baseService=$baseService;
        $this -> archive = $manager;

        

    }
    /**
     * @Post("/api/activite", name="activites")
     */
    public function addActivite( Request $request,BaseService $baseService, ValidatorInterface $validator ,SerializerInterface $serializer): Response
    {

        $activite = $serializer->deserialize($request->getContent(), Activite::class,'json');
        $errors = $validator->validate($activite);
    if (count($errors) > 0)
    {
        $errorsString =$serializer->serialize($errors,"json");
        
        return new JsonResponse( $errorsString ,Response::HTTP_BAD_REQUEST,[],true);
    }
        $user = $this->getUser();
        $structure = $user->getStructure();
        $date = $activite->getDate(); 
        $semaine= $this->baseService->Date2Semaine($date);
        
        $activite->setUser($user);
        $activite->setStructure($structure);
        $activite->setSemaine($semaine);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($activite);
        $data = array(
            'to' => $user->getEmail(),
            'cc'=>array('mamekhady.diakhate@orange-sonatel.com','genvievesebiasylvie.mendy@orange-sonatel.com'),
                        //'moussa.dieye@orange-sonatel.com','chantal.badiat@orange-sonatel.com'),
            'subject' => 'la plateforme Suivi des Activités et de la Roadmap',
            'body' => 'Bonjour '.$user->getPrenom().' '.$user->getNom().',
            <br><br>Merci d\'avoir renseigner le suivi d\'actitivé de la semaine'. '<br>'
        );
        $this->baseService->sendMail($data);
        $entityManager->flush();
        
        return $this->json($activite, 200, [], ['groups' => 'activite:show']);
        
    }
    
    
     /**
     * @Get("/api/activite", name="activite")
     */
    public function listActivite(): Response
    {
        #$activiteJson=file_get_contents("https://server/reportserver/ReportService2010.asmx?wsdl");
        $semaine= strftime("%W");
        $year = date("Y");
        $user = $this->getUser();
        $activites = $this->activiteRepo->precede($semaine, $year);
        return $this->json($activites, 200, [], ['groups' => 'activite:read']);
    }
   
      /**
     * @Get("/api/activite/{id}")
     * @QMLogger(message="Details activite")
     */
    public function detailsactivite($id){
        $activites = $this->activiteRepo->find($id);
        
       return  $this->json($activites, 200, [], ['groups' => 'activite:show']);
    }
    

    /**
     * @Get("/api/activite/semaine/{semaine}", name="precedent-activite")
     */
    public function PrecedeActivite($semaine): Response
    {
       //recupere annee courante
       $year = date("Y");
       $activites=$this->activiteRepo->precede($semaine, $year);
       // recupere l'utilisateur via le token,
       $user = $this->getUser()->getId();
       //recuper les activité ayant comme semaine  $semaine_passer, et comme utilisateur l'utilisateur connecter
       
       return $this->json($activites, 200, [], ['groups' => 'activite:show']);
    }
    
    /**
    * @Delete("/api/activite/{id}", name="delete_activite")
    */
    public function deleteactivite(int $id): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        $activite = $entityManager->getRepository(activite::class)->find($id);
        $entityManager->remove($activite);
        $user = $this->getUser();
        $data = array(
            'to' => $user->getEmail(),
            'cc'=>array('mamekhady.diakhate@orange-sonatel.com','genvievesebiasylvie.mendy@orange-sonatel.com',
                        //'moussa.dieye@orange-sonatel.com','chantal.badiat@orange-sonatel.com'
                    ),
            'subject' => 'Données de connexion à la plateforme Suivi des Activités et de la Roadmap',
            'body' => 'Bonjour '.$user->getPrenom().' '.$user->getNom().',
            <br><br>Suppression de l\'activité de '.$user->getStructure().' enregistré avec succée'. '<br>'
        );
        $this->baseService->sendMail($data);
        
        $entityManager->flush();

    return $this->json(['status'=>200, "message"=>"activité supprimé avec succés"]);
    }

     /**
     * @Put("/api/activite/{id}")
     * @QMLogger(message="modifier activite")
     */
    public function modifiActivite($id, Activite $activite, Request $request):Response
    {
        
        $activite = $this->activiteRepo->find($id);
        $baseService= $this->baseService;
        //$user= $this->userRepo->find($request->get('user'));
        $user = $this->getUser();
        $structure = $user->getStructure();
       
        $date = ($request->request->get('date'));
        $activite->setDate(new \DateTime($date)); 
        $activite->setLibelle($request->request->get('libelle'));
        $activite->setUser($user);
        $activite->setStructure($structure);
        $activite->setSemaine((int) strftime("%W"));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($activite);
        $user = $this->getUser();
        $data = array(
            'from' =>'ddiatou1@gmail.com',
            'to' => $user->getEmail(),
            'cc'=>array('mamekhady.diakhate@orange-sonatel.com','genvievesebiasylvie.mendy@orange-sonatel.com',
                        'moussa.dieye@orange-sonatel.com','chantal.badiat@orange-sonatel.com'),
            'subject' => 'Données de connexion à la plateforme Suivi des Activités et de la Roadmap',
            'body' => 'Bonjour '.$user->getPrenom().' '.$user->getNom().',
            <br><br>Modification de l\'activité de '.$user->getStructure().' enregistré avec succée'. '<br>'
        );
        $this->baseService->sendMail($data);
            $entityManager->flush();
        
        return $this->json(['status'=>200, "message"=>"activite modifie avec succes"]);
    }
  
    /**
     * @var EntityManagerInterface
     * @Get("/api/activite/archive/{id}")
     */
    public function __invoke(Activite $data): Activite
        {
            $data -> setArchives(1);
            //$this -> archive -> persist($update);
            $this -> archive -> flush();
            return $data;
        }
}
