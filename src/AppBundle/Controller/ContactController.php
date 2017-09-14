<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactController extends Controller
{
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

        $atrributes = array('class' => 'form-control' , 'style' => 'margin-bottom:15px');

        $form = $this->createFormBuilder($contact)
            ->add('full_name', TextType::class, ['attr' => $atrributes])
            ->add('email', TextType::class, ['attr' => $atrributes])
            ->add('telephone_number', TextType::class, ['attr' => $atrributes])
            ->add('save', SubmitType::class, array('label' => 'Create contact', 'attr' => ['class' => 'btn btn-primary']))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $contact->setFullName($form['full_name']->getData());
            $contact->setEmail($form['email']->getData());
            $contact->setTelNum($form['telephone_number']->getData());

            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            $this->addFlash('notice', 'Contact added');

            return $this->redirectToRoute('contact_list');
        }

        return $this->render('contact/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     *
     */
    public function editAction(Request $request)
    {

    }
}
