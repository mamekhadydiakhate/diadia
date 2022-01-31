<?php

namespace App\Controller;




use App\Entity\User;
use App\Entity\Autorite;
use App\Entity\Evenement;
use App\Entity\Structure;
use App\Entity\Commentaire;
use App\Entity\Periodicite;
use App\Annotation\QMLogger;
use App\Service\BaseService;
use FOS\UserBundle\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use App\Controller\BaseController;
use App\Repository\UserRepository;
use App\Entity\HistoriqueEvenement;
use App\Repository\EvenementRepository;
use App\Repository\StructureRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PeriodiciteRepository;
use App\Repository\AutoriteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\HistoriqueEvenementRepository;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EvenementController extends BaseController
{
    private UserRepository $userRepo;
    private EvenementRepository $evenementRepo;
    private StructureRepository $structureRepo;
    private PeriodiciteRepository $periodiciterepo;
    private autoriteRepository $autoriteRepo;
    private HistoriqueEvenementRepository $historiqueEvenementRepo;
    private BaseService $baseService; 
    private $archive;

    public function __construct(EntityManagerInterface $manager, BaseService $baseService,UserRepository $userRepo ,EvenementRepository $evenementRepo,
                periodiciteRepository $periodiciteRepo,StructureRepository $structureRepo ,AutoriteRepository $autoriteRepo ,HistoriqueEvenementRepository $historiqueEvenementRepo)
    {
        $this->userRepo = $userRepo;
        $this->evenementRepo = $evenementRepo;
        $this->periodiciteRepo = $periodiciteRepo;
        $this->structureRepo = $structureRepo;
        $this->historiqueEvenementRepo = $historiqueEvenementRepo;
        $this -> archive = $manager;
        $this->baseService=$baseService;
        

    }

    
    /**
     * @Post("/api/evenement", name="evenements")
     */
    public function addEvenement(EntityManagerInterface $manager, Request $request, MailerInterface $mailer ,ValidatorInterface $validator ,SerializerInterface $serializer): Response
    {

        $evenement = $serializer->deserialize($request->getContent(), evenement::class,'json');
        $errors = $validator->validate($evenement);
    if (count($errors) > 0)
    {
        $errorsString =$serializer->serialize($errors,"json");
        
        return new JsonResponse( $errorsString ,Response::HTTP_BAD_REQUEST,[],true);
    }
    

    $user = $this->getUser();
    $structure = $user->getStructure();
    $dateDebut = $evenement->getDateDebut();
    $mois= $evenement->getMois();
    $evenement->setThematique($request->request->get('thematique'));  
   // $evenement->addAutorite($request->request->get('autorite')); 

    $semaine= $this->baseService->Date2Semaine($dateDebut);
    $mois= $this->baseService->Date2mois($dateDebut);
    $evenement->setSemaine($semaine);
    $evenement->setMois($mois);
        $evenement->setUser($user);
        $evenement->setStructure($structure);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($evenement);
        $data = array(
            'to' => $user->getEmail(),
            'cc'=>array('mamekhady.diakhate@orange-sonatel.com','genvievesebiasylvie.mendy@orange-sonatel.com'),
            'subject' => 'Données de connexion à la plateforme Suivi des Activités et de la Roadmap',
            'body' => 'Bonjour '.$user->getPrenom().' '.$user->getNom().',
            <br><br>Merci d\'avoir renseigner la Roadmap '. '<br>'
        );
        $this->baseService->sendMail($data);
        $entityManager->flush();
        return $this->json($evenement, 200, [], ['groups' => 'evenement:detail']);
      
    }

    /**
     * @Get("/api/evenement", name="evenement")
     */
    public function listEvenement(): Response
    {
        
        $evenements = $this->evenementRepo->findAll();
        return $this->json($evenements, 200, [], ['groups' => 'evenement:read']);
    }
      /**
     * @Get("/api/evenement/{id}")
     * @QMLogger(message="Details evenement")
     */
    public function detailsEvenement($id){
        $evenements = $this->evenementRepo->find($id);

        return $this->json($evenements, 200, [], ['groups' => 'evenement:detail']);

    }
    /**
     * @Get("/api/rechercheevenement")
     * @QMLogger(message="Recherche evenement")
     */
    public function recherchErevenement(Request $request){
        $search=$request->query->get('structure');
        $search=$request->query->get('user');
        $search=$request->query->get('profil');
        return new JsonResponse($this->evenementManager->searchEvenement($search));
    }
     
    /**
     * @Get("/api/agenda/evenement/{semaine}", name="agenda-evenement")
     */
    public function AgendaEvenement($semaine): Response
    {
        #$evenementJson=file_get_contents("https://server/reportserver/ReportService2010.asmx?wsdl");
        $year = date("Y");
        $evenements = $this->evenementRepo->agenda($semaine, $year);
        return $this->json($evenements, 200, [], ['groups' => 'evenement:detail']);
    }
     /**
     * @Get("/api/mois/evenement/{mois}", name="agenda-evenement")
     */
    public function MoisEvenement($mois): Response
    {
        #$evenementJson=file_get_contents("https://server/reportserver/ReportService2010.asmx?wsdl");
    
        $year = date("Y");
        $evenements = $this->evenementRepo->Mois($mois, $year);
        return $this->json($evenements, 200, [], ['groups' => 'evenement:detail']);
    }

    /**
    * @Delete("/api/evenement/{id}", name="delete_evenement")
    */
    public function deleteEvenement(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $evenement = $entityManager->getRepository(Evenement::class)->find($id);
        $entityManager->remove($evenement);
        $user = $this->getUser();
        $data = array(
            'to' => $user->getEmail(),
            'cc'=>array('mamekhady.diakhate@orange-sonatel.com','genvievesebiasylvie.mendy@orange-sonatel.com'),
                        //'moussa.dieye@orange-sonatel.com','chantal.badiat@orange-sonatel.com'),
            'subject' => 'Données de connexion à la plateforme Suivi des Activités et de la Roadmap',
            'body' => 'Bonjour '.$user->getPrenom().' '.$user->getNom().',
            <br><br>Suppression de l\'événement de '.$user->getStructure()->getLibelle().' enregistré avec succée'. '<br>'
        );
        $this->baseService->sendMail($data);
        $entityManager->flush();

    return $this->redirectToRoute("evenements");
    }
     /**
     * @Put("/api/evenement/{id}")
     * @QMLogger(message="modifier evenement")
     */
    public function modifiEvenement($id, Evenement $evenement, Request $request)
    {
        $evenement = $this->evenementRepo->find($id);
        $user = $this->getUser();
        $structure = $user->getStructure();
        $dateDebut = $evenement->getDateeDebut();
        $dateFin= ($request->request->get('dateFin'));
        $semaine= $this->baseService->Date2Semaine($dateDebut);
        $evenement->setSemaine($semaine);
        $evenement->setDateDebut(new \DateTime($dateDebut)); 
        $evenement->setDateFin(new \DateTime($dateFin));
        $evenement->setThematique($request->request->get('thematique'));  
        $evenement->setSemaine($semaine);
            $evenement->setUser($user);
            $evenement->setStructure($structure);
    
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenement);
        $user = $this->getUser();
        $data = array(
            'from' =>'ddiatou1@gmail.com',
            'to' => $user->getEmail(),
            'cc'=>array('mamekhady.diakhate@orange-sonatel.com','genvievesebiasylvie.mendy@orange-sonatel.com',
                        'moussa.dieye@orange-sonatel.com','chantal.badiat@orange-sonatel.com'),
            'subject' => 'Données de connexion à la plateforme Suivi des Activités et de la Roadmap',
            'body' => 'Bonjour '.$user->getPrenom().' '.$user->getNom().',
            <br><br>Modification de l\'événement de '.$user->getStructure().' enregistré avec succée'. '<br>'
        );
        $this->baseService->sendMail($data);

        
        $entityManager->flush();

        return $this->json(['status'=>200, "message"=>"Evenement modifie avec succes"]);
    }

    /**
     * @Get("/api/evenement/archive/{id}")
     * @var EntityManagerInterface
     */
    public function __invoke(Evenement $data): Evenement
    {
         $data -> setArchives(1);
        //$this -> archive -> persist($update);
        $this -> archive -> flush();
        return $data;
    }
}