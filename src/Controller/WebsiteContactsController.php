<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\WebsiteContacts;
use App\Form\WebsiteContactsType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\WebsiteContactsRepository;
use App\Services\CheckIfUserService;
use App\Services\CompanyDetailsService;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;

/**
 * @Route("/website/contacts")
 *
 */
class WebsiteContactsController extends AbstractController
{
    private MailerInterface $mailer;

    // Inject the MailerInterface into your controller
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

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
    public function new(Request $request, WebsiteContactsRepository $websiteContactsRepository, EntityManagerInterface $entityManager): Response
    {
        $now = new \DateTime('now');
        $website_contact = new WebsiteContacts();
        $website_contact->setDateTime($now);
        $form = $this->createForm(WebsiteContactsType::class, $website_contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $website_contact->setDateTime(new \DateTime('now'))
                ->setStatus('Pending');
            $entityManager->persist($website_contact);
            $entityManager->flush();
            $this->addFlash('success', 'Your contact request has been submitted.');
            return $this->redirectToRoute('website_contacts_index', [
                'website_contact' => $website_contact,
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('website_contacts/new.html.twig', [
            'website_contact' => $website_contact,
            'form' => $form->createView(),
        ]);
    }


    #[NoReturn] #[Route('/new_website_contact_from_contact_form', name: 'new_website_contact_from_contact_form', methods: ['GET', 'POST'])]
    public function newFromContact(Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository, WebsiteContactsRepository $websiteContactsRepository): Response
    {
        $now = new \DateTime('now');
        $website_contact = new WebsiteContacts();
        $form = $this->createForm(WebsiteContactsType::class, $website_contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $website_contact->setDateTime(new \DateTime('now'))
                ->setStatus('Pending');
            $entityManager->persist($website_contact);
            $entityManager->flush();
            $this->addFlash('success', 'Your contact request has been submitted.');
            return $this->redirectToRoute('app_home');
        }
            return $this->redirectToRoute('app_home');
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
    public function updateWebsiteContact(Request $request, string $new_status, WebsiteContacts $websiteContact, WebsiteContactsRepository $websiteContactsRepository, EntityManagerInterface $manager, CompanyDetailsService $companyDetailsService): Response

    {
        $referer = $request->headers->get('Referer');
        $company_email = $companyDetailsService->getCompanyDetails()->getCompanyEmail();
        $company_name = $companyDetailsService->getCompanyDetails()->getCompanyName();
        $products_request = $websiteContact->getProductsRequested();

        // Proper comparison with '=='
        if ($new_status == "Junk") {
            $websiteContact->setStatus('Junk');
            $manager->flush();
        }

        if ($new_status == "Pending") {
            $websiteContact->setStatus('Pending');
            $manager->flush();
        }

        if ($new_status == "New User") {
            $websiteContact->setStatus('Accepted');
            $new_user = new User();

            $new_user->setEmail($websiteContact->getEmail())
                ->setFirstName($websiteContact->getFirstName())
                ->setLastName($websiteContact->getLastName())
                ->setMobile($websiteContact->getMobile())
                ->setPassword('password')  // Make sure to set a secure password
                ->setRoles(['ROLE_USER']);

            // Sending the email
            $html = $this->renderView('website_contacts/welcome_new_website_contacts_email.html.twig', [
                'contact' => $websiteContact,
                'products_requested' => $products_request,
                'company_name' => $company_name
            ]);

            $email = (new Email())
                ->from($company_email)
                ->to($new_user->getEmail())
                ->bcc('nurse_stephen@hotmail.com')  // You may want to replace this with an actual admin email
                ->subject("Welcome to " . $company_name)
                ->html($html);  // Sending the rendered HTML email content
            $this->mailer->send($email);  // Send the email

            $manager->persist($new_user);
            $manager->flush();
        }

        // Always flush websiteContact in the end, regardless of the new_status
        $manager->flush();

        return $this->redirect($referer); // Redirect back to the referring page
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
