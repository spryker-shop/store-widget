<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\StoreWidget\Plugin\ShopApplication;

use Exception;
use Spryker\Service\Container\ContainerInterface;
use Spryker\Shared\ApplicationExtension\Dependency\Plugin\ApplicationPluginInterface;
use Spryker\Yves\Kernel\AbstractPlugin;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \SprykerShop\Yves\StoreWidget\StoreWidgetConfig getConfig()
 * @method \SprykerShop\Yves\StoreWidget\StoreWidgetFactory getFactory()
 */
class StoreApplicationPlugin extends AbstractPlugin implements ApplicationPluginInterface
{
    /**
     * @var string
     */
    protected const STORE = 'store';

    /**
     * @var string
     */
    protected const SESSION_STORE = 'current_store';

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Spryker\Service\Container\ContainerInterface
     */
    public function provide(ContainerInterface $container): ContainerInterface
    {
        $container = $this->addStore($container);

        return $container;
    }

    /**
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @return \Spryker\Service\Container\ContainerInterface
     */
    protected function addStore(ContainerInterface $container): ContainerInterface
    {
        $container->set(static::STORE, function (ContainerInterface $container) {
            $storeName = $this->resolveStoreName($container);

            $this->getFactory()->getSessionClient()->set(static::SESSION_STORE, $storeName);

            return $storeName;
        });

        return $container;
    }

    /**
     * @param \Spryker\Service\Container\ContainerInterface $container
     *
     * @throws \Exception
     *
     * @return string
     */
    protected function resolveStoreName(ContainerInterface $container): string
    {
        $storeName = $this->getStoreRequestUrlParameter();
        $storeNames = $this->getFactory()->getStoreStorageClient()->getStoreNames();
        if ($storeName) {
            if (in_array($storeName, $storeNames, true)) {
                return $storeName;
            }
        }

        $storeName = $this->getFactory()->getSessionClient()->get(static::SESSION_STORE);
        if ($storeName) {
            return $storeName;
        }

        if (defined('APPLICATION_STORE')) {
            return APPLICATION_STORE;
        }

        $defaultStoreName = current($storeNames);

        if (!$defaultStoreName) {
            throw new Exception('Cannot resolve store');
        }

        return $defaultStoreName;
    }

    /**
     * @return string|null
     */
    protected function getStoreRequestUrlParameter(): ?string
    {
        $requestStack = $this->getFactory()->getRequestStack();

        if ($requestStack->getCurrentRequest() === null) {
            $requestStack = $this->getFactory()->createRequestStack();
            $requestStack->push(Request::createFromGlobals());
        }

        /** @var \Symfony\Component\HttpFoundation\Request $currentRequest */
        $currentRequest = $requestStack->getCurrentRequest();
        $store = $currentRequest->query->get('_store');

        if ($store !== null) {
            /** @phpstan-var string */
            return $store;
        }

        return $this->extractStoreCode($currentRequest->getRequestUri());
    }

    /**
     * @param string $requestUri
     *
     * @return string|null
     */
    protected function extractStoreCode(string $requestUri): ?string
    {
        $urlPath = (string)parse_url(trim($requestUri, '/'), PHP_URL_PATH);
        $pathElements = explode('/', $urlPath);

        return $pathElements[$this->getConfig()->getStoreCodeIndex()] ?? null;
    }
}
