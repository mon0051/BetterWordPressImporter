<?php
/**
 * Author: Andrew Monteith
 * Date: 13/11/14
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
require 'javascript_baker.php';
include_once '../better-wordpress-importer-admin.php';
global $bwi_plugin_folder_url;
?>
<div class="bwi-slide-wrapper" id="upload-slide">
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            var options = {
                target: '#output',   // target element(s) to be updated with server response
                beforeSubmit: beforeSubmit,  // pre-submit callback
                success: afterSuccess,  // post-submit callback
                uploadProgress: OnProgress, //upload progress callback
                resetForm: false        // reset the form after successful submit
            };
            jQuery('#AjaxFileUpload').submit(function () {
                $(this).ajaxSubmit(options);
                return false;
            });
            function beforeSubmit() {
                if (window.File && window.FileReader && window.FileList && window.Blob) {
                    // mb is the size of a megabyte in bytes, needed for calculations as variables may be
                    // stored in bytes in the php.ini, and we will need to convert for readability
                    var mb = 1048576;
                    // By saving the jQuery object as a var, it will improve performance
                    // due to the fact that it will only have to search the DOM once for
                    // the form.
                    var file_input = $('#FileInput');
                    var fsize = file_input[0].files[0].size;
                    var ftype = file_input[0].files[0].type;
                    var max_upload_size = 2 * mb;
                    var post_max_size = 2 * mb;
                    <?php bakeInPhpUploadLimits();?>
                    // Allowed File Type
                    //if (ftype != 'text/xml') return false;
                    if (fsize > max_upload_size) {
                        var max_upload_size_readable = max_upload_size / mb;
                        alert(
                            "<p><b>" + fsize + "</b> is over your servers file upload size limit (" + max_upload_size_readable + " bytes ).</p>" +
                            "<p>You may need to contact your host about raising this limit.</p>");
                        return false;
                    }
                    if (fsize > post_max_size) {
                        var max_post_size_readable = post_max_size / mb;
                        alert(
                            "<p><b>" + fsize + "</b> is over your servers file upload size limit (" + max_post_size_readable + " bytes ).</p>" +
                            "<p>You may need to contact your host about raising this limit.</p>");
                        return false;
                    }
                    $('#submit-btn').hide(); //hide submit button
                    $("#output").html("");

                } else {
                    alert(
                        "<p>This Web Browser does not support some features this plugin requires.</p>" +
                        "<p>Try the latest version of Chrome or Firefox :)</p>"
                    );
                }
            }
            function OnProgress(event, position, total, percentComplete) {
                // Update Progress Bar
                var progress_box = $('#progress-box');
                progress_box.show();
                progress_box.find('#progress-bar').width(percentComplete + '%');
                var text_progress = percentComplete.toString() + "%";
                progress_box.find('#status-txt').text(text_progress);
            }
            function afterSuccess() {
                $('#submit-btn').hide(); //hide submit button
                $('#progress-box').delay(1000).fadeOut(); //hide progress bar
                $('#serverside-filename').prependTo('#ajax-return-values');
                slideLeft();
            }
        });
    </script>
    <form action="<?php echo $bwi_plugin_folder_url ?>ajax/processupload.php" id="AjaxFileUpload" method="POST"
          enctype="multipart/form-data">
        <h2>Select File To Import</h2>

        <div id="file-input-wrapper"><input type="file" id="FileInput" name="FileInput"/></div>
        <div id="progress-box">
            <div id="progress-bar">
                <div id="status-txt">0%</div>
            </div>
        </div>
        <div id="upload-button-wrapper"><input type="submit" id="submit-btn" value="Upload"></div>
    </form>

    <div id="output"></div>

</div>

