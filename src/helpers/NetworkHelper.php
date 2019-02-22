<?php

namespace lispa\amos\cwh\helpers;

use lispa\amos\cwh\helpers\base\BaseEntitiesHelper;

class NetworkHelper extends BaseEntitiesHelper
{
    public static function getEntities($interfaceClassname = '\lispa\amos\cwh\base\ModelNetworkInterface')
    {
        return parent::getEntities($interfaceClassname);
    }


}