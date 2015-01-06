<?php
require_once dirname(__FILE__) . '/../WxrModel/WxrAuthor.php';
require_once dirname(__FILE__) . '/../WxrModel/WxrCategory.php';
require_once dirname(__FILE__) . '/../WxrModel/WxrComment.php';
require_once dirname(__FILE__) . '/../WxrModel/WxrPost.php';
require_once dirname(__FILE__) . '/../WxrModel/WxrPostMeta.php';
require_once dirname(__FILE__) . '/../WxrModel/WxrTag.php';
require_once dirname(__FILE__) . '/../WxrModel/WxrTerm.php';
/**
 * WXR Parser that uses regular expressions. Fallback for installs without an XML parser.
 */
class BWXR_Parser_Regex
{
    private $authors = array();
    private $posts = array();
    private $categories = array();
    private $tags = array();
    private $terms = array();
    private $base_url = '';

    function WXR_Parser_Regex()
    {
        $this->__construct();
    }

    /**
     * Constructor
     */
    function __construct()
    {
        $this->has_gzip = is_callable('gzopen');
    }

    /**
     * @param $file
     * @return array|WP_Error
     * Takes a file pointer and returns an array containing pointers to the exported information
     */
    function parse($file)
    {
        $wxr_version = $in_post = false;

        $fp = $this->fopen($file, 'r');
        if ($fp) {
            while (!$this->feof($fp)) {
                $importline = rtrim($this->fgets($fp));

                if (!$wxr_version && preg_match('|<wp:wxr_version>(\d+\.\d+)</wp:wxr_version>|', $importline, $version)) {
                    $wxr_version = $version[1];
                }

                if (false !== strpos($importline, '<wp:base_site_url>')) {
                    preg_match('|<wp:base_site_url>(.*?)</wp:base_site_url>|is', $importline, $url);
                    $this->base_url = $url[1];
                    continue;
                }
                if (false !== strpos($importline, '<wp:category>')) {
                    preg_match('|<wp:category>(.*?)</wp:category>|is', $importline, $category);
                    $this->categories[] = $this->process_category($category[1]);
                    continue;
                }
                if (false !== strpos($importline, '<wp:tag>')) {
                    preg_match('|<wp:tag>(.*?)</wp:tag>|is', $importline, $tag);
                    $this->tags[] = $this->process_tag($tag[1]);
                    continue;
                }
                if (false !== strpos($importline, '<wp:term>')) {
                    preg_match('|<wp:term>(.*?)</wp:term>|is', $importline, $term);
                    $this->terms[] = $this->process_term($term[1]);
                    continue;
                }
                if (false !== strpos($importline, '<wp:author>')) {
                    preg_match('|<wp:author>(.*?)</wp:author>|is', $importline, $author);
                    /** @var WxrAuthor $a */
                    $a = $this->process_author($author[1]);
                    $this->authors[$a->author_login] = $a;
                    continue;
                }
                if (false !== strpos($importline, '<item>')) {
                    $post = '';
                    $in_post = true;
                    continue;
                }
                if (false !== strpos($importline, '</item>')) {
                    $in_post = false;
                    $this->posts[] = $this->process_post($post);
                    continue;
                }
                if ($in_post) {
                    $post .= $importline . "\n";
                }
            }

            $this->fclose($fp);
        }

        if (!$wxr_version)
            return new WP_Error('WXR_parse_error', __('This does not appear to be a WXR file, missing/invalid WXR version number', 'wordpress-importer'));

        //----------------------------------------------
        //              Map Post Hierarchy
        //----------------------------------------------
        foreach ($this->posts as $cPost) {
            if ($cPost instanceof WxrPost && $cPost->wxrPostParent) {
                foreach ($this->posts as $pPost) {
                    if ($pPost instanceof WxrPost && $cPost->wxrPostParent == $pPost->wxrPostId) {
                        $cPost->post_parent = $pPost;
                    }
                }
            }
        }
        //----------------------------------------------
        //          Map Category Hierarchy
        //----------------------------------------------
        foreach($this->categories as $cCategory){
            if ($cCategory instanceof WxrCategory && $cCategory->wxrCategoryParent) {
                foreach ($this->posts as $pCategory) {
                    if ($pCategory instanceof WxrCategory && $cCategory->wxrCategoryParent == $pCategory->wxrTermId) {
                        $cCategory->category_parent = $pCategory;
                    }
                }
            }
        }
        //----------------------------------------------
        //              Map Term Hierarchy
        //----------------------------------------------
        foreach($this->terms as $cTerm){
            if ($cTerm instanceof WxrTerm && $cTerm->wxrTermParent) {
                foreach ($this->posts as $pTerm) {
                    if ($pTerm instanceof WxrTerm && $cTerm->wxrTermParent == $pTerm->wxrTermId) {
                        $cTerm->term_parent = $pTerm;
                    }
                }
            }
        }
        //----------------------------------------------
        //              Map Comment Hierarchy
        //----------------------------------------------
        foreach($this->terms as $cComment){
            if ($cComment instanceof WxrComment && $cComment->wxrCommentParent) {
                foreach ($this->posts as $pComment) {
                    if ($pComment instanceof WxrComment && $cComment->wxrCommentParent == $pComment->wxrCommentId) {
                        $cComment->comment_parent = $pComment;
                    }
                }
            }
        }
        return array(
            'authors' => $this->authors,
            'posts' => $this->posts,
            'categories' => $this->categories,
            'tags' => $this->tags,
            'terms' => $this->terms,
            'base_url' => $this->base_url,
            'version' => $wxr_version
        );
    }

    /**
     * @param $string
     * @param $tag
     * @return mixed|string
     */
    function get_tag($string, $tag)
    {
        preg_match("|<$tag.*?>(.*?)</$tag>|is", $string, $return);
        if (isset($return[1])) {
            if (substr($return[1], 0, 9) == '<![CDATA[') {
                if (strpos($return[1], ']]]]><![CDATA[>') !== false) {
                    preg_match_all('|<!\[CDATA\[(.*?)\]\]>|s', $return[1], $matches);
                    $return = '';
                    foreach ($matches[1] as $match)
                        $return .= $match;
                } else {
                    $return = preg_replace('|^<!\[CDATA\[(.*)\]\]>$|s', '$1', $return[1]);
                }
            } else {
                $return = $return[1];
            }
        } else {
            $return = '';
        }
        return $return;
    }

    /**
     * @param $c
     * @return array
     */
    function process_category($c)
    {
        $category = new WxrCategory();
        $category->wxrTermId = $this->get_tag($c, 'wp:term_id');
        $category->cat_name = $this->get_tag($c, 'wp:cat_name');
        $category->category_nicename = $this->get_tag($c, 'wp:category_nicename');
        $category->wxrCategoryParent = $this->get_tag($c, 'wp:category_parent');
        $category->category_description = $this->get_tag($c, 'wp:category_description');
        return $category;
    }

    /**
     * @param $t
     * @return array
     */
    function process_tag($t)
    {
        $tag = new WxrTag();
        $tag->term_id = $this->get_tag($t, 'wp:term_id');
        $tag->tag_slug = $this->get_tag($t, 'wp:tag_slug');
        $tag->tag_name = $this->get_tag($t, 'wp:tag_name');
        $tag->tag_description = $this->get_tag($t, 'wp:tag_description');
        return $tag;
    }

    /**
     * @param $t
     * @return array
     */
    function process_term($t)
    {
        $term = new WxrTerm();
        $term->wxrTermId = $this->get_tag($t, 'wp:term_id');
        $term->term_taxonomy = $this->get_tag($t, 'wp:term_taxonomy');
        $term->slug = $this->get_tag($t, 'wp:term_slug');
        $term->wxrTermParent = $this->get_tag($t, 'wp:term_parent');
        $term->term_name = $this->get_tag($t, 'wp:term_name');
        $term->term_description = $this->get_tag($t, 'wp:term_description');
        return $term;

    }

    /**
     * @param $a
     * @return array
     */
    function process_author($a)
    {
        $author = new WxrAuthor();

        $author->author_id = (int)$this->get_tag($a, 'wp:author_id');
        $author->author_first_name = $this->get_tag($a, 'wp:author_first_name');
        $author->author_last_name = $this->get_tag($a, 'wp:author_last_name');
        $author->author_display_name = $this->get_tag($a, 'wp:author_display_name');
        $author->author_email = $this->get_tag($a, 'wp:author_email');
        $author->author_login = $this->get_tag($a, 'wp:author_login');
        return $author;
    }

    /**
     * @param $post
     * @return array
     */
    function process_post($post)
    {
        //----------------------------------------------
        // Standard Post Data
        //----------------------------------------------
        $wxrPost = new WxrPost();
        $wxrPost->wxrPostId = $this->get_tag($post, 'wp:post_id');
        $wxrPost->post_title = $this->get_tag($post, 'title');
        $wxrPost->post_date = $this->get_tag($post, 'wp:post_date');
        $wxrPost->post_date_gmt = $this->get_tag($post, 'wp:post_date_gmt');
        $wxrPost->comment_status = $this->get_tag($post, 'wp:comment_status');
        $wxrPost->ping_status = $this->get_tag($post, 'wp:ping_status');
        $wxrPost->status = $this->get_tag($post, 'wp:status');
        $wxrPost->post_name = $this->get_tag($post, 'wp:post_name');
        $wxrPost->wxrPostParent = $this->get_tag($post, 'wp:post_parent');
        $wxrPost->menu_order = $this->get_tag($post, 'wp:menu_order');
        $wxrPost->post_type = $this->get_tag($post, 'wp:post_type');
        $wxrPost->post_password = $this->get_tag($post, 'wp:post_password');
        $wxrPost->is_sticky = $this->get_tag($post, 'wp:is_sticky');
        $wxrPost->guid = $this->get_tag($post, 'guid');
        $wxrPost->post_author = $this->get_tag($post, 'dc:creator');
        //** End Standard Post Data **/

        //----------------------------------------------
        // post excerpt
        //----------------------------------------------
        $post_excerpt = $this->get_tag($post, 'excerpt:encoded');
        $post_excerpt = preg_replace_callback('|<(/?[A-Z]+)|', array(&$this, '_normalize_tag'), $post_excerpt);
        $post_excerpt = str_replace('<br>', '<br />', $post_excerpt);
        $wxrPost->post_excerpt = str_replace('<hr>', '<hr />', $post_excerpt);
        //** End Post Excerpt */

        //----------------------------------------------
        // post content
        //----------------------------------------------
        $wxrPost->post_content = $this->get_tag($post, 'content:encoded');
        $wxrPost->post_content = preg_replace_callback('|<(/?[A-Z]+)|', array(&$this, '_normalize_tag'), $wxrPost->post_content);
        $wxrPost->post_content = str_replace('<br>', '<br />', $wxrPost->post_content);
        $wxrPost->post_content = str_replace('<hr>', '<hr />', $wxrPost->post_content);
        //** end post content */

        //----------------------------------------------
        // Post Terms
        //----------------------------------------------
        $attachment_url = $this->get_tag($post, 'wp:attachment_url');
        if ($attachment_url)
            $wxrPost->attachment_url['attachment_url'] = $attachment_url;
        preg_match_all('|<category domain="([^"]+?)" nicename="([^"]+?)">(.+?)</category>|is', $post, $terms, PREG_SET_ORDER);
        foreach ($terms as $t) {
            $post_term = new WxrTerm();
            $post_term->slug = $t[2];
            $post_term->domain = $t[1];
            $post_term->term_name = str_replace(array('<![CDATA[', ']]>'), '', $t[3]);
            $post_terms[] = $post_term;
        }
        if (!empty($post_terms)) $wxrPost->terms = $post_terms;
        //** End post terms */

        //----------------------------------------------
        // Post Comments
        //----------------------------------------------
        preg_match_all('|<wp:comment>(.+?)</wp:comment>|is', $post, $comments);
        $comments = $comments[1];
        $post_comments = array();
        if ($comments) {
            foreach ($comments as $comment) {
                $wxrComment = new WxrComment();
                //----------------------------------------------
                // Comment Meta Data
                //----------------------------------------------
                preg_match_all('|<wp:commentmeta>(.+?)</wp:commentmeta>|is', $comment, $commentmeta);
                $commentmeta = $commentmeta[1];
                foreach ($commentmeta as $m) {
                    $wxrComment->commentmeta[] = array(
                        'key' => $this->get_tag($m, 'wp:meta_key'),
                        'value' => $this->get_tag($m, 'wp:meta_value'),
                    );
                }
                //** end comment meta data */

                //----------------------------------------------
                // Standard Comment Data
                //----------------------------------------------
                $wxrComment->comment_id = $this->get_tag($comment, 'wp:comment_id');
                $wxrComment->comment_author = $this->get_tag($comment, 'wp:comment_author');
                $wxrComment->comment_author_email = $this->get_tag($comment, 'wp:comment_author_email');
                $wxrComment->comment_author_IP = $this->get_tag($comment, 'wp:comment_author_IP');
                $wxrComment->comment_author_url = $this->get_tag($comment, 'wp:comment_author_url');
                $wxrComment->comment_date = $this->get_tag($comment, 'wp:comment_date');
                $wxrComment->comment_date_gmt = $this->get_tag($comment, 'wp:comment_date_gmt');
                $wxrComment->comment_content = $this->get_tag($comment, 'wp:comment_content');
                $wxrComment->comment_approved = $this->get_tag($comment, 'wp:comment_approved');
                $wxrComment->comment_type = $this->get_tag($comment, 'wp:comment_type');
                $wxrComment->wxrCommentParent = $this->get_tag($comment, 'wp:comment_parent');
                $wxrComment->comment_user_id = $this->get_tag($comment, 'wp:comment_user_id');
                $post_comments[] = $wxrComment;
                //** End standard comment data */
            }
        }
        if (!empty($post_comments)) $wxrPost->comments = $post_comments;
        //** End Post Comments */

        //----------------------------------------------
        //   Post MetaData
        //----------------------------------------------
        preg_match_all('|<wp:postmeta>(.+?)</wp:postmeta>|is', $post, $postmeta);
        $postmeta = $postmeta[1];
        if ($postmeta) {
            foreach ($postmeta as $p) {
                $pmo = new WxrPostMeta();
                $pmo->value = $this->get_tag($p, 'wp:meta_value');
                $pmo->key = $this->get_tag($p, 'wp:meta_key');
                $post_postmeta[] = $pmo;
            }
        }
        if (!empty($post_postmeta)) $wxrPost->postmeta = $post_postmeta;
        //** end Post MetaData */

        //----------------------------------------------
        // Return completed Post Object
        //----------------------------------------------
        return $wxrPost;
    }

    /**
     * @param $matches
     * @return string
     */
    function _normalize_tag($matches)
    {
        return '<' . strtolower($matches[1]);
    }

    /**
     * @param $filename
     * @param string $mode
     * @return resource
     */
    function fopen($filename, $mode = 'r')
    {
        if ($this->has_gzip)
            return gzopen($filename, $mode);
        return fopen($filename, $mode);
    }

    /**
     * @param $fp
     * @return bool|int
     */
    function feof($fp)
    {
        if ($this->has_gzip)
            return gzeof($fp);
        return feof($fp);
    }

    /**
     * @param $fp
     * @param int $len
     * @return string
     */
    function fgets($fp, $len = 8192)
    {
        if ($this->has_gzip)
            return gzgets($fp, $len);
        return fgets($fp, $len);
    }

    /**
     * @param $fp
     * @return bool
     */
    function fclose($fp)
    {
        if ($this->has_gzip)
            return gzclose($fp);
        return fclose($fp);
    }
}