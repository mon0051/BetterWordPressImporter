<?php
/**
 * Author: Andrew Monteith
 * Date: 12/12/14 1:18 PM
 * @package UI
 */
?>

<div class="blurb">
    <h1>Import Authors</h1>
</div>
<div id="bwi_reload_authors" class="bwi-button">
    <div class="bwi-button-text">Refresh</div>
</div>
<?php include dirname(__FILE__) . '../../Elements/import_user_template.php'; ?>
<div id="author-form-container">
</div>
<script>
    var ajax_file_url = "";
    // bake the correct url into the script
    ajax_file_url = "<?php echo plugins_url() . "/better-wordpress-importer/ajax/bwi_ajax.php"?>";
    /**
     *  Reads the authors found on the server and creates a form from that data
     **/
    function import_authors() {
        // Button Management
        var reload_authors_button = jQuery("#bwi_reload_authors").children(".bwi-button-text");
        reload_authors_button.text("loading...");

        // Read author values from server (As a JSON object) , then construct a form from those authors
        jQuery.ajax({url: ajax_file_url, data: {action: 'get_authors_form'}}).done(function (author_form) {
            var form_wrapper = jQuery('#author-form-container');
            panelSlider.safeLog("response = " + author_form);
            form_wrapper.empty();
            form_wrapper.append(author_form);
            reload_authors_button.text('Refresh');
        });
    }

    jQuery('#import_authors').on("slideFocused", function () {
        import_authors();
    });
    jQuery('#bwi_reload_authors').click(function () {
        import_authors();
    });
</script>