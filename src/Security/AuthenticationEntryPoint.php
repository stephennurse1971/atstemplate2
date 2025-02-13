<?php

// src/Security/AuthenticationEntryPoint.php
namespace App\Security;

use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        // You can return a custom response for unauthorized access (401)
        return new Response('Authentication required', Response::HTTP_UNAUTHORIZED);
    }
}
