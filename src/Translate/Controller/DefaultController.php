<?php

namespace Translate\Controller;

use Core\Controller\AbstractController;
use Translate\Model\TranslateModel;
use Core\Model\ErrorModel;
use Core\Helper\Format;

class DefaultController extends AbstractController {

    public function indexAction() {
        $translateModel = new TranslateModel();
        $translateModel->initialize($this->serviceLocator);
        $this->_view->translates = $translateModel->getAllTranslates();
        $this->_view->companyLanguage = $translateModel->getCompanyLanguage();
        $this->_view->userLanguage = $translateModel->getUserLanguage();
        return $this->_view;
    }
    public function revisedTranslateAction() {
        $translate = $this->params()->fromPost('translate');
        $id = $this->params()->fromPost('id');
        if (!$id || !$translate) {
            ErrorModel::addError('You need send ID and translate');
            return;
        }
        $translateModel = new TranslateModel();
        $translateModel->initialize($this->serviceLocator);
        $entity = $translateModel->revisedTranslate($id, $translate);
        
        $this->_view->setVariables(Format::returnData(array(
                    'id' => $entity->getId(),
                    'original' => $this->params()->fromPost('original-translate'),
                    'translate' => $entity->getTranslate()
        )));

        return $this->_view;
    }
    public function automaticTranslateAction() {
        $translate = $this->params()->fromPost('translate');
        $id = $this->params()->fromPost('id');
        if (!$id || !$translate) {
            ErrorModel::addError('You need send ID and translate');
            return;
        }
        $translateModel = new TranslateModel();
        $translateModel->initialize($this->serviceLocator);
        $entity = $translateModel->automaticTranslate($id, $translate);
        
        $this->_view->setVariables(Format::returnData(array(
                    'id' => $entity->getId(),
                    'original' => $this->params()->fromPost('original-translate'),
                    'translate' => $entity->getTranslate()
        )));

        return $this->_view;
    }

}
