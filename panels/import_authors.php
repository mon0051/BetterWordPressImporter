<?php
/**
 * Author: Andrew Monteith
 * Date: 12/12/14 1:18 PM
 * @package panels
 */
?>

<div class="blurb">
    <h1>Import Authors</h1>
</div>
<div id="bwi_reload_authors" class="bwi-button"><div class="bwi-button-text">Refresh</div></div>
<form id="author_container">
</form>
<script>
    var ajax_file_url = "";
    ajax_file_url = "<?php echo plugins_url() . "/better-wordpress-importer/ajax/bwi_ajax.php"?>";
    function import_authors(){
        var reload_authors = jQuery("#bwi_reload_authors").children(".bwi-button-text");
        reload_authors.text("loading...");
        jQuery.ajax({url: ajax_file_url, data: {action:'read_authors'}}).done(function(response) {
            var author_container = jQuery('#author_container');
            author_container.children(".author_wrapper").remove();
            author_container.append(response);
            reload_authors.text('Refresh');
        });
    }
    jQuery('#import_authors').on("slideFocused" , function(){
        import_authors();
    });
    jQuery('#bwi_reload_authors').click(function(){
        import_authors();
    });
</script>