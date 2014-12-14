<?php
/**
 * Author: Andrew Monteith
 * Date: 13/11/14 2:41 AM
 */
require_once 'PanelController.php';
?>
<div id="ajax-return-values" class="bwi-hidden"></div>
<div class="buffer"></div>
<!-- This Script controls the slider that displays the panels -->
<script type="text/javascript">
    // This code is very fast, no need for it to be in footer
    var slideWidth = 680;
    var bwi_active_slide = 0;
    // This variable keeps track of how many slides there are client side
    var bwi_slide_count = 0;
    // Reset Magazine is used to ensure that panels are in the correct order
    var slider_transistion_time = 250;
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
        var slide_offset = slide_number - bwi_active_slide;
        if (slide_offset == 0) return true;
        if(slide_number > bwi_slide_count || slide_number < 0) return false;
        var slide_pixel_offset = slide_offset * slideWidth;
        var jQueryParameter = "-=";
        var string_version = slide_pixel_offset.toString();
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
            jQuery('#bwi-slide-magazine').animate({left: "-=680"}, slider_transistion_time);
            bwi_active_slide += 1;
            bwi_slide_select();
            return true;
        }
        return false;
    }
    // MOVE the slide right, the effect is that the slide to the LEFT will become in focus
    function slideRight() {
        if (bwi_active_slide > 0) {
            jQuery('#bwi-slide-magazine').animate({left: "+=680"}, slider_transistion_time);
            bwi_active_slide -= 1;
            bwi_slide_select();
            return true;
        }
        return false;
    }
</script>
<?php $panelConroller = new PanelController(); ?>
<div id="bwi-panel-wrapper">
    <div id="bwi-slide-window">
        <div id="bwi-slide-magazine">
            <?php
            $panelConroller->add_panel('check_session.php');
            $panelConroller->add_panel('ajax_upload_panel.php');
            $panelConroller->add_panel('parse_xml.php');
            $panelConroller->add_panel('import_authors.php');
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
