<?php

namespace MicroCMS\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use MicroCMS\Domain\Comment;
use MicroCMS\Form\Type\CommentType;

class HomeController {

    /**
     * Home page controller.
     *
     * @param Application $app Silex application
     */
    public function indexAction(Application $app) {
        $articles = $app['dao.article']->findAll();
        return $app['twig']->render('index.html.twig', array('articles' => $articles));
    }
    
    /**
     * Article details controller.
     *
     * @param integer $id Article id
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function articleAction($id, Request $request, Application $app) {
        $article = $app['dao.article']->find($id);
        $commentFormView = null;

        // Si requête ajax dans la requête on recherche le commentaire et met son champs isSignaled à true
        if ($request->isXmlHttpRequest() ) {
            $commentId = $request->request->get('commentId');
            $comment = $app['dao.comment']->find($commentId);
            $comment->setIsSignaled(TRUE);
            $app['dao.comment']->save($comment);
            return '';
        }

        if ($app['security.authorization_checker']->isGranted('IS_AUTHENTICATED_FULLY')) {
            // A user is fully authenticated : he can add comments
            $comment = new Comment();
            $comment->setArticle($article);
            $user = $app['user'];
            $comment->setAuthor($user);
            $commentForm = $app['form.factory']->create(CommentType::class, $comment);
            $commentForm->handleRequest($request);

            // Si Request a un paramètre parentId envoyé par POST alors on redéfinit comment->parent_id
            if ($request->isMethod('POST') && $request->request->get('parentId')) {
                $comment->setContent($request->request->get('content'));
                $comment->setParentId($request->request->get('parentId'));
                $app['dao.comment']->save($comment);
                $app['session']->getFlashBag()->add('success', 'Your comment was successfully added.');
            }

            if ($commentForm->isSubmitted() && $commentForm->isValid()) {
                $app['dao.comment']->save($comment);
                $app['session']->getFlashBag()->add('success', 'Your comment was successfully added.');
                // On redéfinit $comment et $commentForm pour éviter de repasser le commentaire à la page après avoir été sauvegardé
                $comment = new Comment();
                $comment->setArticle($article);
                $user = $app['user'];
                $comment->setAuthor($user);
                $commentForm = $app['form.factory']->create(CommentType::class, $comment);
            } 
            $commentFormView = $commentForm->createView();
        }
        $comments = $app['dao.comment']->findAllByArticle($id);
        
        return $app['twig']->render('article.html.twig', array(
            'article' => $article,
            'comments' => $comments,
            'commentForm' => $commentFormView));
    }
    
    /**
     * User login controller.
     *
     * @param Request $request Incoming request
     * @param Application $app Silex application
     */
    public function loginAction(Request $request, Application $app) {
        return $app['twig']->render('login.html.twig', array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ));
    }
}

