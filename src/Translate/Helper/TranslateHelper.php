<?php

namespace Translate\Helper;

class TranslateHelper extends \Zend\I18n\View\Helper\Translate {

    protected $serviceLocator;

    public function __construct($sm) {
        $this->serviceLocator = $sm;
    }

    public function __invoke($message, $textDomain = null, $locale = null, $type = null) {
        $translateModel = new \Translate\Model\TranslateModel();
        $translateModel->initialize($this->serviceLocator);
        $translateModel->setEntity('Core\Entity\Translate');

        $translate = $translateModel->translateFromKey($message);

        return is_object($translate) ? $translate->getTranslate() : $translate;
    }

}
