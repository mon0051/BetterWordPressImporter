<?php
/**
 * Author: Andrew Monteith
 * Date: 13/11/14 2:41 AM
 */
require_once 'PanelController.php';
$panelController = new PanelController();
?>
<div id="ajax-return-values" class="bwi-hidden"></div>
<div class="buffer"></div>
<!-- This Script controls the slider that displays the panels -->
<script type="text/javascript">
    // This line of code just shuts my inspection tools up about not finding jQuery
    // which it can't find due to it being a dynamic inclusion I will remove it in
    // the final version
    jQuery = jQuery;
    // This code is very fast, no need for it to be in footer
    var slideWidth = 680;
    var bwi_active_slide = 0;
    // This variable keeps track of how many slides there are client side
    var bwi_slide_count = 0;
    // Reset Magazine is used to ensure that panels are in the correct order
    var slider_transistion_time = 250;
    function safeLog(message){
        if(window.console){
            console.log(message);
        }
    }
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
    function bwi_jump_to_slide(slide_number){
        safeLog("bwi_jump_to_slide "+slide_number);
        var slide_offset = slide_number - bwi_active_slide;
        if (slide_offset == 0) {
            return true;
        }
        if(slide_number > bwi_slide_count || slide_number < 0) {
            return false;
        }
        var slide_pixel_offset = slide_offset * slideWidth;
        var jQueryParameter = "-=";
        jQueryParameter = jQueryParameter + slide_pixel_offset;
        jQuery('#bwi-slide-magazine').animate({left: jQueryParameter}, slider_transistion_time);
        bwi_active_slide = slide_number;
        bwi_slide_select();
        return true;
    }
    function resetMagazine() {
        jQuery('#bwi-slide-magazine').children().css('left', function (index) {
            bwi_slide_count += 1;
            bwi_slide_select();
            return (index) * slideWidth;
        });
    }
    // MOVE the slide left, the effect is that the slide to the RIGHT will become in focus
    function slideLeft() {
        if (bwi_active_slide < bwi_slide_count -1) {
            safeLog("slideLeft");
            var magazine = jQuery('#bwi-slide-magazine');
            magazine.animate({left: "-=680"}, slider_transistion_time);
            bwi_active_slide += 1;
            bwi_slide_select();
            var jquery_selector = "[data_position=" + bwi_active_slide +"]";
            magazine.children(jquery_selector).trigger("slideFocused");
            return true;
        }
        return false;
    }
    // MOVE the slide right, the effect is that the slide to the LEFT will become in focus
    function slideRight() {
        if (bwi_active_slide > 0) {
            safeLog("slideRight");
            var magazine = jQuery('#bwi-slide-magazine');
            magazine.animate({left: "+=680"}, slider_transistion_time);
            bwi_active_slide -= 1;
            bwi_slide_select();
            var jquery_selector = "[data_position=" + bwi_active_slide +"]";
            magazine.children(jquery_selector).trigger("slideFocused");
            return true;
        }
        return false;
    }
    function jumpToSlideWithId(slideId){
        var jquery_selector = "#"+slideId;
        var panel_position = jQuery(jquery_selector).attr('data_position');
        bwi_jump_to_slide(panel_position);
    }
</script>
<div id="bwi-panel-wrapper">
    <div id="bwi-slide-window">
        <div id="bwi-slide-magazine">
            <?php
            $panelController->add_panel('check_session.php');
            $panelController->add_panel('ajax_upload_panel.php');
            $panelController->add_panel('parse_xml.php');
            $panelController->add_panel('import_authors.php');
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
        slideRight();
    });
    jQuery('#bwi-slider-right-button').click(function () {
        slideLeft();
    });
    jQuery('#bwi-page-selector').on('click','.page-selector-button',function(){
        bwi_jump_to_slide(jQuery(this).index());
    });
    // resetMagazine now, all slides should be added
    resetMagazine();
</script>
