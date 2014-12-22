<?php
/**
 * Author: Andrew Monteith
 * Date: 12/12/14 1:18 PM
 * @package PanelSlider
 */
$ajax_url = plugins_url() . '/better-wordpress-importer/ajax/bwi_ajax.php';
?>
<div class="blurb">
    <h1>Import Authors</h1>
</div>
<div id="bwi_reload_authors" class="bwi-button">
    <div class="bwi-button-text">Refresh</div>
</div>
<?php //include dirname(__FILE__) . '../../Elements/import_user_template.php'; ?>
<form class="bwi-form" id="bwi-import-author-form"
      action="<?php echo $ajax_url; ?>">
</form>
<script>
    var ajax_file_url = "<?php echo $ajax_url;?>";
    /**
     *  Reads the authors found in the import file and creates a form bsed on that data
     *  so they can be imported or overwritten
     **/
    function bwi_read_authors() {
        var reload_authors_button = jQuery("#bwi_reload_authors").children(".bwi-button-text");
        reload_authors_button.text("loading...");
        /*
        *   Download the user import form via ajax
        */
        jQuery.ajax({url: ajax_file_url, data: {action: 'get_authors_form'}}).done(function (author_form) {
            var form_wrapper = jQuery('#bwi-import-author-form');
            form_wrapper.empty();
            form_wrapper.append(author_form);
            reload_authors_button.text('Refresh');
            jQuery("#bwi-submit-author-form").click(function () {
                var iaform_data = readAuthorForm();
                var ajax_options = {
                    url: ajax_file_url, type: "POST",
                    data: {formdata: iaform_data, action: 'post-author-import-form'}
                };
                jQuery.ajax(ajax_options).done(function (html) {
                    panelSlider.safeLog("Ajax Done </br>" + html);
                });
            });
        });
    }
    /**
     * Parses the author form into an array
     * @return authorData an array eg.
     * [    0 : { id: a, opt: b,new_value: c, exisiting_author: d, username: e },
     *      1 : { id: f, opt: g,new_value: h, exisiting_author: i, username: j }   ];
     */
    function readAuthorForm() {
        var authors = jQuery("#bwi-import-author-form").find(".author-wrapper");
        var authorData = [];
        authors.each(function () {
            var author_id = jQuery(this).find(".bwi-author-id").val();
            var a_option = jQuery(this).find('select.author-selector option:selected').val();
            var new_author_value = jQuery(this).find('.author-new-input-wrapper input').val();
            var existing_author_value = jQuery(this).find('.author-select-wrapper select option:selected').val();
            authorData.push({
                aid: author_id,
                option: a_option,
                new_value: new_author_value,
                exisiting_author: existing_author_value
            });
        });
        return authorData;
    }
    /*
    * Add Handlers to the panel
    */
    jQuery('#import_authors').on("slideFocused", function () {
        bwi_read_authors();
    });
    jQuery('#bwi_reload_authors').click(function () {
        bwi_read_authors();
    });
</script>