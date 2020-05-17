<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends Controller
{
    /**
     * @Route("/contact", name="contact_list")
     * @return Response
     */
    public function indexAction()
    {
        $contactsRepository = $this->getDoctrine()->getRepository(Contact::class);
        $contacts = $contactsRepository->findAll();

        return $this->render('contacts/index.html.twig', ['contacts' => $contacts]);
    }

    /**
     * @Route("/contact/create", name="create_contact")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->_generatedForm($contact);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $contact = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirect('/contact');
        }

        return $this->render('contacts/edit.html.twig', [
            'form' => $form->createView()
        ]);

    }

    /**
     * @Route("/delete-contact/{id}", name="delete_contact")
     * @param $id
     * @return RedirectResponse
     */
    public function deleteAction($id)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $contact = $entityManager->getRepository(Contact::class)->find($id);

        if (!$contact) {
            throw $this->createNotFoundException(
                'There is no contact with id: ' . $id
            );
        }

        $entityManager->remove($contact);
        $entityManager->flush();

        return $this->redirect('/contact');

    }

    /**
     * @Route("/update-contact/{id}", name="edit_contact")
     * @param Request $request
     * @param $id
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request, $id)
    {

        $entityManger = $this->getDoctrine()->getManager();
        $contact = $entityManger->getRepository(Contact::class)->find($id);

        if (!$contact) {
            throw $this->createNotFoundException(
                'There is no contact with id: ' . $id
            );
        }

        $form = $this->_generatedForm($contact);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $contact = $form->getData();
            $entityManger->flush();

            return $this->redirect('/contact');

        }

        return $this->render(
            'contacts/edit.html.twig',
            ['form' => $form->createView()]
        );

    }

    private function _generatedForm($contact)
    {
        return $this->createFormBuilder($contact)
            ->add('first_name', TextType::class)
            ->add('last_name', TextType::class)
            ->add('street_number', TextType::class)
            ->add('city', TextType::class)
            ->add('country', TextType::class)
            ->add('zip', TextType::class)
            ->add('email', TextType::class)
            ->add('phone_number', TextType::class)
            ->add('birthdate', BirthdayType::class)
            ->add('picture', FileType::class, ['required' => false])
            ->add('save', SubmitType::class, ['label' => 'Add new contact'])
            ->getForm();
    }
}
