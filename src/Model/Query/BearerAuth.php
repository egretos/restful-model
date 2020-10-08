<?php

namespace Egretos\RestModel\Query;

use Egretos\RestModel\Auth\Bearer\Token;
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
    public function touchToken() {
        $cacheKey = $this->getConnection()
            ->getConfiguration('auth.cache_key', $this->getConnection()->connection.'bearer_token');

        if (!$token = cache()->get($cacheKey)) {
            $builder = new Builder($this->getConnection());
            $response = $builder
                ->setRoute( $this->getConnection()->getConfiguration('auth.token_route') )
                ->send();

            $this->setModel( Token::make() );
            $token = $this->normalizeResponse($response);

            if ($tokenIndex = $this->getConnection()->getConfiguration('auth.token_index', null)) {
                $tokenString = $token->get($tokenIndex);
            } else {
                $tokenString = $token->get(['body']);
            }

            if ($tokenString) {
                cache()->set($cacheKey, $tokenString);
            } else {
                throw new LogicException('Received token is not readable! Check configuration file');
            }
        }
    }
}