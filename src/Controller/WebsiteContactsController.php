<?php

namespace App\Controller;

use App\Entity\Translation;
use App\Entity\User;
use App\Entity\WebsiteContacts;
use App\Form\TranslationType;
use App\Form\WebsiteContactsType;
use App\Repository\ProductRepository;
use App\Repository\TranslationRepository;
use App\Repository\UserRepository;
use App\Repository\WebsiteContactsRepository;
use App\Services\CheckIfUserService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/website/contacts")
 *
 */
class WebsiteContactsController extends AbstractController
{
    /**
     * @Route("/index", name="website_contacts_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     *
     */
    public function index(WebsiteContactsRepository $websiteContactsRepository, UserRepository $userRepository, CheckIfUserService $checkIfUser): Response
    {
        return $this->render('website_contacts/index.html.twig', [
            'website_contacts' => $websiteContactsRepository->findAll(),
            'Users' => $userRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, WebsiteContactsRepository $websiteContactsRepository): Response
    {
        $website_contact = new WebsiteContacts();
        $form = $this->createForm(WebsiteContactsType::class, $website_contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $websiteContactsRepository->add($website_contact, true);

            return $this->redirectToRoute('website_contacts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('website_contacts/new.html.twig', [
            'website_contact' => $website_contact,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/new_website_contact_from_contact_form', name: 'new_website_contact_from_contact_form', methods: ['GET', 'POST'])]
    public function newFromContact(Request $request, WebsiteContactsRepository $websiteContactsRepository, EntityManagerInterface $manager, ProductRepository $productRepository): Response
    {

        $websiteContact = new WebsiteContacts();
        $form = $this->createForm(WebsiteContactsType::class, $websiteContact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $websiteContact->setDateTime(new \DateTime('now'))
                ->setStatus('Pending');

            // Get selected products
            $selectedProducts = $form->get('products')->getData();
            foreach ($selectedProducts as $product) {
                $websiteContact->addProduct($product);
            }

            $manager->persist($websiteContact);
            $manager->flush();

            $this->addFlash('success', 'Your contact request has been submitted.');

            return $this->redirect($request->headers->get('Referer'));
        }

        // If validation fails, return back with errors
        return $this->render('app_home');
    }

    /**
     * @Route("/show/{id}", name="website_contacts_show", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(WebsiteContacts $websiteContact): Response
    {
        return $this->render('website_contacts/show.html.twig', [
            'website_contact' => $websiteContact,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="website_contacts_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, WebsiteContacts $websiteContact, WebsiteContactsRepository $websiteContactsRepository): Response
    {
        $form = $this->createForm(WebsiteContactsType::class, $websiteContact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $websiteContactsRepository->add($websiteContact, true);

            return $this->redirectToRoute('website_contacts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('website_contacts/edit.html.twig', [
            'website_contact' => $websiteContact,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/update_status/{new_status}/{id}", name="website_contacts_update_status", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function setToJunk(Request $request, string $new_status, WebsiteContacts $websiteContact, WebsiteContactsRepository $websiteContactsRepository, EntityManagerInterface $manager): Response
    {
        $referer = $request->headers->get('Referer');
        if ($new_status = "Junk") {
            $websiteContact->setStatus('Junk');
            $manager->flush($websiteContact);
        }
        if ($new_status = "Pending") {
            $websiteContact->setStatus('Pending');
            $manager->flush($websiteContact);
        }
        if ($new_status = "New User") {
            $websiteContact->setStatus('Accepted');
            $new_user = new User();

            $new_user->setEmail($websiteContact->getEmail())
                ->setFirstName($websiteContact->getFirstName())
                ->setLastName($websiteContact->getLastName())
                ->setMobile($websiteContact->getMobile())
                ->setPassword('password')
                ->setRoles(['ROLE_USER']);  // Pass roles as an array
            $manager->persist($new_user);
            $manager->flush($new_user);

            $manager->flush($websiteContact);
        }
        return $this->redirect($referer);
    }


    /**
     * @Route("/delete/{id}", name="website_contacts_delete", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public
    function delete(Request $request, WebsiteContacts $websiteContact, WebsiteContactsRepository $websiteContactsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $websiteContact->getId(), $request->request->get('_token'))) {
            $websiteContactsRepository->remove($websiteContact, true);
        }
        return $this->redirectToRoute('website_contacts_index', [], Response::HTTP_SEE_OTHER);
    }
}
