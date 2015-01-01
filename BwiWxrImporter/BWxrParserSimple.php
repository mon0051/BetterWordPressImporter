<?php
require_once dirname(__FILE__) . '../../BwiWxrImporter/WxrModel/WxrAuthor.php';
require_once dirname(__FILE__) . '../../BwiWxrImporter/WxrModel/WxrCategory.php';
require_once dirname(__FILE__) . '../../BwiWxrImporter/WxrModel/WxrComment.php';
require_once dirname(__FILE__) . '../../BwiWxrImporter/WxrModel/WxrPost.php';
require_once dirname(__FILE__) . '../../BwiWxrImporter/WxrModel/WxrPostMeta.php';
require_once dirname(__FILE__) . '../../BwiWxrImporter/WxrModel/WxrTag.php';
require_once dirname(__FILE__) . '../../BwiWxrImporter/WxrModel/WxrTerm.php';

/**
 * WXR Parser that makes use of the SimpleXML PHP extension.
 */
class BWXR_Parser_SimpleXML
{
    /**
     * @param $file
     * @return array
     * Takes a file pointer and returns an array containing pointers to the exported information
     */
    function parse($file)
    {
        $authors = $posts = $categories = $tags = $terms = array();

        $internal_errors = libxml_use_internal_errors(true);

        $dom = new DOMDocument;
        $old_value = null;
        if (function_exists('libxml_disable_entity_loader')) {
            $old_value = libxml_disable_entity_loader(true);
        }
        $success = $dom->loadXML(file_get_contents($file));
        if (!is_null($old_value)) {
            libxml_disable_entity_loader($old_value);
        }

        if (!$success || isset($dom->doctype)) {
            return new WP_Error('SimpleXML_parse_error', __('There was an error when reading this WXR file', 'wordpress-importer'), libxml_get_errors());
        }

        $xml = simplexml_import_dom($dom);
        unset($dom);

        // halt if loading produces an error
        if (!$xml)
            return new WP_Error('SimpleXML_parse_error', __('There was an error when reading this WXR file', 'wordpress-importer'), libxml_get_errors());

        $wxr_version = $xml->xpath('/rss/channel/wp:wxr_version');
        if (!$wxr_version)
            return new WP_Error('WXR_parse_error', __('This does not appear to be a WXR file, missing/invalid WXR version number', 'wordpress-importer'));

        $wxr_version = (string)trim($wxr_version[0]);
        // confirm that we are dealing with the correct file format
        if (!preg_match('/^\d+\.\d+$/', $wxr_version))
            return new WP_Error('WXR_parse_error', __('This does not appear to be a WXR file, missing/invalid WXR version number', 'wordpress-importer'));

        $base_url = $xml->xpath('/rss/channel/wp:base_site_url');
        $base_url = (string)trim($base_url[0]);

        $namespaces = $xml->getDocNamespaces();
        if (!isset($namespaces['wp']))
            $namespaces['wp'] = 'http://wordpress.org/export/1.1/';
        if (!isset($namespaces['excerpt']))
            $namespaces['excerpt'] = 'http://wordpress.org/export/1.1/excerpt/';

        // grab authors
        foreach ($xml->xpath('/rss/channel/wp:author') as $author_arr) {
            $a = $author_arr->children($namespaces['wp']);
            $login = (string)$a->author_login;
            // Create author object
            $author = new WxrAuthor();
            $author->author_id = $a->author_id;
            $author->author_first_name = $a->author_first_name;
            $author->author_last_name = $a->author_last_name;
            $author->author_display_name = $a->author_display_name;
            $author->author_email = $a->author_email;
            $author->author_login = $login;
            // Add $author to to $authors Array
            $authors[$login] = $author;
        }

        // grab cats, tags and terms
        foreach ($xml->xpath('/rss/channel/wp:category') as $term_arr) {
            $t = $term_arr->children($namespaces['wp']);
            $category = new WxrCategory();
            $category->term_id = (int)$t->term_id;
            $category->category_nicename = (string)$t->category_nicename;
            $category->category_parent = (string)$t->category_parent;
            $category->cat_name = (string)$t->cat_name;
            $category->category_description = (string)$t->category_description;
            $categories[] = $category;
        }

        foreach ($xml->xpath('/rss/channel/wp:tag') as $term_arr) {
            $t = $term_arr->children($namespaces['wp']);
            $tags[] = array(
                'term_id' => (int)$t->term_id,
                'tag_slug' => (string)$t->tag_slug,
                'tag_name' => (string)$t->tag_name,
                'tag_description' => (string)$t->tag_description
            );
        }

        foreach ($xml->xpath('/rss/channel/wp:term') as $term_arr) {
            $t = $term_arr->children($namespaces['wp']);
            $terms[] = array(
                'term_id' => (int)$t->term_id,
                'term_taxonomy' => (string)$t->term_taxonomy,
                'slug' => (string)$t->term_slug,
                'term_parent' => (string)$t->term_parent,
                'term_name' => (string)$t->term_name,
                'term_description' => (string)$t->term_description
            );
        }

        // grab posts
        foreach ($xml->channel->item as $item) {
            $post = array(
                'post_title' => (string)$item->title,
                'guid' => (string)$item->guid,
            );

            $dc = $item->children('http://purl.org/dc/elements/1.1/');
            $post['post_author'] = (string)$dc->creator;

            $content = $item->children('http://purl.org/rss/1.0/modules/content/');
            $excerpt = $item->children($namespaces['excerpt']);
            $post['post_content'] = (string)$content->encoded;
            $post['post_excerpt'] = (string)$excerpt->encoded;

            $wp = $item->children($namespaces['wp']);
            $post['post_id'] = (int)$wp->post_id;
            $post['post_date'] = (string)$wp->post_date;
            $post['post_date_gmt'] = (string)$wp->post_date_gmt;
            $post['comment_status'] = (string)$wp->comment_status;
            $post['ping_status'] = (string)$wp->ping_status;
            $post['post_name'] = (string)$wp->post_name;
            $post['status'] = (string)$wp->status;
            $post['post_parent'] = (int)$wp->post_parent;
            $post['menu_order'] = (int)$wp->menu_order;
            $post['post_type'] = (string)$wp->post_type;
            $post['post_password'] = (string)$wp->post_password;
            $post['is_sticky'] = (int)$wp->is_sticky;

            if (isset($wp->attachment_url))
                $post['attachment_url'] = (string)$wp->attachment_url;

            foreach ($item->category as $c) {
                $att = $c->attributes();
                if (isset($att['nicename']))
                    $post['terms'][] = array(
                        'name' => (string)$c,
                        'slug' => (string)$att['nicename'],
                        'domain' => (string)$att['domain']
                    );
            }

            foreach ($wp->postmeta as $meta) {
                $post['postmeta'][] = array(
                    'key' => (string)$meta->meta_key,
                    'value' => (string)$meta->meta_value
                );
            }

            foreach ($wp->comment as $comment) {
                $meta = array();
                if (isset($comment->commentmeta)) {
                    foreach ($comment->commentmeta as $m) {
                        $meta[] = array(
                            'key' => (string)$m->meta_key,
                            'value' => (string)$m->meta_value
                        );
                    }
                }

                $post['comments'][] = array(
                    'comment_id' => (int)$comment->comment_id,
                    'comment_author' => (string)$comment->comment_author,
                    'comment_author_email' => (string)$comment->comment_author_email,
                    'comment_author_IP' => (string)$comment->comment_author_IP,
                    'comment_author_url' => (string)$comment->comment_author_url,
                    'comment_date' => (string)$comment->comment_date,
                    'comment_date_gmt' => (string)$comment->comment_date_gmt,
                    'comment_content' => (string)$comment->comment_content,
                    'comment_approved' => (string)$comment->comment_approved,
                    'comment_type' => (string)$comment->comment_type,
                    'comment_parent' => (string)$comment->comment_parent,
                    'comment_user_id' => (int)$comment->comment_user_id,
                    'commentmeta' => $meta,
                );
            }

            $posts[] = $post;
        }

        return array(
            'authors' => $authors,
            'posts' => $posts,
            'categories' => $categories,
            'tags' => $tags,
            'terms' => $terms,
            'base_url' => $base_url,
            'version' => $wxr_version,
            'type' => 'simpleXml'
        );
    }
}