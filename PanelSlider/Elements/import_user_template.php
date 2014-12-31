<?php
/* *****************************
 *  The template for the author import form
 ******************************/
?>
<div class="bwi-input-area">
    <div class="bwi-form-header">
        <div class="bwi-author bwi-header">Author</div>
        <div class="bwi-action bwi-header">Action</div>
    </div>
    <?php
    foreach ($authors as $author) :
        /** @var  WxrAuthor $author */
        $id = $author->author_id;
        $name = $author->author_display_name;
        $username = $author->author_login;
        $email = $author->author_email;
        ?>
        <div class="author-wrapper" id="bwi-author-template">
            <input type="hidden" class="bwi-author-id" name="author-id" value="<?php echo $id;?>">
            <input type="hidden" name="action" value="post-author-import-form">
            <div class="bwi-author-name-wrapper bwi-author">
                <div class="bwi-author-name"><?php echo $name;?></div>
            </div>
            <div class="bwi-action">
                <label for="author-import-option-selector-<?php echo $id;?>"></label>
                <select name="author-import-option-selector-<?php echo $id;?>"
                        id="author-import-option-selector-<?php echo $id;?>"
                        class="author-selector">
                    <option value="import">Import Author</option>
                    <option value="import">Create New Author</option>
                    <option value="import">Use Existing Author</option>
                </select>
            </div>
            <div class="author-input-wrapper bwi-hidden">
                <div class="author-new-input-wrapper">
                    <label for="new-author-input-<?php echo $id;?>">New Name</label>
                    <input type="text" name="new-author-input-<?php echo $id;?>" value="<?php echo $name;?>"
                           id="new-author-input-<?php echo $id;?>">
                </div>
                <div class="author-select-wrapper bwi-hidden">
                    <label for="existing-author-selector-<?php echo $id;?>">Assign Existing Author</label>
                    <select name="existing-author-selector-<?php echo $id;?>"
                            id="existing-author-selector-<?php echo $id;?>">
                        <?php
                        foreach ($wp_users as $wp_user) {
                            $value = "value=\"" . $wp_user->ID . "\"";
                            $uname = $wp_user->display_name;
                            echo "<option $value > $uname </option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
    <?php endforeach;?>
</div>
<div class="bwi-row bwi-children-align-right">
    <div class="bwi-button" id="bwi-submit-author-form">
        <div class="bwi-button-text">Import</div>
    </div>
</div>