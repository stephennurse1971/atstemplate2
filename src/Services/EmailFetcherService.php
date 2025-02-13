<?php
namespace App\Services;

use PhpImap\Mailbox;
use PhpImap\Exceptions\ConnectionException;

class EmailFetcherService
{
    private $mailbox;

    public function __construct()
    {
        // Set up IMAP connection details
        $host = 'ssl0.ovh.net';  // IMAP host for OVHCloud
        $username = 'techarete@atts-systems.com';  // Your email address
        $password = 'Descartes99';  // Your email password
        $folder = 'INBOX';  // Specify folder (optional)
        $port = 993;

        // Use PhpImap\Mailbox to connect (no need for OP_READONLY or imap_open)
        try {
            $this->mailbox = new Mailbox(
                '{' . $host . ':' . $port . '/imap/ssl/novalidate-cert}' . $folder,  // Connection string
                $username,  // Email username
                $password,  // Email password
                __DIR__  // Directory for storing attachments (optional)
            );
        } catch (ConnectionException $e) {
            throw new \Exception("Failed to connect to IMAP server: " . $e->getMessage());
        }
    }





    public function fetchEmails()
    {
        try {
            // Get the emails from the inbox (or another folder)
            $emails = $this->mailbox->searchMailbox('ALL'); // You can adjust the search criteria
            $emailDetails = [];

            foreach ($emails as $emailId) {
                $email = $this->mailbox->getMail($emailId);
                $emailDetails[] = [
                    'subject' => $email->subject,
                    'from' => $email->fromAddress,
                    'date' => $email->date,
                    'body' => $email->textHtml,  // Or use $email->textPlain for plain text
                ];
            }

            return $emailDetails;
        } catch (\Exception $e) {
            return ['error' => 'Failed to fetch emails: ' . $e->getMessage()];
        }
    }
}
