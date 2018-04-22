<?php

namespace Translate\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TranslateFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $sl) {
        return new \Translate\Helper\TranslateHelper($sl->getServiceLocator());
    }

}
