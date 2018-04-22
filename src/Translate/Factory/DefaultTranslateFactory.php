<?php

namespace Translate\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DefaultTranslateFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $sl) {
        return new \Translate\Helper\DefaultTranslateHelper($sl->getServiceLocator());
    }

}
