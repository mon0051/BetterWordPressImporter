<?php
/**
 * Author: Andrew Monteith
 * Date: 14/12/14 6:34 AM
 * @package UI
 */ ?>

<div class="bwi-panel-header">
    <h1> Checking for current Import processe's...</h1>
</div>
<div class="blurb">
    <p>
        Checking to see if there is another process running...
    </p>
</div>
<div id="ajax-response-session-check">
    <div id="bwi_session_found" class="bwi-hidden bwi-center">
        <p>A previous session was found, do you want to continue with this import, or start a new import?</p>

        <div id="bwi_start_new" class="bwi-button">
            <div class="bwi-button-text">New Import</div>
        </div>
        <div id="bwi_resume" class="bwi-button">
            <div class="bwi-button-text">Resume</div>
        </div>
    </div>
    <div id="bwi_no_session_found" class="bwi-hidden bwi-center">
        <p>No Session was found</p>

        <div id="bwi_retry_session" class="bwi-button">
            <div class="bwi-button-text">Retry</div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var session_check_response = "";
    var bwi_session_check_first = true;
    /**
     * Checks to see if a import session has already been started, and
     * displays options to resume the session if that is the case.
     */
    function check_session() {
        var ajax_file_url = "<?php echo plugins_url() . "/better-wordpress-importer/ajax/bwi_ajax.php"?>";
        jQuery.ajax({url: ajax_file_url, data: {action: "first_contact"}}).done(function (html) {
            session_check_response = html;

            if (html == "no_session") {
                jQuery('#bwi_no_session_found').toggleClass('bwi-hidden', false);
                jQuery('#bwi_session_found').toggleClass("bwi-hidden", true);
                if(bwi_session_check_first){
                    bwi_session_check_first = false;
                    panelSlider.bwi_slideLeft();
                }else{
                    jQuery('#bwi_start_new').appendTo('#bwi_no_session_found');
                }
            } else {
                jQuery('#bwi_no_session_found').toggleClass('bwi-hidden', true);
                jQuery('#bwi_session_found').toggleClass("bwi-hidden", false);
                jQuery('#bwi_start_new').appendTo('#bwi_session_found');
            }
        });
    }
    jQuery(document).ready(function () {
        check_session();
    });
    jQuery('#bwi_start_new').click(function () {
        var ajax_file_url = "<?php echo plugins_url() . "/better-wordpress-importer/ajax/bwi_ajax.php"?>";
        jQuery.ajax({url: ajax_file_url, data: {action: "delete_session"}}).done(function () {
            bwi_session_check_first = false;
            check_session();
        });
        panelSlider.bwi_slideLeft();
    });
    jQuery('#bwi_retry_session').click(function () {
        check_session();
    });
    jQuery('#bwi_resume').click(function() {
        panelSlider.bwi_jumpToSlideWithId("import_authors",true);
    });
</script>

