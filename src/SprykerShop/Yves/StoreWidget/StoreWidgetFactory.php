<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\StoreWidget;

use Spryker\Yves\Kernel\AbstractFactory;
use SprykerShop\Yves\StoreWidget\Dependency\Client\StoreWidgetToSessionClientInterface;
use SprykerShop\Yves\StoreWidget\Dependency\Client\StoreWidgetToStoreClientInterface;
use SprykerShop\Yves\StoreWidget\Dependency\Client\StoreWidgetToStoreStorageClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @method \SprykerShop\Yves\StoreWidget\StoreWidgetConfig getConfig()
 */
class StoreWidgetFactory extends AbstractFactory
{
    public function getStoreClient(): StoreWidgetToStoreClientInterface
    {
        return $this->getProvidedDependency(StoreWidgetDependencyProvider::CLIENT_STORE);
    }

    public function getStoreStorageClient(): StoreWidgetToStoreStorageClientInterface
    {
        return $this->getProvidedDependency(StoreWidgetDependencyProvider::CLIENT_STORE_STORAGE);
    }

    public function getSessionClient(): StoreWidgetToSessionClientInterface
    {
        return $this->getProvidedDependency(StoreWidgetDependencyProvider::CLIENT_SESSION);
    }

    public function getRequest(): ?Request
    {
        return $this->getRequestStack()->getCurrentRequest();
    }

    public function getRequestStack(): RequestStack
    {
        return $this->getProvidedDependency(StoreWidgetDependencyProvider::SERVICE_REQUEST_STACK);
    }

    public function createRequestStack(): RequestStack
    {
        return new RequestStack();
    }
}
