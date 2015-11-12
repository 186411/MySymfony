<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\News;
use AppBundle\Form\NewsType;

/**
 * News controller.
 *
 * @Route("/news")
 */
class NewsController extends Controller
{

    /**
     * Lists all News entities.
     *
     * @Route("/", name="news")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:News')->createQueryBuilder("n");

        $pagetool = $this->get("knp_paginator");
        
        $pagination = $pagetool->paginate($entities, $request->query->getInt('page', 1));
       /* return $this->render('news/index.html.twig', [
            'pagination' => $pagination,
        ]);*/
       return array(
            'pagination' => $pagination,
           // 'entities' => $entities,
        );
    }

    /**
     * Creates a form to create a News entity.
     *
     * @param News $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(News $entity)
    {
        $form = $this->createForm(new NewsType(), $entity, array(
            'action' => $this->generateUrl('news_new'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
    * @Route("/new", methods={"POST", "GET"}, name="news_new")
    * @Template()
    */
   public function newAction(Request $request)
   {
       $entity = new News();
       $form = $this->createCreateForm($entity);
       $form->handleRequest($request);

       if ($form->isValid()) { // 注意如果不是POST请求，isValid方法会返回false
           $em = $this->getDoctrine()->getManager();
           $em->persist($entity);
           $em->flush();

           return $this->redirect($this->generateUrl('news_show', ['id' => $entity->getId()]));
       }
       return array(
           'entity' => $entity,
           'form'   => $form->createView()
        );
       /*return $this->render('news/new.html.twig', [
           'entity' => $entity,
           'form'   => $form->createView(),
       ]);*/
   }

    /**
     * Finds and displays a News entity.
     *
     * @Route("/{id}", name="news_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:News')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find News entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * @Route("/{id}/edit", methods={"GET", "PUT"}, name="news_edit")
     * @Template()
    */
   public function editAction(Request $request, $id)
   {
       $em = $this->getDoctrine()->getManager();

       $entity = $em->getRepository('AppBundle:News')->find($id);

       if (!$entity) {
           throw $this->createNotFoundException('Unable to find News entity.');
       }

       $editForm = $this->createEditForm($entity);
       $editForm->handleRequest($request);
       $deleteForm = $this->createDeleteForm($id); 
       if ($editForm->isValid()) {
           $em->flush();
           
           return $this->redirect($this->generateUrl('news_edit', ['id' => $id]));
       }
       return array(
           'entity'      => $entity,
           'edit_form'   => $editForm->createView(),
           'delete_form' => $deleteForm->createView(),
       );
       /*return $this->render('news/edit.html.twig', [
           'entity'      => $entity,
           'edit_form'   => $editForm->createView(),
       ]);*/
   }

    /**
    * Creates a form to edit a News entity.
    *
    * @param News $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(News $entity)
    {
        $form = $this->createForm(new NewsType(), $entity, array(
            'action' => $this->generateUrl('news_edit', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    
    /**
     * Deletes a News entity.
     *
     * @Route("/{id}", name="news_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:News')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find News entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('news'));
    }

    /**
     * Creates a form to delete a News entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('news_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
}
