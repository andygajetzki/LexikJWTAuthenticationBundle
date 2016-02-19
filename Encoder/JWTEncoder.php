<?php

namespace Lexik\Bundle\JWTAuthenticationBundle\Encoder;

use InvalidArgumentException;
use Namshi\JOSE\SimpleJWS;

/**
 * JWTEncoder
 *
 * @author Dev Lexik <dev@lexik.fr>
 */
class JWTEncoder implements JWTEncoderInterface
{
    const ALGORYTHM = 'HS256';

    /**
     * @var string
     */
    protected $secret;


    /**
     * @param string $privateKey
     * @param string $publicKey
     * @param string $passPhrase
     */
    public function __construct($secret)
    {
        $this->secret = $secret;

    }

    /**
     * {@inheritdoc}
     */
    public function encode(array $data)
    {
        $jws = new SimpleJWS(['alg' => self::ALGORYTHM]);
        $jws->setPayload($data);
        $jws->sign($this->secret);

        return $jws->getTokenString();
    }

    /**
     * {@inheritdoc}
     */
    public function decode($token)
    {
        try {
            /** @var SimpleJWS $jws */
            $jws = SimpleJWS::load($token);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        if (!$jws->isValid($this->secret, self::ALGORYTHM)) {
            return false;
        }

        return $jws->getPayload();
    }

    /**
     * @return bool|resource
     */
    protected function getPrivateKey()
    {
        return openssl_pkey_get_private('file://' . $this->privateKey, $this->passPhrase);
    }

    /**
     * @return resource
     */
    protected function getPublicKey()
    {
        return openssl_pkey_get_public('file://' . $this->publicKey);
    }
}
