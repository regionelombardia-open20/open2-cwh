<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\cwh
 * @category   CategoryName
 */
\open20\amos\cwh\assets\CwhAsset::register($this);

use open20\amos\cwh\AmosCwh;
?>


<div class='row'>
  <h3 class="subtitle-section-form"><?= AmosCwh::t('amoscwh', '#subtitle_section_form'); ?></h3>
  <div class="col-md-4 col-xs-12">{validatori}</div>
  <div class='col-md-4 col-xs-12'>{previewSign}</div>
  <div class="clearfix"></div>
  <div class="col-md-4 col-xs-12">{destinatari}</div>
  <div class='col-md-4 col-xs-12'>{regolaPubblicazione}</div>
</div>
