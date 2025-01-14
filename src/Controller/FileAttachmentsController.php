<?php

namespace App\Controller;

use App\Entity\FileAttachments;
use App\Form\FileAttachmentsType;
use App\Repository\FileAttachmentsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/fileattachments")
 */
class FileAttachmentsController extends AbstractController
{
    /**
     * @Route("/index", name="file_attachments_index", methods={"GET"})
     */
    public function index(FileAttachmentsRepository $fileAttachmentsRepository): Response
    {
        return $this->render('file_attachments/index.html.twig', [
            'file_attachments' => $fileAttachmentsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/show/attachment/{id}/{filename}", name="show_attachment")
     */
    public function showAttachment(string $filename, int $id, FileAttachmentsRepository $fileAttachmentsRepository)
    {
        $filepath = $this->getParameter('file_attachments_directory') . "/" . $filename;
        if (file_exists($filepath)) {
            return $this->file($filepath, 'sample.pdf', ResponseHeaderBag::DISPOSITION_INLINE);
        } else {
            return new Response("file does not exist");
        }
    }

    /**
     * @Route("/show_attachment/{id}/{filename}", name="show_attachment_file_upload_directory")
     */
    public function showAttachmentFileUploadDirectory(string $filename, int $id, FileAttachmentsRepository $fileAttachmentsRepository)
    {
        $filepath = $this->getParameter('file_attachments_directory') . "/" . $filename;
        if (file_exists($filepath)) {
            $response = new BinaryFileResponse($filepath);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE, //use ResponseHeaderBag::DISPOSITION_ATTACHMENT to save as an attachment
                $filename
            );
            return $response;
        } else {
            return new Response("file does not exist");
        }
    }

    /**
     * @Route("/new", name="file_attachments_new", methods={"GET","POST"})
     */
    public function new(Request $request, FileAttachmentsRepository $fileAttachmentsRepository): Response
    {
        $fileAttachment = new FileAttachments();
        $form = $this->createForm(FileAttachmentsType::class, $fileAttachment);
        $form->remove('additional');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $attachments = $form['attachments']->getData();
            if ($attachments) {
                $files_name = [];
                $attachment_directory = $this->getParameter('file_attachments_directory');
                foreach ($attachments as $attachment) {
                    $fileName = pathinfo($attachment->getClientOriginalName(), PATHINFO_FILENAME);
                    $file_extension = $attachment->guessExtension();
                    $newFileName = $fileName . "." . $file_extension;
                    $attachment->move($attachment_directory, $newFileName);
                    $files_name[] = $newFileName;
                }
                $fileAttachment->setAttachments($files_name);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fileAttachment);
            $entityManager->flush();
            return $this->redirectToRoute('file_attachments_index');
        }

        return $this->render('file_attachments/new.html.twig', [
            'file_attachments' => $fileAttachment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="file_attachments_show", methods={"GET"})
     */
    public function show(FileAttachments $fileAttachments): Response
    {
        return $this->render('file_attachments/show.html.twig', [
            'file_attachment' => $fileAttachments,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="file_attachments_edit", methods={"GET","POST"})
     */
    public function edit(int $id, Request $request, FileAttachments $fileAttachments): Response
    {
        $form = $this->createForm(FileAttachmentsType::class, $fileAttachments, ['id' => $id]);
        if (empty($fileAttachments->getAttachments())) {
            $form->remove('additional');
        } else {
            $form->remove('attachments');
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!empty($fileAttachments->getAttachments())) {
                $attachments = $form['additional']->getData();
                if ($attachments) {
                    $files_name = [];
                    $attachment_directory = $this->getParameter('file_attachments_directory');
                    foreach ($attachments as $attachment) {
                        $fileName = pathinfo($attachment->getClientOriginalName(), PATHINFO_FILENAME);
                        $file_extension = $attachment->guessExtension();
                        $newFileName = $fileName . "." . $file_extension;
                        $attachment->move($attachment_directory, $newFileName);
                        $files_name[] = $newFileName;
                    }
                    $previous_files = $fileAttachments->getAttachments();
                    if (!empty($previous_files)) {
                        $files = array_merge($previous_files, $files_name);
                    } else {
                        $files = array_merge($files_name);
                    }
                    $fileAttachments->setAttachments($files);
                }
            }
            if (empty($fileAttachments->getAttachments())) {
                $attachments = $form['attachments']->getData();
                if ($attachments) {
                    $files_name = [];
                    $attachment_directory = $this->getParameter('file_attachments_directory');
                    foreach ($attachments as $attachment) {
                        $fileName = pathinfo($attachment->getClientOriginalName(), PATHINFO_FILENAME);
                        $file_extension = $attachment->guessExtension();
                        $newFileName = $fileName . "." . $file_extension;
                        $attachment->move($attachment_directory, $newFileName);
                        $files_name[] = $newFileName;
                    }
                    $fileAttachments->setAttachments($files_name);
                }
            }
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('file_attachments_index');
        }

        return $this->render('file_attachments/edit.html.twig', [
            'file_attachment' => $fileAttachments,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/delete/{id}", name="file_attachments_delete", methods={"POST"})
     */
    public function delete(Request $request, FileAttachments $fileAttachments): Response
    {
        if ($this->isCsrfTokenValid('delete' . $fileAttachments->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fileAttachments);
            $entityManager->flush();
        }
        return $this->redirectToRoute('file_attachments_index');
    }


    /**
     * @Route("/email_fileattachments/{fileid}/{recipientid}", name="file_attachments_email")
     */
    public function emailFileAttachments(Security                  $security, int $fileid, int $recipientid, Request $request, UserRepository $userRepository,
                                         FileAttachmentsRepository $fileAttachmentsRepository, MailerInterface $mailer)
    {
        $file = $fileAttachmentsRepository->find($fileid);
        $senderEmail = $security->getUser()->getEmail();
        $recipient = $userRepository->find($recipientid);
        $subject = 'File Attachments: ' . $file->getCategory();
        $html = $this->renderView('file_attachments/email_attachment.html.twig', [
            'subject' => $subject,
            'description' => $file->getDescription(),
        ]);
        $attachments = $file->getAttachments();
        $email = (new Email())
            ->to($recipient->getEmail())
            ->subject($subject)
            ->from($senderEmail)
            ->html($html);
        if ($attachments) {
            foreach ($attachments as $attachment) {
                $attachment_path = $this->getParameter('file_attachments_directory') . "/" . $attachment;
                $email->attachFromPath($attachment_path);
            }
        }
        $mailer->send($email);
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }


    /**
     * @Route("/delete/attachment/{id}", name="file_attachments_delete_attachment")
     */
    public function deleteAttachment(Request $request, FileAttachments $fileAttachments, EntityManagerInterface $entityManager)
    {
        $referer = $request->headers->get('referer');
        $fileAttachments->setAttachments(null);
        $entityManager->flush();
        return $this->redirect($referer);
    }
}
