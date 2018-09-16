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
        $thisUser = $this->getDoctrine()
        ->getRepository(Users::class)
        ->findOneByUsername('LeDocteur');

        $user_bottles = $bottlesRepository->findBy(
            array('author' => $thisUser),
            array('id' => 'DESC')
        );
        // $user_bottles = $thisUser->getBottles();

        return $this->render('bottles/index.html.twig', ['bottles' => $user_bottles]);
    }
    
    /**
     * @Route("/send", name="bottles_send", methods="GET|POST")
     */
    public function send(Request $request, BottlesRepository $bottlesRepository): Response
    {
        
        $bottle = $this->getDoctrine()
        ->getRepository(Bottles::class)
        ->findOneById($_POST['bottleId']);
        $bottle->setSent(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($bottle);
        $em->flush();

        // $bottlesSent = new BottlesSent();
        // $bottlesSent->setBottle($bottle);
        // $bottlesSent->setReceived(0);

        // $em->persist($bottlesSent);
        // $em->flush();
            

        return $this->redirectToRoute('bottles_index');
    }

    /**
     * @Route("/found", name="bottles_found", methods="GET|POST")
     */
    public function found(Request $request) {

        $thisUser = $this->getDoctrine()
        ->getRepository(Users::class)
        ->findOneByUsername('LeDocteur');

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
        $thisUser = $this->getDoctrine()
        ->getRepository(Users::class)
        ->findOneByUsername('LeDocteur');
        
        // $bottles_at_the_beach = $thisUser->getBottlesSents();

        // $bottles_at_the_beach = $bottlesRepository->findByBottlesSent(true);
        // $bottlesSent_at_the_beach = $bottlesSentRepository->findAll();
        // $thisBottles = [];
        // foreach ($bottle as $bottles_at_the_beach) {
        //     if ($bottle->getAuthor() == $thisUser) {
        //         array_push($thisBottles, $bottle);
        //     }
        // }
        // $bottles_at_the_beach = $bottlesRepository->findBySent(true);
        // $bottles_at_the_beach = $bottlesRepository->findBy([
        
            // $bottles_at_the_beach = $bottlesRepository->findBy([
        $bottles_at_the_beach = $bottlesSentRepository->findBy(
            array('received' => '0'),           
            array('id' => 'DESC')
        
            // 'bottlesSent' => 'null',
            // 'bottle' => $thisUser->getUsername(),
        );


        return $this->render('bottles/beach.html.twig', [
            'bottles' => $bottles_at_the_beach,
        ]);
    }

    /**
     * @Route("/new", name="bottles_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        
        // $username = $_SESSION['auth']['username'];
        $thisUser = $this->getDoctrine()
        ->getRepository(Users::class)
        ->findOneByUsername('LeDocteur');
        
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
