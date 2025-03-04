<?php

namespace Adyen\PrestaShop\infra;

use Adyen\PrestaShop\exception\GenericLoggedException;
use Adyen\PrestaShop\exception\MissingDataException;
use Adyen\PrestaShop\service\adapter\classes\Configuration;
use Adyen\PrestaShop\service\Logger;

class Crypto
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var string
     */
    private $method;

    public function __construct(Configuration $configuration, Logger $logger)
    {
        $this->method = 'aes-256-ctr';
        $this->configuration = $configuration;
        $this->logger = $logger;
    }

    public function encrypt($data)
    {
        // Generate an initialization vector
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->method));
        // Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
        return bin2hex($iv) . openssl_encrypt(
            $data,
            $this->method,
            $this->configuration->sslEncryptionKey,
            0,
            $iv
        );
    }

    /**
     * @param $data
     *
     * @return false|string
     *
     * @throws GenericLoggedException
     * @throws MissingDataException
     */
    public function decrypt($data)
    {
        if (empty($data)) {
            throw new MissingDataException();
        }

        $ivLength = openssl_cipher_iv_length($this->method);
        $hex = \Tools::substr($data, 0, $ivLength * 2);

        if (!ctype_xdigit($hex)) {
            throw new GenericLoggedException('Crypto decrypt() $data parameter is not hex encoded');
        }

        $iv = hex2bin($hex);

        return openssl_decrypt(
            \Tools::substr($data, $ivLength * 2),
            $this->method,
            $this->configuration->sslEncryptionKey,
            0,
            $iv
        );
    }
}
