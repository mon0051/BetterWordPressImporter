<?php
/**
 * Author: Andrew Monteith
 * Date: 13/11/14 2:41 AM
 */

require_once 'PanelController.php';
?>
<div id="ajax-return-values"></div>
<div class="buffer"></div>
<!-- This Script controls the slider that displays the panels -->
<script type="text/javascript">
    var bwi_active_slide = 0;

    // This variable keeps track of how many slides there are client side
    var bwi_slide_count = 0;
    // Reset Magazine is used to ensure that panels are in the correct order
    function bwi_slide_select(){
        var page_selector = jQuery('#bwi-page-selector');
        page_selector.empty();
        for (var i=0;i<bwi_slide_count;i++) {
            if(i==bwi_active_slide){
                page_selector.append("<div class=\"page-selector-button\"><i class=\"fa fa-circle\"></i></div>");
            }else {
                page_selector.append("<div class=\"page-selector-button\"><i class=\"fa fa-circle-o\"></i></div>");
            }
        }
    }
    function resetMagazine() {
        jQuery('#bwi-slide-magazine').children().css('left', function (index) {
            bwi_slide_count +=1;
            bwi_slide_select();
            return (index) * 680;
        });
    }
    // MOVE the slide left, the effect is that the slide to the RIGHT will become in focus
    function slideLeft() {
        if (bwi_active_slide < bwi_slide_count) {
            jQuery('#bwi-slide-magazine').animate({left: "-=680"}, 1000);
            bwi_active_slide += 1;
            bwi_slide_select();
            return true;
        }
        return false;
    }
    // MOVE the slide right, the effect is that the slide to the LEFT will become in focus
    function slideRight() {
        if (bwi_active_slide > 0) {
            jQuery('#bwi-slide-magazine').animate({left: "+=680"}, 1000);
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
        <div id="bwi-slider-left-button"><i class="fa fa-caret-left"></i></div>
        <div id="bwi-page-selector"></div>
        <div id="bwi-slider-right-button"><i class="fa fa-caret-right"></i></div>
    </div>
</div>

<script type="text/javascript">
    jQuery('#bwi-slider-left-button').click(function() {
        slideRight();
    });
    jQuery('#bwi-slider-right-button').click(function(){
        slideLeft();
    });
    resetMagazine();
</script>
