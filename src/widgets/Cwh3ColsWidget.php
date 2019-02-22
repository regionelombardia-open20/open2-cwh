<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\cwh
 * @category   CategoryName
 */

namespace lispa\amos\cwh\widgets;

use lispa\amos\admin\models\UserProfile;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use lispa\amos\cwh\AmosCwh;


class Cwh3ColsWidget extends Widget
{
    public $layout = "<div class=\"col-xs-12\">{validatori}</div><div class=\"col-xs-12\">{previewSign}</div><div class=\"col-xs-12\">{regolaPubblicazione}</div><div class=\"col-xs-12\">{destinatari}</div>";
    protected $regolaPubblicazione = [];
    protected $validatori = [];
    protected $destinatari = [];
    /**
     * @var \yii\widgets\ActiveForm $form
     */
    protected $form = null;

    /*
     * SIM
     */
    public $renderCols = true;


    //protected $layout = "<div class=\"col-xs-12\">{regolaPubblicazione}</div><div class=\"col-xs-12\">{destinatari}</div><div class=\"col-xs-12\">{validatori}</div>";
    /**
     * @var \yii\db\ActiveRecord $model
     */
    protected $model = null;

    public function init()
    {

        /**
         *
         *
         * public $validatoriEnabled = true;
         * public $destinatariEnabled = true;
         * public $pubblicazioneEnabled = true;
         */

        $regolaPubblicazione = "{regolaPubblicazione}";
        $destinatari = "{destinatari}";
        $validatori = "{validatori}";
        $recipientsCheck = "{recipientsCheck}";
        if (!\Yii::$app->getModule('cwh')->validatoriEnabled) {
            $validatori = '';
        }

        if (!\Yii::$app->getModule('cwh')->destinatariEnabled) {
            $destinatari = '';
            $recipientsCheck = '';
        }

        if (!\Yii::$app->getModule('cwh')->regolaPubblicazioneEnabled) {
            $regolaPubblicazione = '';
            $recipientsCheck = '';
        }

        parent:: init();
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @param null $destinatari
     */
    public function setEditori($destinatari)
    {
        $this->destinatari = $destinatari;
    }

    public function run()
    {
        $layoutToRender = "";

        if($this->renderCols) {
            $layoutToRender = $this->render('layouts/3cols');
        } else {
            $layoutToRender = $this->render('layouts/check-recipients');
        }
        $content = preg_replace_callback("/{\\w+}/", function ($matches) {
            $content = $this->renderSection($matches[0]);

            return $content === false ? $matches[0] : $content;
        }, $layoutToRender);

        return $content;
    }

    /**
     * Renders a section of the specified name.
     * If the named section is not supported, false will be returned.
     * @param string $name the section name, e.g., `{summary}`, `{items}`.
     * @return string|boolean the rendering result of the section, or false if the named section is not supported.
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{regolaPubblicazione}':
                return $this->renderRegolaPubblicazione();
            case '{destinatari}':
                return $this->renderEditori();
            case '{validatori}':
                return $this->renderValidatori();
            case '{recipientsCheck}':
                return $this->renderRecipientsCheck();
            case '{previewSign}':
                return $this->renderPreviewSign();
            default:
                return false;
        }
    }

    protected function renderRegolaPubblicazione()
    {

        $configRegolaPubblicazione = [
            'form' => $this->getForm(),
            'model' => $this->getModel(),
        ];

        $configRegolaPubblicazione = ArrayHelper::merge($configRegolaPubblicazione, $this->getRegolaPubblicazione());

        return RegolaPubblicazioneNEW::widget($configRegolaPubblicazione);
    }

    /**
     * @return null
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param null $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

    /**
     * @return \yii\db\ActiveRecord
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param \yii\db\ActiveRecord $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return null
     */
    public function getRegolaPubblicazione()
    {
        return $this->regolaPubblicazione;
    }

    /**
     * @param null $regolaPubblicazione
     */
    public function setRegolaPubblicazione($regolaPubblicazione)
    {
        $this->regolaPubblicazione = $regolaPubblicazione;
    }

    protected function renderEditori()
    {
        $configEditori = [
            'form' => $this->getForm(),
            'model' => $this->getModel(),
        ];

        $configEditori = ArrayHelper::merge($configEditori, $this->getDestinatari());

        return DestinatariNEW::widget($configEditori);
    }

    /**
     * @return null
     */
    public function getDestinatari()
    {
        return $this->destinatari;
    }

    /**
     * @param array $destinatari
     */
    public function setDestinatari($destinatari)
    {
        $this->destinatari = $destinatari;
    }

    protected function renderValidatori()
    {
        $configValidatori = [
            'form' => $this->getForm(),
            'model' => $this->getModel(),
        ];

        $configValidatori = ArrayHelper::merge($configValidatori, $this->getValidatori());

        return ValidatoriNEW::widget($configValidatori);
    }

    /**
     * @return null
     */
    public function getValidatori()
    {
        return $this->validatori;
    }

    /**
     * @param null $validatori
     */
    public function setValidatori($validatori)
    {
        $this->validatori = $validatori;
    }

    protected function renderRecipientsCheck()
    {
        $configRecipientsCheck = [
            'form' => $this->getForm(),
            'model' => $this->getModel(),
        ];
        return RecipientsCheckNEW::widget($configRecipientsCheck);
    }


    /**
     * @return string
     */
    protected function renderPreviewSign()
    {
        $model = $this->getModel();
        $profile = UserProfile::findOne(['user_id' => $model->created_by]);
        return $this->render('preview_sign', [
            'model' => $this->getModel(),
            'profile' => $profile
        ]);
    }

}