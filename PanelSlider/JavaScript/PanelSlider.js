/**
 * Created by Andrew Monteith on 18/12/14.
 */
function PanelSlider() {
    var ps = this;
    this.slideWidth = 680;
    this.bwi_active_slide = 0;
    this.bwi_slide_count = 0;
    this.slider_transistion_time = 250;


    /**
     * @message A string to print out to the console
     * A simple helper to ensure logging does not crash program in old browsers
     * */

    this.safeLog = function (message) {
        if (window.console) {
            console.log(message);
        }
    };
    /**
     * Updates the PanelSlider to show correct slide selected in the controls
     */
    this.bwi_slide_select = function () {
        var page_selector = jQuery('#bwi-page-selector');
        page_selector.empty();
        for (var i = 0; i < ps.bwi_slide_count; i++) {
            if (i == ps.bwi_active_slide) {
                page_selector.append("<div class=\"page-selector-button\"><i class=\"fa fa-circle\"></i></div>");
            } else {
                page_selector.append("<div class=\"page-selector-button\"><i class=\"fa fa-circle-o\"></i></div>");
            }
        }
    };
    /**
     * Jumps to the slide given by slide number. If trigger default action is set to true
     * then the slide that is displayed will run it's default action immediatly on display
     * with no prompt
     * @param slide_number
     * @param trigger_default_action
     * @returns {boolean}
     */
    this.bwi_jump_to_slide = function (slide_number, trigger_default_action) {
        // Sets the trigger default action to false if it's not set
        trigger_default_action = typeof trigger_default_action !== 'undefined' ? trigger_default_action : false;
        ps.safeLog("bwi_jump_to_slide " + slide_number);
        var slide_offset = slide_number - ps.bwi_active_slide;
        // Check if requires movement, then check if slide is in valid range
        if (slide_offset === 0) {
            return true;
        }
        if (slide_number > ps.bwi_slide_count || slide_number < 0) {
            return false;
        }
        // Perform actual slide here
        var bwiMagazine = jQuery('#bwi-slide-magazine');
        bwiMagazine.animate({left: "-=" + slide_offset * ps.slideWidth}, ps.slider_transistion_time);
        // The next two lines properly set the current active slide
        ps.bwi_active_slide = slide_number;
        ps.bwi_slide_select();
        if (trigger_default_action === true) {
            bwiMagazine.children('.bwi-slide-wrapper[data_position=' + ps.bwi_active_slide + ']').trigger('slideDisplay');
        }
        return true;
    };

    /**
     * Required to be called after a new slide or group of new slides is added to the magazine.
     * It ensures that slides do not overlap and are in the correct order.
     * It is only required to be called once after adding multiple slides.
     */
    this.bwi_resetMagazine = function () {
        jQuery('#bwi-slide-magazine').children().css('left', function (index) {
            ps.bwi_slide_count += 1;
            ps.bwi_slide_select();
            return (index) * ps.slideWidth;
        });
    };

    /**
     * Slides the magazine left if it can.
     * This will result in the panel to the right of the current slide becoming the  focus.
     * @returns {boolean}
     */
    this.bwi_slideLeft = function () {
        if (ps.bwi_active_slide < ps.bwi_slide_count - 1) {
            ps.bwi_jump_to_slide((ps.bwi_active_slide + 1), false);
            return true;
        }
        return false;
    };
    /**
     * Slides the magazine right if it can.
     * This will result in the panel to the left of the current slide becoming the focus.
     * @returns {boolean}
     */
    this.bwi_slideRight = function () {
        if (ps.bwi_active_slide > 0) {
            ps.bwi_jump_to_slide(ps.bwi_active_slide - 1, false);
        }
        return false;
    };
    /**
     * Jumps to a slide with a given id. A slide id will always be the same as the filename
     * of the panel (without the .php extension)
     * @param slideId
     * @param trigger_default_action If true, will trigger the slides default action immediatly on arrival
     */
    this.bwi_jumpToSlideWithId = function (slideId, trigger_default_action) {
        trigger_default_action = typeof trigger_default_action !== 'undefined' ? trigger_default_action : false;
        var jquery_selector = "#" + slideId;
        var panel_position = jQuery(jquery_selector).attr('data_position');
        ps.bwi_jump_to_slide(panel_position, trigger_default_action);
        var magazine = jQuery('#bwi-slide-magazine');
        jquery_selector = "[data_position=" + ps.bwi_active_slide + "]";
        ps.safeLog("trigger" + jquery_selector);
        magazine.children(jquery_selector).trigger("slideFocused");
    }
}
panelSlider = new PanelSlider();