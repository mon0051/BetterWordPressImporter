<?php

/**
 * Author: Andrew Monteith
 * Date: 20/12/14 12:03 AM
 */
class BmiImport
{
    public $authors;
    public $author_form;

    /**
     *  Basic Constuctor
     */
    function __construct()
    {
        if (isset($_SESSION['bwi_results']['authors'])) {
            $this->authors = $_SESSION['bwi_results']['authors'];
        }
        if (isset($_POST['formdata'])) {
            $this->form = $_POST['formdata'];
        }
    }

    /**
     * Import the authors passed back by the web form into wordpress
     */
    public function authorImport()
    {
        if (isset($this->form) && $this->form != null) {
            $authors = $_POST['formdata'];
            $rollbackData = "{ \"users_added\":[";
            $log = "<div>";
            foreach ($authors as $author) {
                $session_author = $this->get_author_from_session($author['author_login']);
                $username = ';';
                $user_id = wp_create_user($username, rand(), "bwi@bwi.com");
                if (is_wp_error($user_id)) {
                    $log = $log . '<div> User :' . $username . 'Failed to Import : Error<div>' . $user_id->get_error_message() . '</div></div>';
                    continue;
                }
                $rollbackData = $rollbackData . "{ \"username\": \"$username\" , \"user_id\" :\"$user_id\" },";
            }
            $rollbackData = $rollbackData . " ]}";
            $log = $log . '</div>';

            echo $log;
            $post = array(
                'post_content' => $rollbackData,
                'post_type' => 'bwi_rollback'
            );
            $rollback_pid = wp_insert_post($post);
            if (is_wp_error($rollback_pid)) {
                echo $rollback_pid->get_error_message();
            }
        } else {
            echo "no_form_data";
        }
    }

    /**
     * Get Author from the cached session value
     * @param $oldId
     * @return bool
     */

    function get_author_from_session($oldId)
    {
        foreach ($this->authors as $author) {
            if ($oldId == $author['user_id']) {
                return $author;
            }
        }
        return false;
    }

    /**
     * @param $log
     */
    function save_log($log){

    }
}