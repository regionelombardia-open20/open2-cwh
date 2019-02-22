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

use lispa\amos\cwh\models\CwhTagInterestMm;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Class TagWidgetAreeInteresse
 * @package lispa\amos\cwh\widgets
 */
class TagWidgetAreeInteresse extends \yii\widgets\InputWidget
{
    public $form;
    public $contentsTrees = [];
    public $contentsTreesSimple = [];

    /**
     * @inheritdoc
     */
    public $name = 'interestTagValues';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->contentsTrees = $this->fetchContentsTrees();
    }

    /**
     * Return all roots
     * @return array
     */
    private function fetchContentsTrees()
    {
        $contentsTrees = [];

        $moduleTag = Yii::$app->getModule('tag');
        if(isset($moduleTag)) {
            $contents = Yii::$app->getModule('cwh')->modelsEnabled;

            foreach ($contents as $content) {
                $refClass = new \ReflectionClass($content);

                $id_user = null;
                if (Yii::$app->getModule('admin')->modelMap['UserProfile'] == get_class($this->model)) {
                    $id_user = $this->model['user_id'];
                } else {
                    $id_user = \Yii::$app->getUser()->getId();
                }

                //query di recupero dei tags
                /** @var ActiveQuery $query */
                $query = \lispa\amos\tag\models\Tag::find()
                    ->joinWith('cwhTagInterestMm')
                    ->joinWith('tagModelsAuthItems')
                    ->andWhere([
                        \lispa\amos\tag\models\TagModelsAuthItemsMm::tableName() . '.classname' => $content,
                        CwhTagInterestMm::tableName() . '.classname' => get_class($this->model),
                        CwhTagInterestMm::tableName() . '.auth_item' => array_keys(\Yii::$app->authManager->getRolesByUser($id_user))
                    ]);

                if ($query->count()) {
                    $contentsTree['label'] = $refClass->getShortName();
                    $contentsTree['classnameRef'] = $refClass->getShortName();
                    $contentsTree['classname'] = $content;
                    $contentsTree['trees'] = $query->asArray()->all();
                    $contentsTrees[] = $contentsTree;
                    $this->contentsTreesSimple = $this->contentsTreesSimple + ArrayHelper::map($query->all(), 'id',
                            'nome');
                }

            }
        }
        return $contentsTrees;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $moduleTag = Yii::$app->getModule('tag');
        if(isset($moduleTag)) {
            return $this->render('tag', [
                'model' => $this->model,
                'form' => $this->form,
                'name' => $this->name,
                'contentsTrees' => $this->contentsTrees,
                'contentsTreesSimple' => $this->contentsTreesSimple,
                'tags_selected' => $this->getTagsSelected(),
                'limit_trees' => $this->getLimitTrees()
            ]);
        }
        return '';
    }

    /**
     * i tag selezionati per il record in esame
     * @return array
     */
    private function getTagsSelected()
    {
        //data la tabella delle mm tra record e oggetti, recupera le row
        //dell'oggetto per il model in esame
        $listaTagId = \lispa\amos\cwh\models\CwhTagOwnerInterestMm::findAll([
            'classname' => get_class($this->model),
            'record_id' => $this->model->id
        ]);

        $ret = [];
        foreach ($listaTagId as $tag) {
            //recupera il tag
            $tagObj = $this->getTagById($tag->tag_id);
            if (is_null($tagObj)) {
                continue;
            }

            //identifica l'id dell'albero
            $id_tree = $tagObj->root;

            //verifica se esiste già il riferimento alla categoria in esame
            //e nel caso la crea
            if (!array_key_exists($tag->interest_classname, $ret)) {
                $ret[$tag->interest_classname] = [];
            }

            //verifica se esiste già il riferimento per l'albero in esame
            //e nel caso la crea
            if (!array_key_exists("tree_" . $id_tree, $ret[$tag->interest_classname])) {
                $ret[$tag->interest_classname]["tree_" . $id_tree] = [];
            }

            //aggiunge il tag nell'elenco dell'albero relativo
            $ret[$tag->interest_classname]["tree_" . $id_tree][] = [
                "id" => $tagObj->id,
                "label" => $tagObj->nome,
            ];
        }

        return $ret;
    }

    /**
     * @param $tagId
     * @return \lispa\amos\tag\models\Tag
     */
    private function getTagById($tagId)
    {
        return \lispa\amos\tag\models\Tag::findOne($tagId);
    }

    /**
     * @return array
     */
    private function getLimitTrees()
    {
        $array_limit_trees = [];

        foreach ($this->contentsTreesSimple as $id_tree => $label_tree) {
            //limite di default: nessun limite
            $limit_tree = false;

            //carica il nodo radice
            $root_node = $this->getTagById($id_tree);

            //se è presente un limite impostato per questa radice allora lo usa
            if ($root_node->limit_selected_tag && is_numeric($root_node->limit_selected_tag)) {
                $limit_tree = $root_node->limit_selected_tag;
            }

            $array_limit_trees["tree_" . $id_tree] = $limit_tree;
        }

        return $array_limit_trees;
    }

    /**
     *
     * @return array tutte le root
     */
    private function fetchRoles()
    {
        $moduleTag = Yii::$app->getModule('tag');
        if(isset($moduleTag)) {
            /**@var ActiveQuery $query * */
            $query = \lispa\amos\tag\models\Tag::find()->joinWith('cwhTagInterestMm')->andWhere(['auth_item' => array_keys(\Yii::$app->authManager->getRolesByUser(\Yii::$app->getUser()->getId()))]);

            return $query->all();
        }
        return null;
    }
}
