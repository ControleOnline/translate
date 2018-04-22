<?php

namespace Translate\Model;

use Core\Model\DefaultModel;
use Company\Model\CompanyModel;
use User\Model\UserModel;

class TranslateModel extends DefaultModel {

    protected static $languageId = 2;
    protected static $translate_cache = array();
    protected static $default_translate_cache = array();

    /**
     * @var \Company\Model\CompanyModel
     */
    protected $_companyModel;

    /**
     * @var \Core\Entity\Language
     */
    protected static $default_language;

    /**
     * @var \User\Model\UserModel
     */
    protected $_userModel;

    /**
     * @var \Core\Entity\Language
     */
    protected static $company_language;

    /**
     * @var \Core\Entity\Language
     */
    protected static $user_language;

    public function initialize(\Zend\ServiceManager\ServiceManager $serviceLocator) {
        parent::initialize($serviceLocator);
        $company = new CompanyModel();
        $company->initialize($serviceLocator);
        $this->_companyModel = $company;

        $user = new UserModel();
        $user->initialize($serviceLocator);
        $this->_userModel = $user;

        if (!self::$translate_cache) {
            $this->getAllTranslateCache();
        }
    }

    public function getAllTranslates($limit = 100, $offset = 0) {
        $translate['wait_translate'] = $this->_em->getRepository('\Core\Entity\Translate')->findBy(array(
            'lang' => $this->getUserLanguage(),
            'status' => '1',
            'people' => $this->_companyModel->getPeopleByDomain()
                ), array('translate' => 'ASC'), $limit, $offset);
        $translate['wait_revision'] = $this->_em->getRepository('\Core\Entity\Translate')->findBy(array(
            'lang' => $this->getUserLanguage(),
            'status' => '2',
            'people' => $this->_companyModel->getPeopleByDomain()
                ), array('translate' => 'ASC'), $limit, $offset);
        $translate['revised'] = $this->_em->getRepository('\Core\Entity\Translate')->findBy(array(
            'lang' => $this->getUserLanguage(),
            'status' => '3',
            'people' => $this->_companyModel->getPeopleByDomain()
                ), array('translate' => 'ASC'), $limit, $offset);
        return $translate;
    }

    protected function getAllTranslateCache() {
        if (!self::$translate_cache) {
            $entity = $this->_em->getRepository('\Core\Entity\Translate')->findBy(array(
                'lang' => $this->getUserLanguage(),
                'people' => $this->_companyModel->getPeopleByDomain()
            ));
            foreach ($entity AS $translate) {
                self::$translate_cache[$translate->getTranslateKey()] = $translate;
            }
        }
        return self::$translate_cache;
    }

    protected function getAllDefaultTranslateCache() {
        if (!self::$default_translate_cache) {
            $entity = $this->_em->getRepository('\Core\Entity\Translate')->findBy(array(
                'lang' => $this->getDefaultLanguage(),
                'people' => $this->_companyModel->getPeopleByDomain()
            ));
            foreach ($entity AS $translate) {
                self::$default_translate_cache[$translate->getTranslateKey()] = $translate;
            }
        }
        return self::$default_translate_cache;
    }

    public function defaultTranslateFromKey($translate) {
        if (isset(self::$default_translate_cache[$this->getKey($translate)]) && self::$default_translate_cache[$this->getKey($translate)]) {
            return self::$default_translate_cache[$this->getKey($translate)];
        }
        $entity = $this->discoveryDefaultTranslate($translate, $this->getDefaultLanguage());
        self::$default_translate_cache[$this->getKey($translate)] = $entity ? $entity : $translate;
        return self::$default_translate_cache[$this->getKey($translate)];
    }

    public function translateFromKey($translate) {
        if (isset(self::$translate_cache[$this->getKey($translate)]) && self::$translate_cache[$this->getKey($translate)]) {
            return self::$translate_cache[$this->getKey($translate)];
        }
        $entity = $this->discoveryTranslate($translate);
        self::$translate_cache[$this->getKey($translate)] = $entity ? $entity : $translate;
        return self::$translate_cache[$this->getKey($translate)];
    }

    public function revisedTranslate($id, $translate) {
        $entity = $this->_em->getRepository('\Core\Entity\Translate')->findOneBy(array(
            'id' => $id,
            'people' => $this->_companyModel->getPeopleByDomain()
        ));
        $entity->setTranslate($translate);
        $entity->setStatus('3');
        try {
            $this->_em->persist($entity);
            $this->_em->flush($entity);
            return $entity;
        } catch (Exception $e) {
            ErrorModel::addError(array('code' => $e->getCode(), 'message' => 'Error on add default translate'));
            ErrorModel::addError(array('code' => $e->getCode(), 'message' => $e->getMessage()));
            $this->_em->rollback();
        }
    }

    public function automaticTranslate($id, $translate) {
        $entity = $this->_em->getRepository('\Core\Entity\Translate')->findOneBy(array(
            'id' => $id,
            'people' => $this->_companyModel->getPeopleByDomain()
        ));
        $entity->setTranslate($translate);
        $entity->setStatus('2');
        try {
            $this->_em->persist($entity);
            $this->_em->flush($entity);
            return $entity;
        } catch (Exception $e) {
            ErrorModel::addError(array('code' => $e->getCode(), 'message' => 'Error on add default translate'));
            ErrorModel::addError(array('code' => $e->getCode(), 'message' => $e->getMessage()));
            $this->_em->rollback();
        }
    }

    protected function discoveryTranslate($translate) {
        //if (!$this->_companyModel->getDefaultCompany() || $this->getDefaultLanguage()->getId() != $this->getUserLanguage()->getId()) {
        if (!$this->_companyModel->getDefaultCompany()) {
            $entity = $this->discoveryDefaultTranslate($translate, $this->getCompanyLanguage() ? : $this->getDefaultLanguage());
        } else {
            $entity = $this->_em->getRepository('\Core\Entity\Translate')->findOneBy(array(
                'translate_key' => $this->getKey($translate),
                'lang' => $this->getUserLanguage(),
                'people' => $this->_companyModel->getPeopleByDomain()
            ));
            if (!$entity) {
                try {
                    $defaultTranslate = $this->discoveryDefaultTranslate($translate, $this->getUserLanguage());
                    $entity = new \Core\Entity\Translate();
                    $entity->setLang($this->getUserLanguage()->getId());
                    $entity->setTranslate($defaultTranslate ? $defaultTranslate->getTranslate() : $translate);
                    $entity->setPeople($this->_companyModel->getPeopleByDomain());
                    $entity->setTranslateKey($this->getKey($translate));
                    $entity->setStatus($defaultTranslate ? 2 : 1);
                    $this->_em->persist($entity);
                    $this->_em->flush($entity);
                } catch (Exception $e) {
                    ErrorModel::addError(array('code' => $e->getCode(), 'message' => 'Error on add user translate'));
                    ErrorModel::addError(array('code' => $e->getCode(), 'message' => $e->getMessage()));
                    $this->_em->rollback();
                }
            }
        }
        return $entity;
    }

    protected function discoveryDefaultTranslate($translate, $lang) {
        $entity = $this->_em->getRepository('\Core\Entity\Translate')->findOneBy(array(
            'translate_key' => $this->getKey($translate),
            'lang' => $lang? : $this->getDefaultLanguage(),
            'people' => $this->_companyModel->getDefaultCompany()
        ));
        if (!$entity) {
            try {
                $entity = new \Core\Entity\Translate();
                $entity->setLang($this->getDefaultLanguage()->getId());
                $entity->setTranslate($translate);
                $entity->setPeople($this->_companyModel->getPeopleByDomain());
                $entity->setTranslateKey($this->getKey($translate));
                $this->_em->persist($entity);
                $this->_em->flush($entity);
            } catch (Exception $e) {
                ErrorModel::addError(array('code' => $e->getCode(), 'message' => 'Error on add default translate'));
                ErrorModel::addError(array('code' => $e->getCode(), 'message' => $e->getMessage()));
                $this->_em->rollback();
            }
        }
        return $entity;
    }

    protected function getKey($translate) {
        return strtolower(preg_replace('/\s+/', '_', $translate));
    }

    public function getDefaultLanguage() {
        if (!self::$default_language) {
            self::$default_language = $this->_em->getRepository('\Core\Entity\Language')->findOneBy(array(
                'language' => 'en'
            ));
        }
        return self::$default_language;
    }

    public function getCompanyLanguage() {
        if (!self::$company_language) {
            self::$company_language = $this->_companyModel->getPeopleByDomain()->getLanguage();
        }
        return self::$company_language;
    }

    public function getUserLanguage() {
        if (!self::$user_language) {
            self::$user_language = self::$company_language = $this->_userModel->getDefaultUserLanguage();
        }
        return self::$user_language;
    }

    public function translate($id, $languageId = null) {
        self::setLanguageId($languageId ? : self::getLanguageId());
        $data = $this->get($id);
        $translate = $this->getEntityKey($this->entity_name);
        return isset($data[$translate]) ? $data[$translate][0]['translate'] : null;
    }

    public static function getLanguageId() {
        return self::$languageId;
    }

    public static function setLanguageId($languageId) {
        self::$languageId = $languageId;
    }

}
