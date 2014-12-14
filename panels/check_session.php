<?php
/**
 * Author: Andrew Monteith
 * Date: 14/12/14 6:34 AM
 */ ?>
<div class="bwi-slide-wrapper" id="check_session">
    <div class="bwi-panel-header">
        <h1> Checking for current Import processe's...</h1>
    </div>
    <div class="blurb">
        <p>
            There can only be one import process going on per user, to avoid database conflicts.
            Checking to see if there is another process running...
        </p>
    </div>
    <div id="ajax-response-session-check">
        <div id="bwi_session_found" class="bwi-hidden">
            A previous session was found, do you want to continue with this import, or start a new import?
            <div id="bwi_start_new" class="bwi-button"><div class="bwi-button-text">Start New</div></div>
            <div id="bwi_resume" class="bwi-button"><div class="bwi-button-text">Start New</div></div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            var ajax_file_url = "<?php echo plugins_url() . "/better-wordpress-importer/ajax/session_check.php"?>";
            jQuery.ajax({url: ajax_file_url, data: { action:"first_contact" } }).done( function(html) {
                jQuery('#ajax-response-session-check').append(html);
                jQuery('#bwi_session_found').removeClass('bwi-hidden');
            });
        });
    </script>
</div>
