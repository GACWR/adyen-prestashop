<?php

namespace Adyen\PrestaShop\service;

use Adyen\PrestaShop\exception\GenericLoggedException;
use Adyen\PrestaShop\exception\MissingDataException;
use Adyen\PrestaShop\infra\Crypto;

class Client extends \Adyen\Client
{
    /**
     * @var adapter\classes\Configuration
     */
    private $configuration;

    /**
     * Client constructor.
     *
     * @param adapter\classes\Configuration $configuration
     * @param Logger $logger
     * @param Crypto $crypto
     *
     * @throws \Adyen\AdyenException
     */
    public function __construct(
        adapter\classes\Configuration $configuration,
        Logger $logger,
        Crypto $crypto
    ) {
        parent::__construct();

        $apiKey = '';

        // Do not call PrestaShop logger if this fails.
        // See https://github.com/PrestaShop/PrestaShop/issues/24302 for more info
        try {
            $apiKey = $crypto->decrypt($configuration->encryptedApiKey);
        } catch (GenericLoggedException $e) {
            $logger->addRecord(
                Logger::ERROR,
                'For configuration "ADYEN_CRONJOB_TOKEN" an exception was thrown: ' . $e->getMessage(),
                [],
                false
            );
        } catch (MissingDataException $e) {
            $logger->addRecord(
                Logger::ERROR,
                'The API key configuration value is missing',
                [],
                false
            );
        }

        $this->setXApiKey($apiKey);
        $this->setMerchantApplication($configuration->moduleName, $configuration->moduleVersion);
        $this->setExternalPlatform('PrestaShop', _PS_VERSION_, $configuration->integratorName);
        $this->setEnvironment($configuration->adyenMode, $configuration->liveEndpointPrefix);

        $this->setLogger($logger);

        $this->configuration = $configuration;
    }
}
