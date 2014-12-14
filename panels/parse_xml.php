<?php
/**
 * Author: Andrew Monteith
 * Date: 11/12/14 10:08 AM
 */
//include_once '../parser/parser.php';
?>
<div class="bwi-slide-wrapper" id="parse_xml_slide">
    <div id="parse-blurb" class="blurb">
        <h1>Upload Success</h1>

        <p>Seems like the file has uploaded to the server correctly, that's a good start!</p>

        <p>Now we need to try and read the file. If there is a problem with the export file, this is where it will be
            picked up.</p>

        <p>Click on the Parse button (that's computer speak for read) to start the process. This should take no more
            than 30 seconds if the file is properly exported.</p>
    </div>
    <div id="ajax-parse-button">
        <div class="bwi-button-text">Parse</div>
    </div>
    <div id="ajax-parse-return" class="ajax-return-value"></div>
    <script type="text/javascript">
        jQuery('#ajax-parse-button').click(function () {
            var parse_button = jQuery('#ajax-parse-button');
            parse_button.children(".bwi-button-text").text("Parsing ...");
            //noinspection JSUnusedAssignment
            var ajax_file_url = "";
            ajax_file_url = "<?php echo plugins_url() . "/better-wordpress-importer/ajax/ajax-xml-parse.php"?>";
            var serverside_filename = jQuery('#serverside-filename').text();
            jQuery.ajax({url: ajax_file_url, data: {filename: serverside_filename}}).done(function (html) {
                var return_val = jQuery('#ajax-parse-return');
                return_val.append(html);
                return_val.appendTo('#ajax-return-values');
                parse_button.children(".bwi-button-text").text("Done");
                slideLeft();
                //parse_button.click(function (){return false;});
            });
        });
    </script>
</div>