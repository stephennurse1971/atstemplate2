<?php

namespace App\Controller;

use App\Entity\WebsiteContacts;
use App\Form\WebsiteContactsType;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\WebsiteContactsRepository;
use App\Services\CheckIfUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/website/contacts")
 */
class WebsiteContactsController extends AbstractController
{
    /**
     * @Route("/index", name="website_contacts_index", methods={"GET"})
     */
    public function index(WebsiteContactsRepository $websiteContactsRepository, UserRepository $userRepository, CheckIfUserService $checkIfUser): Response
    {
        return $this->render('website_contacts/index.html.twig', [
            'website_contacts' => $websiteContactsRepository->findAll(),
            'users' => $userRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="website_contacts_new", methods={"GET", "POST"})
     */
    public function new(Request $request, WebsiteContactsRepository $websiteContactsRepository, EntityManagerInterface $manager, ProductRepository $productRepository): Response
    {
        $now = new \DateTime('now');
        $first_name = $_POST['firstName'];
        $last_name = $_POST['lastName'];
        $email = $_POST['email'];
        $mobile = $_POST['mobile'];
        $notes = $_POST['notes'];
        $service = $_POST['service'];
        $serviceId = $productRepository->findOneBy([
            'product' => $service
        ]);
        $website_contact = new WebsiteContacts();
        $website_contact->setProduct($serviceId)
            ->setDateTime($now)
            ->setFirstName($first_name)
            ->setLastName($last_name)
            ->setEmail($email)
            ->setMobile($mobile)
            ->setStatus('Pending')
            ->setNotes($notes);
        $manager->persist($website_contact);
        $manager->flush();

        return $this->redirect($request->headers->get('Referer'));
    }

    /**
     * @Route("/show/{id}", name="website_contacts_show", methods={"GET"})
     */
    public function show(WebsiteContacts $websiteContact): Response
    {
        return $this->render('website_contacts/show.html.twig', [
            'website_contact' => $websiteContact,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="website_contacts_edit", methods={"GET", "POST"})
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
     */
    public function setToJunk(Request $request, string $new_status,  WebsiteContacts $websiteContact, WebsiteContactsRepository $websiteContactsRepository, EntityManagerInterface $manager): Response
    {
        $referer = $request->headers->get('Referer');
        if($new_status = "Junk"){
            $websiteContact->setStatus('Junk');
            $manager->flush($websiteContact);
        }
        if($new_status = "Pending"){
            $websiteContact->setStatus('Pending');
            $manager->flush($websiteContact);
        }
        if($new_status = "Pending"){
            $websiteContact->setStatus('Pending');
            $manager->flush($websiteContact);
        }
        return $this->redirect($referer);

    }



    /**
     * @Route("/delete/{id}", name="website_contacts_delete", methods={"POST"})
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
