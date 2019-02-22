<?php

namespace lispa\amos\cwh\helpers;

use lispa\amos\cwh\helpers\base\BaseEntitiesHelper;

class ContentHelper extends BaseEntitiesHelper
{
    public static function getEntities($interfaceClassname = '\lispa\amos\core\interfaces\ContentModelInterface')
    {
        return parent::getEntities($interfaceClassname);
    }

}