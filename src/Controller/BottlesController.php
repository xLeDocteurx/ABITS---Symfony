<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;

use App\Entity\Bottles;
use App\Form\BottlesType;
use App\Repository\BottlesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @Route("/bottles")
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

        return $this->render('bottles/index.html.twig', ['bottles' => $bottlesRepository->findByAuthor($thisUser)]);
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
        
        $bottle = new Bottles();
        $bottle->setDate(new \DateTime('now'));
        $bottle->setAuthor($thisUser);
        $bottle->setSent(false);

        // $form = $this->createForm(BottlesType::class, $bottle);

        $form = $this->createFormBuilder($bottle)
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
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
        $form = $this->createForm(BottlesType::class, $bottle);
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
