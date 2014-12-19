<?php
/**
 * Author: Andrew Monteith
 * Date: 18/12/14 10:29 AM
 */
?>
<div class="author-wrapper bwi-hidden" id="bwi-author-template">
    <input type="hidden" name="author-id">
    <div class="author-import-options-wrapper">
        <div class="bwi-input-wrapper">
            <label for="author-import-options-import">Import Author</label>
            <input type="radio" name="author-import-option" value="author-import" class="bwi-author-radio-import">
        </div>
        <div class="bwi-input-wrapper">
            <label for="author-import-options-new">New Author</label>
            <input type="radio" name="author-import-option" value="author-new" class="bwi-author-radio-new">
        </div>
        <div class="bwi-input-wrapper">
            <label for="author-import-options-existing">Use Existing</label>
            <input type="radio" name="author-import-option" value="author-existing" class="bwi-author-radio-existing">
        </div>
    </div>
    <div class="author-input-wrapper">
        <div class="author-new-input-wrapper">
            <label for="new-author-input">Create New Authors</label>
            <input type="text" name="new-author-input" value="new-author-name">
        </div>
        <div class="author-select-wrapper bwi-hidden">
            <label for="existing-author-selector">Assign Existing Author</label>
            <select name="existing-author-selector" id="existing-author-selector"></select>
        </div>
    </div>
</div>
