<?php

namespace Sff\LiensBundle\Controller;

use Sff\LiensBundle\Entity\Categories;
use Sff\LiensBundle\Form\CategoriesType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoriesController extends Controller
{
    public function nouveauAction() {
        $this->denyAccessUnlessGranted('ROLE_MODERATEUR', null, 'Vous n\'avez pas les droits nécessaires');
        $categorie = new Categories();

        $form = $this->createForm(new CategoriesType(), $categorie);

        $request = $this->get('request');
        if($request->getMethod() == 'POST') {
            $form->bind($request);

            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($categorie);
                $em->flush();

                return $this->redirect($this->generateUrl('admin_liens_categories_nouveau'));
            }
        }

        return $this->render('SffLiensBundle:Categories:formulaire.html.twig', array( 'form' => $form->createView()));
    }

    public function modifierAction(Categories $categories)
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATEUR', null, 'Vous n\'avez pas les droits nécessaires');
        $form = $this->createForm(new CategoriesType, $categories, array('attr' => array('class' => 'false')));

        $request = $this->get('request');
        if($request->getMethod() == 'POST') {
            $form->bind($request);

            if($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($categories);
                $em->flush();

                return $this->redirect($this->generateUrl('admin_liens_liste'));
            }
        }

        return $this->render('SffLiensBundle:Categories:formulaire.html.twig', array( 'form' => $form->createView(), 'categorie' => $categories));
    }

    public function supprimerAction(Request $request, Categories $categories)
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATEUR', null, 'Vous n\'avez pas les droits nécessaires');
        if(!$request->isXmlHttpRequest()) {
            throw new \Exception('Cette resource n\'est pas accessible');
        }
        return $this->render('SffLiensBundle:Categories:supprimer.html.twig', array('categorie' => $categories));
    }
    public function supprimerOkAction(Categories $categories) {
        $this->denyAccessUnlessGranted('ROLE_MODERATEUR', null, 'Vous n\'avez pas les droits nécessaires');
        $em= $this->getDoctrine()->getManager();
        $repository = $em->getRepository('SffLiensBundle:Liens');
        $liens = $repository->findByCategorie($categories);
        foreach($liens as $lien) {
            $em->remove($lien);
        }
        $em->remove($categories);
        $em->flush();
        return $this->redirect($this->generateUrl('admin_liens_liste'));
    }

    public function categoriesBlocAction()
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATEUR', null, 'Vous n\'avez pas les droits nécessaires');
        $repo = $this->getDoctrine()
            ->getManager()->getRepository('SffLiensBundle:Categories');
        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul class="Tree">',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) {
                return '<a href="'.$this->generateUrl('admin_liens_liste', array('id'=> $node['id'])).'">'.$node['titre'].'</a>
                <a class="ShowLoading" href="'.$this->generateUrl('admin_liens_categories_modifier', array('id'=> $node['id'])).'"><span style="margin:0; padding:0;" class="fa fa-pencil"></span></a>
                <a class="ShowLoading" href="'.$this->generateUrl('admin_liens_categories_move', array('id'=> $node['id'],'sens' => 'up')).'"><span style="margin:0; padding:0;" class="fa fa-caret-up"></span></a>
                <a class="ShowLoading" href="'.$this->generateUrl('admin_liens_categories_move', array('id'=> $node['id'], 'sens' => 'down')).'"><span style="margin:0; padding:0;" class="fa fa-caret-down"></span></a>';
            }
        );
        $htmlTree = $repo->childrenHierarchy(
            null, /* starting from root nodes */
            false, /* true: load all children, false: only direct */
            $options
        );
        return $this->render('SffLiensBundle:Categories:bloc.html.twig', array('htmlTree' => $htmlTree));
    }
    public function moveAction(Categories $categories, $sens)
    {
        $this->denyAccessUnlessGranted('ROLE_MODERATEUR', null, 'Vous n\'avez pas les droits nécessaires');
        $em= $this->getDoctrine()->getManager();
        $repo = $em->getRepository('SffLiensBundle:Categories');
        if($sens == 'up') {
            $repo->moveUp($categories, 1);
        } else {
            $repo->moveDown($categories, 1);
        }
        $em->flush();
        return $this->redirect($this->generateUrl('admin_liens_liste'));
    }
};