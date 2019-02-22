<?php

namespace lispa\amos\cwh\base;

use lispa\amos\cwh\AmosCwh;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class ModelConfig
 *
 * @package lispa\amos\cwh\base
 * @var string classname
 * @var string label
 * @var string module_id
 * @var boolean configured
 *
 */
class ModelConfig extends Model
{
    public $classname;
    public $label;
    public $tablename;
    public $module_id;
    public $configured;
    public $config_class;
    public $base_url_config;
    public $configWrapper;

    public function attributeLabels()
    {
        return [
            'classname' => AmosCwh::t('amoscwh', 'Classname'),
            'label' => AmosCwh::t('amoscwh', 'Label'),
            'tablename' => AmosCwh::t('amoscwh', 'Table Name'),
            'module_id' => AmosCwh::t('amoscwh', 'Modulo'),
            'configured' => AmosCwh::t('amoscwh', 'Configurato'),
        ];
    }

    /*
        public function init()
        {
            parent::init();
            $this->configured = $this->isConfigured();
        }
    */

    public function composeUrl()
    {
        $attributes = $this->getAttributes();

        if ($this->isConfigured()) {
            $configClass = $this->getConfigWrapper();
            $attributes = $configClass::getConfig($this->classname)->getAttributes();
        }

        return ArrayHelper::merge(
            [
                $this->base_url_config
            ],
            $attributes
        );
    }

    public function isConfigured()
    {
        $configClass = $this->getConfigWrapper();
        return $configClass::getConfig($this->classname) ? true : false;
    }

    public function getConfigWrapper()
    {
        return $this->configWrapper = \Yii::createObject($this->config_class);
    }

    function __toString()
    {
        return ($this->label) ? $this->label : '';
    }


}