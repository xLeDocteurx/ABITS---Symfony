<?php

namespace App\Controller;

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
        while (sizeof($chosenUsers) < $receiversLimit) {

            $randomId  = rand(1, $allUsers_Length);
            $randomReceiver = $usersRepository->findOneById($randomId);

            if ( !in_array($randomReceiver, $chosenUsers) && !in_array($this->getUser(), $chosenUsers) ) {
                array_push($chosenUsers, $randomReceiver);
            }
        }
        foreach ( $chosenUsers as $user ) {
            $bottlesSent->addReceiver($user);
        }
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

        // $username = $_SESSION['auth']['username'];

        $bottles_at_the_beach = array_reverse($bottlesRepository->findBySent(true));

        return $this->render('bottles/beach.html.twig', [
            'bottles' => $bottles_at_the_beach,
        ]);
    }

    /**
     * @Route("/find", name="bottles_find", methods="GET|POST")
     */
    public function find(Request $request, BottlesRepository $bottlesRepository, BottlesSentRepository $bottlesSentRepository): Response
    {

        // $username = $_SESSION['auth']['username'];
        $thisUser = $this->getUser();
        
        $bottles_at_the_beach = $bottlesSentRepository->findBy(
            array('received' => '0'),           
            array('id' => 'DESC')
        
            // 'bottlesSent' => 'null',
            // 'bottle' => $thisUser->getUsername(),
        );


        return $this->render('bottles/find.html.twig', [
            'bottles' => $bottles_at_the_beach,
        ]);
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
