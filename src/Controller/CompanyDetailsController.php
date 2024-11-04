<?php

namespace App\Controller;

use App\Entity\CompanyDetails;
use App\Form\CompanyDetailsType;
use App\Repository\CompanyDetailsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/company_details")
 */
class CompanyDetailsController extends AbstractController
{
    /**
     * @Route("/index", name="company_details_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(CompanyDetailsRepository $companyDetailsRepository): Response
    {
        return $this->render('company_details/index.html.twig', [
            'company_details' => $companyDetailsRepository->findAll()
        ]);
    }

    /**
     * @Route("/new", name="company_details_new", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request, CompanyDetailsRepository $companyDetailsRepository): Response
    {
        $companyDetails = new CompanyDetails();
        $form = $this->createForm(CompanyDetailsType::class, $companyDetails);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $faviconDev = $form['faviconDev']->getData();
            $faviconLive = $form['faviconLive']->getData();
            $qrCode = $form['companyQrCode']->getData();

            if ($faviconDev) {
                $originalFilename = pathinfo($faviconDev->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $companyDetails->getCompanyName() . '_dev.' . $faviconDev->guessExtension();
                $faviconDev->move(
                    $this->getParameter('favicon_directory'),
                    $newFilename
                );
                $companyDetails->setFaviconDev($newFilename);
            }
            if ($faviconLive) {
                $originalFilenameLive = pathinfo($faviconLive->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilenameLive = $companyDetails->getCompanyName() . '_live.' . $faviconLive->guessExtension();
                $faviconLive->move(
                    $this->getParameter('favicon_directory'),
                    $newFilenameLive
                );
                $companyDetails->setFaviconLive($newFilenameLive);
            }
            if ($qrCode) {
                $originalFilenameQR = pathinfo($qrCode->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilenameQR = $companyDetails->getCompanyName() . '_qr_code.' . $qrCode->guessExtension();
                $qrCode->move(
                    $this->getParameter('favicon_directory'),
                    $newFilenameQR
                );
                $companyDetails->setCompanyQrCode($newFilenameQR);
            }
            $companyDetailsRepository->add($companyDetails, true);

            return $this->redirectToRoute('company_details_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company_details/new.html.twig', [
            'company_details' => $companyDetails,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="company_details_show", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(CompanyDetails $companyDetails): Response
    {
        return $this->render('company_details/show.html.twig', [
            'company_details' => $companyDetails,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="company_details_edit", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, CompanyDetails $companyDetails, CompanyDetailsRepository $companyDetailsRepository): Response
    {
        $form = $this->createForm(CompanyDetailsType::class, $companyDetails);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $faviconDev = $form['faviconDev']->getData();
            $faviconLive = $form['faviconLive']->getData();
            $qrCode = $form['companyQrCode']->getData();

            if ($faviconDev) {
                $originalFilename = pathinfo($faviconDev->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $companyDetails->getCompanyName() . '_dev.' . $faviconDev->guessExtension();
                $faviconDev->move(
                    $this->getParameter('favicon_directory'),
                    $newFilename
                );
                $companyDetails->setFaviconDev($newFilename);
            }
            if ($faviconLive) {
                $originalFilenameLive = pathinfo($faviconLive->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilenameLive = $companyDetails->getCompanyName() . '_live.' . $faviconLive->guessExtension();
                $faviconLive->move(
                    $this->getParameter('favicon_directory'),
                    $newFilenameLive
                );
                $companyDetails->setFaviconLive($newFilenameLive);
            }
            if ($qrCode) {
                $originalFilenameQR = pathinfo($qrCode->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilenameQR = $companyDetails->getCompanyName() . '_qr_code.' . $qrCode->guessExtension();
                $qrCode->move(
                    $this->getParameter('favicon_directory'),
                    $newFilenameQR
                );
                $companyDetails->setCompanyQrCode($newFilenameQR);
            }
            $companyDetailsRepository->add($companyDetails, true);

            return $this->redirectToRoute('company_details_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('company_details/edit.html.twig', [
            'company_details' => $companyDetails,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="company_details_delete", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function delete(Request $request, CompanyDetails $companyDetails, CompanyDetailsRepository $companyDetailsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $companyDetails->getId(), $request->request->get('_token'))) {
            $companyDetailsRepository->remove($companyDetails, true);
        }

        return $this->redirectToRoute('company_details_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/office_address", name="office_address", methods={"GET"})
     */
    public function officeAddress(CompanyDetailsRepository $companyDetailsRepository): Response
    {
        return $this->render('home/officeAddress.html.twig');
    }

    /**
     * @Route("/delete_favicon/{live_or_dev}/{id}", name="company_details_delete_favicon", methods={"POST", "GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteLiveFavicon(Request $request, int $id, string $live_or_dev, CompanyDetails $companyDetails, EntityManagerInterface $entityManager)
    {
        $referer = $request->headers->get('referer');
        if ($live_or_dev == 'live') {
            $companyDetails->setFaviconLive(null);
            $entityManager->flush();
            $files = glob($this->getParameter('favicon_directory') . "/*live*");
            foreach ($files as $file) {
                unlink($file);
            }
        }
        if ($live_or_dev == 'dev') {
            $companyDetails->setFaviconDev(null);
            $entityManager->flush();
            $files = glob($this->getParameter('favicon_directory') . "/*dev*");
            foreach ($files as $file) {
                unlink($file);
            }
        }
        $entityManager->flush();
        return $this->redirect($referer);
    }


    /**
     * @Route("/delete_qr_code/{id}", name="company_details_delete_qr_code", methods={"POST", "GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function deleteQRCodeLiveFavicon(Request $request, int $id, CompanyDetails $companyDetails, EntityManagerInterface $entityManager)
    {
        $referer = $request->headers->get('referer');
        $companyDetails->setCompanyQrCode(null);
        $entityManager->flush();
        $files = glob($this->getParameter('favicon_directory') . "/*qr*");
        foreach ($files as $file) {
            unlink($file);
        }

        $entityManager->flush();
        return $this->redirect($referer);
    }

    /**
     * @Route("/export/live/database", name="export_live_database", methods={"POST", "GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function exportDatabase(Request $request, EntityManagerInterface $entityManager, \App\Services\CompanyDetails $companyDetails)
    {
        $filename = $companyDetails->getCompanyDetails()->getSqlDatabase().'.sql';
        $sqlDatabase = $companyDetails->getCompanyDetails()->getSqlDatabase();
        $sqlPassword = $companyDetails->getCompanyDetails()->getDatabasePassword();

        if( $_ENV['APP_SERVER']=="local"){
            exec('mysqldump --user=root --password= --host=localhost '.$sqlDatabase.'>'.$filename);
        }
        else{
            exec('mysqldump --user=stephen --password='.$sqlPassword.' --host=localhost '.$sqlDatabase.' >'.$filename);
        }
        $file = $this->getParameter('public').$filename;
        if(file_exists($file)){
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . basename($filename) . "\"");
            readfile($file);
            unlink($file);
        }
        $referer = $request->headers->get('Referer');
        return $this->redirect($referer);
    }
}
