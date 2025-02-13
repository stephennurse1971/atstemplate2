<?php

namespace App\Services;

use App\Entity\EmailTemplates;
use App\Entity\Transaction;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use function App\Services\str_contains;

class StringReplaceService
{
    public function replace(Transaction $transaction, EmailTemplates $emailTemplates)
    {
        $body = $emailTemplates->getBody();

        if (str_contains($body, '%transaction_reference%')) {
            $body = str_replace('%transaction_reference%', $transaction->getId(), $body);
        }

        if (str_contains($body, '%name%')) {
            $body = str_replace('%name%', $transaction->getClient()->getFullName(), $body);
        }

        if (str_contains($body, '%transaction_link%')) {
            $transaction_link = $this->router->generate('transaction_show', ['id' => $transaction->getId()], urlGeneratorInterface::ABSOLUTE_URL);
            $body = str_replace('%transaction_link%', $transaction_link, $body);
        }

        if (str_contains($body, '%transaction_service%')) {
            $body = str_replace('%transaction_service%', $transaction->getService()->getServiceOffered(), $body);
        }

        if (str_contains($body, '%transaction_status%')) {
            $body = str_replace('%transaction_status%', $transaction->getStatus(), $body);
        }
        return $body;

    }

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

}