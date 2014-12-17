<?php
/**
 * Author: Andrew Monteith
 * Date: 13/11/14
 * @package ui
 */
/*
 * Unfortunately the JavaScript for this can't be enqueued as normal
 * as it requires PHP to modify the script at runtime in order for
 * the script to be accurate.
 * It would be possible to save these variables in a hidden <div>,
 * and retrieve them with jQuery, but that is both less secure
 * and more intensive on the client side. As this is an admin
 * function, server load should be minimal, hence my choice to
 * perform this server-side.
 */
require_once dirname(__FILE__) . '/../javascript_baker.php';
include_once dirname(__FILE__) . '/../../better-wordpress-importer-admin.php';
global $bwi_plugin_folder_url;
?>
<script type="text/javascript">

    bigUpload = new BigUpload();
    bigUpload.settings['scriptPath'] = "<?php echo $bwi_plugin_folder_url; ?>ajax/bwi_ajax.php";
    <?php bakeInPhpUploadLimits(); ?>
    function upload() {

        panelSlider.safeLog("called upload");
        bigUpload.fire();
    }
    function abort() {
        panelSlider.safeLog("called abort");
        bigUpload.abortFileUpload();
    }
    function OnProgress(event, position, total, percentComplete) {
        // Update Progress Bar
        panelSlider.safeLog(event+position+total);
        var progress_box = jQuery('#chunk-progress-box');
        progress_box.show();
        progress_box.find('#chunk-progress-bar').width(percentComplete + '%');
        var text_progress = percentComplete.toString() + "%";
        progress_box.find('#chunk-status-txt').text(text_progress);
    }
</script>
<form action="<?php echo $bwi_plugin_folder_url ?>ajax/bwi_ajax.php" id="AjaxFileUpload" method="POST"
      enctype="multipart/form-data">
    <h2>Select File To Import</h2>

    <div id="file-input-wrapper"><input type="file" id="bigUploadFile" name="FileInput"/></div>
    <div id="chunk-progress-box">
        <div id="chunk-progress-bar"></div>
        <div id="chunk-status-txt"></div>
    </div>
    <div id="progress-box">
        <div id="progress-bar"></div>
        <div id="status-txt"></div>
    </div>
    <div id="timeRemaining"></div>
    <div id="upload-button-wrapper"><input type="button" id="bigUploadSubmit" value="Start Upload" onclick="upload()" /></div>
</form>
<div id="bigUploadResponse"></div>


