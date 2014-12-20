<?php
/**
 * Author: Andrew Monteith
 * Date: 20/12/14 12:03 AM
 */

class BMI_Import {
    public function authorImport(){
        //var_dump($_POST);
        if(isset($_POST['formdata'])){
            $authors = $_POST['formdata'];
            $rollbackData = "{ \"users_added\":[";
            $log = "";
            foreach($authors as $author){
                $username = $author['username'];
                $user_id = wp_create_user($username,rand(),"bwi@bwi.com");
                if(is_wp_error($user_id)){
                    $log = $log .'\n User :'. $username . 'Failed to Import';
                    continue;
                }
                $rollbackData = $rollbackData."{ \"username\": \"$username\" , \"user_id\" :\"$user_id\" },";
            }
            $rollbackData = $rollbackData . " ]}";
            $post = array(
                'post_content' => $rollbackData,
                'post_type' => 'bwi_rollback'
            );
            $rollback_pid = wp_insert_post($post);
            if(is_wp_error($rollback_pid)){
                echo $rollback_pid->get_error_message();
            }
        }else{
            echo "no_form_data";
        }
    }
}