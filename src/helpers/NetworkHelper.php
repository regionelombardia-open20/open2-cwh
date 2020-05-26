<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    Open20Package
 * @category   CategoryName
 */

namespace open20\amos\cwh\helpers;

use open20\amos\cwh\helpers\base\BaseEntitiesHelper;

class NetworkHelper extends BaseEntitiesHelper
{
    public static function getEntities($interfaceClassname = '\open20\amos\cwh\base\ModelNetworkInterface')
    {
        return parent::getEntities($interfaceClassname);
    }


}