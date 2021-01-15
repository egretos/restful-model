<?php

namespace Egretos\RestModel\Query;

use Egretos\RestModel\Auth\Bearer\Token;
use Egretos\RestModel\Request;
use Illuminate\Support\Arr;
use LogicException;

/**
 * Trait BearerAuth
 * @package Egretos\RestModel\Query
 *
 * @mixin Builder
 *
 * TODO maybe we should remove it
 */
trait BearerAuth
{
    /**
     * @throws
     */
    public function refreshToken(): BearerAuth
    {
        $authData = $this->getConnection()->getConfiguration()->get('auth');
        $cacheKey = $this->getCacheKey();

        if (!$token = $this->getToken()) {
            /** new request only for login, when the old one is keeping */
            $builder = (new Builder)
                ->resetRequest(false)
                ->setConnection($this->getConnection());

            $builder
                ->getRequest()
                ->setAuth([$authData['login'], $authData['password']]);

            $response = $builder
                ->setRoute( $authData['token_route'] )
                ->setMethod( Request::METHOD_POST )
                ->send();

            $this->setModel( Token::make() );
            $token = $this->normalizeResponse($response);

            if ($tokenIndex = $authData['token_index']) {
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