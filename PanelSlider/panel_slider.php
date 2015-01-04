<?php
/**
 * Author: Andrew Monteith
 * Date: 13/11/14 2:41 AM
 * @package PanelSlider
 */
require_once 'PanelController.php';
$panelController = new PanelController();
?>
<div id="ajax-return-values" class="bwi-hidden"></div>
<div class="buffer"></div>
<!-- This Script controls the slider that displays the PanelSlider -->

<div id="bwi-panel-wrapper">
    <div id="bwi-slide-window">
        <div id="bwi-slide-magazine">
            <?php
            $panelController->add_panel('Panels/check_session.php');
            $panelController->add_panel('Panels/ajax_upload_panel.php');
            $panelController->add_panel('Panels/parse_xml.php');
            $panelController->add_panel('Panels/import_authors.php');
            $panelController->add_panel('Panels/import_content.php')
            ?>
        </div>
    </div>
    <div id="bwi-slider-controls">
        <div id="bwi-slider-left-button"><a class="fa fa-caret-left"></a></div>
        <div id="bwi-page-selector"></div>
        <div id="bwi-slider-right-button"><a class="fa fa-caret-right"></a></div>
    </div>
</div>
<script type="text/javascript">
    // Simply adding the onClick handlers to the control buttons
    jQuery('#bwi-slider-left-button').click(function () {
        panelSlider.bwi_slideRight();
    });
    jQuery('#bwi-slider-right-button').click(function () {
        panelSlider.bwi_slideLeft();
    });
    jQuery('#bwi-page-selector').on('click', '.page-selector-button', function () {
        panelSlider.bwi_jump_to_slide(jQuery(this).index(), false);
    });
    // resetMagazine now, all slides should be added
    panelSlider.bwi_resetMagazine();
</script>
