<?php
/**
 * Author: Andrew Monteith
 * Date: 11/12/14 10:08 AM
 * @package PanelSlider
 */
?>
<div id="parse-blurb" class="blurb bwi-hidden">
    <h1><?php echo __("Upload Success", 'better-wordpress-importer'); ?></h1>
</div>
<div id="ajax-parse-button" class="bwi-button">
    <div class="bwi-button-text"><?php echo __("Parse", 'better-wordpress-importer'); ?></div>
</div>
<div id="ajax-parse-return" class="ajax-return-value"></div>
<script type="text/javascript">
    function bwi_local_parse() {
        var parse_button = jQuery('#ajax-parse-button');
        //noinspection JSCheckFunctionSignatures
        parse_button.children(".bwi-button-text").text("Parsing ...");
        var ajax_file_url = "<?php echo plugins_url() . "/better-wordpress-importer/ajax/bwi_ajax.php"?>";
        var serverside_filename = jQuery('#serverside-filename').text();
        jQuery.ajax({
            url: ajax_file_url,
            data: {filename: serverside_filename, action: 'parse_xml'}
        }).done(function (html) {
            var return_val = jQuery('#ajax-parse-return');
            return_val.append(html);
            return_val.appendTo('#ajax-return-values');
            //noinspection JSCheckFunctionSignatures
            parse_button.children(".bwi-button-text").text("Done");
            panelSlider.bwi_jumpToSlideWithId('import_authors', true);
        });
    }
    jQuery("#ajax-parse-button").click(function () {
        bwi_local_parse();
    });
    jQuery("#parse_xml").on("slideFocused", function () {
        bwi_local_parse();
    });
</script>