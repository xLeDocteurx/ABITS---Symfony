<?php

namespace App\Controller;
// use Symfony\Component\Security\Core\User\UserInterface;

use App\Entity\Users;
use App\Form\UsersType;
use App\Repository\UsersRepository;

use App\Entity\Bottles;
use App\Form\BottlesType;
use App\Repository\BottlesRepository;
use App\Entity\BottlesSent;
use App\Form\BottlesSentType;
use App\Repository\BottlesSentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @Route("/bottles")
 * @Route("/")
 */
class BottlesController extends AbstractController
{
    /**
     * @Route("/", name="bottles_index", methods="GET")
     */
    public function index(BottlesRepository $bottlesRepository): Response
    {
        // $username = $_SESSION['auth']['username'];
        $thisUser = $this->getUser();

        $user_bottles = $bottlesRepository->findBy(
            array('author' => $thisUser),
            array('id' => 'DESC')
        );
        // $user_bottles = $thisUser->getBottles();

        return $this->render('bottles/index.html.twig', ['bottles' => $user_bottles]);
    }
    
    /**
     * @Route("/all", name="bottles_index_all", methods="GET")
     */
    public function all(BottlesRepository $bottlesRepository): Response
    {
        $all_bottles = $bottlesRepository->findAll();
        // $user_bottles = $thisUser->getBottles();

        return $this->render('bottles/index.html.twig', ['bottles' => $all_bottles]);
    }
    
    /**
     * @Route("/sent", name="bottles_sent_index_bis", methods="GET")
     */
    public function sent()
    {
        return $this->redirectToRoute('bottles_sent_index');
    }

    /**
     * @Route("/send", name="bottles_send", methods="GET|POST")
     */
    public function send(Request $request, BottlesRepository $bottlesRepository, UsersRepository $usersRepository): Response
    {
        
        $bottle = $bottlesRepository->findOneById($_POST['bottleId']);
        $bottle->setSent(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($bottle);
        $em->flush();

        $receiversLimit = 3;
        $allUsers = $usersRepository->findAll();
        $allUsers_Length = sizeof($allUsers);
        $chosenUsers = [];

        $bottlesSent = new BottlesSent();
        $bottlesSent->setBottle($bottle);
        $bottlesSent->setReceived(false);

        $em->persist($bottlesSent);
        $em->flush();

        return $this->redirectToRoute('bottles_index');
    }

    /**
     * @Route("/found", name="bottles_found", methods="GET|POST")
     */
    public function found(Request $request) {

        $thisUser = $this->getUser();

        $bottlesFound = $thisUser->getBottlesSents(           
            array('id' => 'DESC')
        );

        return $this->render('bottles/found.html.twig', [
            'bottles' => $bottlesFound
        ]);
    }

    /**
     * @Route("/beach", name="bottles_beach", methods="GET|POST")
     */
    public function beach(Request $request, BottlesRepository $bottlesRepository, BottlesSentRepository $bottlesSentRepository): Response
    {

        $user = $this->getUser();
        $bottles_at_the_beach = array_reverse($bottlesRepository->findBySent(true));
                
        $all_bottles = array_reverse($bottlesSentRepository->findBy([
            'received' => false
        ]));

        return $this->render('bottles/beach.html.twig', [
            'bottles' => $all_bottles,
            'user' => $user
        ]);
    }

    /**
     * @Route("/find", name="bottles_find", methods="GET|POST")
     */
    public function find(Request $request, 
    // UserInterface $user, 
    BottlesRepository $bottlesRepository, BottlesSentRepository $bottlesSentRepository): Response
    {
        $maxReceivers = 3;
        $user = $this->getUser();
        $userId = $user->getId();
        
        // Nous récupérons toutes les bouteilles ayant l'attribut "received" à false
        $bottles_at_the_beach = $bottlesSentRepository->findBy([
            'received' => false
        ]);
        
        // filtrer les bouteilles pour exclure celles ayant étées envoyées par notre  utilisateur
        array_filter($bottles_at_the_beach, function ($bottle) {
            $bottleAuthorId = $bottle->getBottle()->getAuthor()->getId();
            return $bottleAuthorId != $this->getUser()->getId();
        });
        
        // Filtrer les bouteilles pour exclure celles ayant déja étées reçues par notre utilisateur
        array_filter($bottles_at_the_beach, function ($bottle) {
            $bottleReceivers = $bottle->getReceivers();
            $receiversId = [];
            foreach ($bottleReceivers as $key => $receiver) {
                array_push($receiversId, $receiver->getId());
            }
            return !in_array($this->getUser()->getId(), $receiversId);
        });



        file_put_contents('./errorLogs.txt', 'Debug Objects: sizeof($bottles_at_the_beach) ' . sizeof($bottles_at_the_beach) . ' // ');
        if (sizeof($bottles_at_the_beach) > 0) {
            

            $bottle = $bottles_at_the_beach[ rand(
                0,
                sizeof($bottles_at_the_beach) - 1
            ) ];

            // Nous ajoutons l'utilisateur courant à la liste des "receivers" de la bouteille
            $bottle->addReceiver($user);
            // Si le nombre de "receivers" maximum est atteint nous faisons passer l'attribut "received" à true
            if (sizeof($bottle->getReceivers()) >= $maxReceivers) {
                $bottle->setReceived(true);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($bottle);
            $em->flush();

            return $this->redirectToRoute('bottles_show', ['id' => $bottle-getBottle()->getId()]);
        } else {
            return $this->redirectToRoute('bottles_beach');
        }
    }

    /**
     * @Route("/new", name="bottles_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        
        // $username = $_SESSION['auth']['username'];
        $thisUser = $this->getUser();
        
        $bottle = new Bottles();
        $bottle->setDate(new \DateTime('now'));
        $bottle->setAuthor($thisUser);
        $bottle->setSent(false);

        // $form = $this->createForm(BottlesType::class, $bottle);

        $form = $this->createFormBuilder($bottle)
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            // ->add('sent',ChoiceType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($bottle);
            $em->flush();

            return $this->redirectToRoute('bottles_index');
        }

        return $this->render('bottles/new.html.twig', [
            'bottle' => $bottle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bottles_show", methods="GET")
     */
    public function show(Bottles $bottle): Response
    {
        return $this->render('bottles/show.html.twig', ['bottle' => $bottle]);
    }

    /**
     * @Route("/{id}/edit", name="bottles_edit", methods="GET|POST")
     */
    public function edit(Request $request, Bottles $bottle): Response
    {
        $form = $this->createFormBuilder($bottle)
            ->add('sent', ChoiceType::class)
            ->add('title')
            ->add('content')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bottles_edit', ['id' => $bottle->getId()]);
        }

        return $this->render('bottles/edit.html.twig', [
            'bottle' => $bottle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bottles_delete", methods="DELETE")
     */
    public function delete(Request $request, Bottles $bottle): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bottle->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($bottle);
            $em->flush();
        }

        return $this->redirectToRoute('bottles_index');
    }
}
