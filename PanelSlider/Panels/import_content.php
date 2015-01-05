<?php
/**
 * Author: Andrew Monteith
 * Date: 3/01/15 12:16 PM
 */
include_once '../PanelController.php';
$ajax_url = plugins_url() . '/better-wordpress-importer/ajax/bwi_ajax.php';
?>
<div class="bwi-slide-wrapper" id="import_content.php">
    <div class="bwi-panel-header">
        <h1><?php echo __("Import Content", "better-wordpress-importer"); ?> </h1>
    </div>
    <div class="blurb">
    </div>
    <div class="bwi-center">
        <div class="bwi-button" id="bwi-import-content">
            <div class="bwi-button-text">Import</div>
        </div>
    </div>
    <script type="text/javascript">
        function bwi_import_content(){
            var ajax_file_url = "<?php echo $ajax_url;?>"
            panelSlider.safeLog("Import Ajax Started");
            var ajax_options = {
                url: ajax_file_url, type: "POST",
                data: {action: 'import_content'}
            };
            jQuery.ajax(ajax_options).done(function(response){
                panelSlider.safeLog("Import Ajax Finished" + response);
            });
        }
        jQuery('#import_content').on("slideFocused", function () {
            bwi_import_content();
        });
        jQuery('#bwi-import-content').click(function () {
            bwi_import_content();
        });
    </script>
</div>
