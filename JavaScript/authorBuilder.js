/**
 * Created by mon on 18/12/14.
 */
function AuthorBuilder(){
    var aObj = this;
    this.authorName = '';
    this.authorId = -1;
    /**
     * Generates a section of a form pertaining to one user
     * @param author_container_id The container in which to place the authors
     */
    this.buildForm = function (author_container_id){
        var author = jQuery("<div class=\"author-wrapper\"></div>");

        var author_name = jQuery("<div class=\"author-name\"></div>").text(aObj.authorName);
        author_name.appendTo(author);
        // Radio Buttons
        var radioButtons = jQuery("<div class=\"bwi-radio-wrapper\"></div>");
        var radioImport = jQuery("<input type=\"radio\" value=\"import\">").attr('name','radio-'+aObj.authorId);
        var radioNew = jQuery("<input type=\"radio\" value=\"new\">").attr('name','radio-'+aObj.authorId);
        var radioExisting = jQuery("<input type=\"radio\" value=\"existing\">").attr('name','radio-'+aObj.authorId);
        radioImport.appendTo(radioButtons);
        radioNew.appendTo(radioButtons);
        radioExisting.appendTo(radioButtons);
        radioButtons.appendTo(author);
        author.appendTo(jQuery(author_container_id));
    }
}