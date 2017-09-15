<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactController extends Controller
{

    private $formAttributes = array('class' => 'form-control' , 'style' => 'margin-bottom:15px');

    /**
     * @Route("/", name="contact_list")
     */
    public function indexAction()
    {
        $contacts = $this->getDoctrine()
            ->getRepository('AppBundle:Contact')
            ->findAll();

        return $this->render('contact/index.html.twig', [
            'contacts' => $contacts
        ]);
    }

    /**
     * @Route("/create", name="contact_create")
     */
    public function createAction(Request $request)
    {
        $contact = new \AppBundle\Entity\Contact();

        $form = $this->createFormBuilder($contact)
            ->add('full_name', TextType::class, ['attr' => $this->formAttributes])
            ->add('email', EmailType::class, ['attr' => $this->formAttributes])
            ->add('telephone_number', TextType::class, ['attr' => $this->formAttributes])
            ->add('save', SubmitType::class, array('label' => 'Create contact', 'attr' => ['class' => 'btn btn-primary']))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $contact->setFullName($form['full_name']->getData());
            $contact->setEmail($form['email']->getData());
            $contact->setTelephoneNumber($form['telephone_number']->getData());

            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            $this->addFlash('success', 'New contact added');

            return $this->redirectToRoute('contact_list');
        }

        return $this->render('contact/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="contact_edit")
     */
    public function editAction($id, Request $request)
    {
        $contact = $this->getDoctrine()
            ->getRepository('AppBundle:Contact')
            ->find($id);


        if (empty($contact))
        {
            $this->addFlash('danger', 'Contact not found');
            return $this->redirectToRoute('contact_list');
        }

        $this->formAttributes = array(
            'class' => 'form-control' ,
            'style' => 'margin-bottom:15px'
        );

        $form = $this->createFormBuilder($contact)
            ->add('full_name', TextType::class, ['attr' => $this->formAttributes])
            ->add('email', EmailType::class, ['attr' => $this->formAttributes])
            ->add('telephone_number', TextType::class, ['attr' => $this->formAttributes])
            ->add('save', SubmitType::class, array('label' => 'Save changes', 'attr' => ['class' => 'btn btn-primary']))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $contact->setFullName($form['full_name']->getData());
            $contact->setEmail($form['email']->getData());
            $contact->setTelephoneNumber($form['telephone_number']->getData());

            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            $this->addFlash('success', 'Contact updated');

            return $this->redirectToRoute('contact_list');
        }

        return $this->render('contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="contact_view")
     */
    public function showAction($id)
    {
        $contact = $this->getDoctrine()
            ->getRepository('AppBundle:Contact')
            ->find($id);


        if (empty($contact))
        {
            $this->addFlash('danger', 'Contact not found');
            return $this->redirectToRoute('contact_list');
        }

        return $this->render('contact/view.html.twig', [
            'contact' => $contact
        ]);
    }


    /**
     * @Route("/delete/{id}", name="contact_delete")
     */
    public function deleteAction($id, Request $request)
    {
        $contact = $this->getDoctrine()
            ->getRepository('AppBundle:Contact')
            ->find($id);

        if (empty($contact))
        {
            $this->addFlash('danger', 'Contact not found');
            return $this->redirectToRoute('contact_list');
        }

        $form = $this->createFormBuilder($contact)
            ->add('delete', SubmitType::class, array('label' => 'Yes', 'attr' => ['class' => 'btn btn-danger', 'style' => 'float:left;margin-right:15px']))
            ->add('cancel', SubmitType::class, array('label' => 'No', 'attr' => ['class' => 'btn btn-secondary']))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if (!$form->get('delete')->isClicked())
            {
                return $this->redirectToRoute('contact_list');
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($contact);
            $em->flush();

            $this->addFlash('success', 'Contact deleted');

            return $this->redirectToRoute('contact_list');
        }

        return $this->render('contact/delete.html.twig', [
            'form' => $form->createView(),
            'contact' => $contact
        ]);
    }
}
