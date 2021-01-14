<?php

namespace Egretos\RestModel\Query;

use Egretos\RestModel\Auth\Bearer\Token;
use Illuminate\Support\Arr;
use LogicException;

/**
 * Trait BearerAuth
 * @package Egretos\RestModel\Query
 *
 * @mixin Builder
 */
trait BearerAuth
{
    /**
     * @throws
     */
    public function refreshToken(): BearerAuth
    {
        $cacheKey = $this->getCacheKey();

        if (!$token = $this->getToken()) {
            /** new request only for login, when the old one is keeping */
            $builder = (new Builder)
                ->resetRequest(false)
                ->setConnection($this->getConnection());

            $response = $builder
                ->setRoute( $this->getConnection()->getConfiguration('auth.token_route') )
                ->send();

            $this->setModel( Token::make() );
            $token = $this->normalizeResponse($response);

            if ($tokenIndex = $this->getConnection()->getConfiguration('auth.token_index')) {
                $tokenString = Arr::get($token->attributesToArray(), $tokenIndex);
            } else {
                $tokenString = $token->get(['body']);
            }

            if ($tokenString) {
                cache()->set($cacheKey, $tokenString);
            } else {
                throw new LogicException('Received token is not readable! Check configuration file');
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCacheKey() {
        return $this->getConnection()
            ->getConfiguration('auth.cache_key', $this->getConnection()->connection.'bearer_token');
    }

    /**
     * @return mixed
     * @throws
     */
    public function getToken()
    {
        return cache()->get($this->getCacheKey());
    }
}