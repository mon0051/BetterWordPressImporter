<?php
/**
 * Author: Andrew Monteith
 * Date: 13/11/14 2:41 AM
 * @package ui
 */
require_once 'PanelController.php';
$panelController = new PanelController();
?>
<div id="ajax-return-values" class="bwi-hidden"></div>
<div class="buffer"></div>
<!-- This Script controls the slider that displays the ui -->
<script type="text/javascript">
    var slideWidth = 680;
    var bwi_active_slide = 0;
    var bwi_slide_count = 0;
    var slider_transistion_time = 250;

    /**
     * @message A string to print out to the consolse
     * A simple helper to ensure logging does not crash program in old browsers
     * */

    function safeLog(message){
        if(window.console){
            console.log(message);
        }
    }
    /**
     * Updates the UI to show correct slide selected in the controls
     */
    function bwi_slide_select() {
        var page_selector = jQuery('#bwi-page-selector');
        page_selector.empty();
        for (var i = 0; i < bwi_slide_count; i++) {
            if (i == bwi_active_slide) {
                page_selector.append("<div class=\"page-selector-button\"><i class=\"fa fa-circle\"></i></div>");
            } else {
                page_selector.append("<div class=\"page-selector-button\"><i class=\"fa fa-circle-o\"></i></div>");
            }
        }
    }
    /**
     * Jumps to the slide given by slide number. If trigger default action is set to true
     * then the slide that is displayed will run it's default action immediatly on display
     * with no prompt
     * @param slide_number
     * @param trigger_default_action
     * @returns {boolean}
     */
    function bwi_jump_to_slide(slide_number,trigger_default_action = false){
        safeLog("bwi_jump_to_slide "+slide_number);
        var slide_offset = slide_number - bwi_active_slide;
        // Check if requires movement, then check if slide is in valid range
        if (slide_offset === 0) return true;
        if (slide_number > bwi_slide_count || slide_number < 0) return false;
        // Perform actual slide here
        jQuery('#bwi-slide-magazine').animate({left: "-="+slide_offset * slideWidth}, slider_transistion_time);
        // The next two lines properly set the current active slide
        bwi_active_slide = slide_number;
        bwi_slide_select();
        return true;
    }

    /**
     * Required to be called after a new slide or group of new slides is added to the magazine.
     * It ensures that slides do not overlap and are in the correct order.
     * It is only required to be called once after adding multiple slides.
     */
    function bwi_resetMagazine() {
        jQuery('#bwi-slide-magazine').children().css('left', function (index) {
            bwi_slide_count += 1;
            bwi_slide_select();
            return (index) * slideWidth;
        });
    }

    /**
     * Slides the magazine left if it can.
     * This will result in the panel to the right of the current slide becoming the  focus.
     * @returns {boolean}
     */
    function bwi_slideLeft() {
        if (bwi_active_slide < bwi_slide_count -1) {
            bwi_jump_to_slide(bwi_active_slide +1);
            return true;
        }
        return false;
    }
    /**
     * Slides the magazine right if it can.
     * This will result in the panel to the left of the current slide becoming the focus.
     * @returns {boolean}
     */
    function bwi_slideRight() {
        if (bwi_active_slide > 0) {
            bwi_jump_to_slide(bwi_active_slide -1);
        }
        return false;
    }
    /**
     * Jumps to a slide with a given id. A slide id will always be the same as the filename
     * of the panel (without the .php extension)
     * @param slideId
     */
    function bwi_jumpToSlideWithId(slideId){
        var jquery_selector = "#"+slideId;
        var panel_position = jQuery(jquery_selector).attr('data_position');
        bwi_jump_to_slide(panel_position);
        var magazine = jQuery('#bwi-slide-magazine');
        jquery_selector = "[data_position=" + bwi_active_slide +"]";
        safeLog("trigger"+jquery_selector);
        magazine.children(jquery_selector).trigger("slideFocused");
    }
</script>
<div id="bwi-panel-wrapper">
    <div id="bwi-slide-window">
        <div id="bwi-slide-magazine">
            <?php
            $panelController->add_panel('panels/check_session.php');
            $panelController->add_panel('panels/ajax_upload_panel.php');
            $panelController->add_panel('panels/parse_xml.php');
            $panelController->add_panel('panels/import_authors.php');
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
        bwi_slideRight();
    });
    jQuery('#bwi-slider-right-button').click(function () {
        bwi_slideLeft();
    });
    jQuery('#bwi-page-selector').on('click','.page-selector-button',function(){
        bwi_jump_to_slide(jQuery(this).index());
    });
    // resetMagazine now, all slides should be added
    bwi_resetMagazine();
</script>
