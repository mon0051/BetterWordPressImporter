<?php
/**
 * Author: Andrew Monteith
 * Date: 3/01/15 12:16 PM
 */
include_once '../PanelController.php';
$ajax_url = plugins_url() . '/better-wordpress-importer/ajax/bwi_ajax.php';
?>

<div class="bwi-panel-header">
    <h1><?php echo __("Import Content", "better-wordpress-importer"); ?> </h1>
</div>
<div class="blurb">
</div>
<div class="bwi-center">
    <div class="bwi-button" id="bwi-rollback-refresh">
        <div class="bwi-button-text"><?php echo __("Refresh",'better-wordpress-importer'); ?></div>
    </div>
</div>
<div class="bwi-form-wrapper" id="bwi-rollback-form">

</div>
<script type="text/javascript">
    function bwi_import_content() {
        var ajax_file_url = "<?php echo $ajax_url;?>";
        panelSlider.safeLog("Import Ajax Started");
        var ajax_options = {
            url: ajax_file_url, type: "GET",
            data: {action: 'get_rollback_data'}
        };
        jQuery.ajax(ajax_options).done(function (response) {
            jQuery('#bwi-rollback-form').append(response);
        });
    }
    jQuery('#bwi-rollback-refresh').on("slideFocused", function () {
        bwi_import_content();
    });
    jQuery('#bwi-rollback-refresh').click(function () {
        bwi_import_content();
    });
</script>

