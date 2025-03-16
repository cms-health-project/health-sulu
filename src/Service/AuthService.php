<?php

namespace CmsHealthProject\Service;

/**
 * This is a service to compare a static token against the content of the authentication header.
 * We should refactor the authentication to use something like OAuth 2.0 with multiple credentials,
 * as this currently is comparing a static password.
 *
 * Some logging would also be nice.
 */
class AuthService
{
    private const MIN_AUTH_TOKEN_LENGTH = 16;

    private string $secretToken;

    public function __construct(string $secretToken)
    {
        $this->secretToken = $secretToken;
    }

    public function isTokenValid($accessToken): bool
    {
        if (strlen($accessToken) < static::MIN_AUTH_TOKEN_LENGTH) {
            return false;
        }

        return $accessToken === $this->secretToken;
    }
}
