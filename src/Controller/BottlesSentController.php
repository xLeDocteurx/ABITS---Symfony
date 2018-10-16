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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
 * @Route("/bottles/sent")
 */
class BottlesSentController extends Controller
{
    /**
     * @Route("/", name="bottles_sent_index", methods="GET")
     */
    public function index(BottlesSentRepository $bottlesSentRepository): Response
    {

        // $username = $_SESSION['auth']['username'];
        $thisUser = $this->getUser();
        
        $user_bottles = $thisUser->getBottlesSents();

        // $user_bottles = $bottlesSentRepository->findAll();
        // $user_bottles = $bottlesSentRepository->findById($thisUser->getId());
        // $user_bottles = $bottlesSentRepository->findByReceivers($thisUser->getId());
        // $user_bottles = $bottlesSentRepository->findByReceivers($thisUser->getId());

        // $user_bottles = $bottlesSentRepository->findBy(
        //     array('receivers' => $thisUser) // Critere
        //     // array('date' => 'desc'),        // Tri
        //     // 5,                              // Limite
        //     // 0                               // Offset
        //   );

        return $this->render('bottles/index_sent.html.twig', ['bottles' => $user_bottles]);
    }

    /**
     * @Route("/all", name="bottles_sent_all", methods="GET")
     */
    public function all(BottlesSentRepository $bottlesSentRepository): Response
    {
        $all_bottles = $bottlesSentRepository->findAll();
        // return $this->render('bottles_sent/index.html.twig', ['bottles_sents' => $all_bottles]);
        return $this->render('bottles/index_sent.html.twig', ['bottles' => $all_bottles]);
    }

    /**
     * @Route("/new", name="bottles_sent_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {

        // $numberOfReceivers = 4;

        // $usersCount = count(
        //     $this->getDoctrine()
        //     ->getRepository(Users::class)
        //     ->findAll()
        // );
        // for ($i = 0; $i < $numberOfReceivers;$i++) {
        //     $bottlesSent = new BottlesSent();
        //     $bottlesSent->setBottle($bottle);
        //     $randomId = rand(1,$usersCount);
        //     $randomUser = $this->getDoctrine()
        //     ->getRepository(Users::class)
        //     ->findOneById($randomId);
        //     $bottlesSent->addReceiver($randomUser);
        //     $em = $this->getDoctrine()->getManager();
        //     $em->persist($bottlesSent);
        //     $em->flush();
        // }

        // $bottle->setSent(true);

        // return $this->redirectToRoute('bottles_sent_index');
        
        $bottlesSent = new BottlesSent();
        // $bottlesSent->setBottle($bottle);

        // $form = $this->createForm(BottlesSentType::class, $bottlesSent);



        $form = $this->createFormBuilder($bottlesSent)
            ->add('bottle', BottlesType::class, array('data_class' => null))
            ->add('receivers', UsersType::class, array('data_class' => null))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($bottlesSent);
            $em->flush();

            return $this->redirectToRoute('bottles_sent_index');
        }

        return $this->render('bottles_sent/new.html.twig', [
            'bottles_sent' => $bottlesSent,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/{id}", name="bottles_sent_show", methods="GET")
     */
    public function show(BottlesSent $bottlesSent): Response
    {
        return $this->render('bottles_sent/show.html.twig', ['bottles_sent' => $bottlesSent]);
    }

    /**
     * @Route("/{id}/edit", name="bottles_sent_edit", methods="GET|POST")
     */
    public function edit(Request $request, BottlesSent $bottlesSent): Response
    {
        $form = $this->createForm(BottlesSentType::class, $bottlesSent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('bottles_sent_edit', ['id' => $bottlesSent->getId()]);
        }

        return $this->render('bottles_sent/edit.html.twig', [
            'bottles_sent' => $bottlesSent,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="bottles_sent_delete", methods="DELETE")
     */
    public function delete(Request $request, BottlesSent $bottlesSent): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bottlesSent->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($bottlesSent);
            $em->flush();
        }

        return $this->redirectToRoute('bottles_sent_index');
    }
}
