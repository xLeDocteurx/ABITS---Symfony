<?php

namespace App\Controller;

use App\Entity\Users;
use App\Repository\UsersRepository;
use App\Entity\Comments;
use App\Repository\CommentsRepository;

use App\Entity\Posts;
use App\Form\PostsType;
use App\Repository\PostsRepository;
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
 * @Route("/posts")
 * @Route("/")
 */
class PostsController extends AbstractController
{
    /**
     * @Route("/", name="posts_index", methods="GET")
     */
    public function index(PostsRepository $postsRepository): Response
    {        

        // $postsIWant = $bottlesSentRepository->findById($thisUser->getId());

        // $postsIWant = $bottlesSentRepository->findBy(
        //     array('receivers' => $thisUser) // Critere
        //     // array('date' => 'desc'),        // Tri
        //     // 5,                              // Limite
        //     // 0                               // Offset
        //   );
        
        $postsIWant = array_reverse($postsRepository->findAll());
        return $this->render('posts/index.html.twig', ['posts' => $postsIWant]);
    }

    /**
     * @Route("/new/", name="posts_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        // $username = $_SESSION['auth']['username'];
        $author = $this->getDoctrine()
        ->getRepository(Users::class)
        ->findOneByUsername('LeDocteur');
        // $author = new Users;
        // $author->setUsername('LeDocteur');

        $post = new Posts();
        $post->setDate(new \DateTime('now'));
        $post->setAuthor($author);

        // $form = $this->createForm(PostsType::class, $post);

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('posts_index');
        }

        return $this->render('posts/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'author' => $author,
        ]);
    }

    /**
     * @Route("/{id}", name="posts_show", methods="GET|POST")
     */
    public function show(Request $request, Posts $post): Response
    {

        // $username = $_SESSION['auth']['username'];
        $thisUser = $this->getDoctrine()
        ->getRepository(Users::class)
        ->findOneByUsername('LeDocteur');


        $comment = new Comments();
        $comment->setDate(new \DateTime('now'));
        $comment->setAuthor($thisUser);
        $comment->setPost($post);

        // $form = $this->createForm(CommentsType::class, $comment);

        $form = $this->createFormBuilder($comment)
            ->add('content', TextareaType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('posts_show', [
                'id' => $post->getId(),
            ]);
        }

        return $this->render('posts/show.html.twig', [
            'post' => $post,
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="posts_edit", methods="GET|POST")
     */
    public function edit(Request $request, Posts $post): Response
    {
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('posts_edit', ['id' => $post->getId()]);
        }

        return $this->render('posts/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="posts_delete", methods="DELETE")
     */
    public function delete(Request $request, Posts $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('posts_index');
    }
}
