<?php
namespace WpGenesisFred;

class My_Theme_Content
{
    CONST NO_IMAGE = 'data:image/jpeg;base64,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';
    
    public function get_related_posts($nb = 6, $limit = 100)
    {
        $str              = "";
        $related_post_ids = array();
        $related_posts    = array();
        $first            = true;
        global $post;
        $orig_post = $post;
        $post_id   = $post->ID;
        
        $tags = wp_get_post_tags($post_id);
        
        if ($tags) {
            $tag_ids = array();
            foreach ($tags as $tag) {
                $tag_ids[] = $tag->term_id;
            }
            
            $args     = array(
                'tag__in' => $tag_ids,
                'post__not_in' => array(
                    $post_id
                ),
                'posts_per_page' => $limit,
                'fields' => 'ids'
            );
            $my_query = new \wp_query($args);
            foreach ($my_query->posts as $id) {
                if (!array_key_exists($id, $related_post_ids)) {
                    $related_post_ids[$id] = 0;
                }
                
                $tags2 = wp_get_post_tags($id);
                foreach ($tags2 as $tag) {
                    if (in_array($tag->term_id, $tag_ids)) {
                        $related_post_ids[$id] = $related_post_ids[$id] + 1;
                    }
                }
            }
        }
        
        $category_ids = wp_get_post_categories($post_id);
        
        if ($category_ids) {
            
            $args     = array(
                'category__in' => $category_ids,
                'post__not_in' => array(
                    $post_id
                ),
                'posts_per_page' => $limit,
                'fields' => 'ids'
            );
            $my_query = new \wp_query($args);
            foreach ($my_query->posts as $id) {
                if (!array_key_exists($id, $related_post_ids)) {
                    $related_post_ids[$id] = 0;
                }
                
                $category_ids2 = wp_get_post_categories($id);
                foreach ($category_ids2 as $category_id) {
                    if (in_array($category_id, $category_ids)) {
                        $related_post_ids[$id] = $related_post_ids[$id] + 1;
                    }
                }
            }
        }
        
        if (count($related_post_ids) > 0) {
            arsort($related_post_ids);
            $count = 0;
            foreach ($related_post_ids as $related_post_id => $related_indice) {
                $count++;
                if ($count > $nb) {
                    break;
                }
                
                $related_post = get_post($related_post_id);
                $permalink    = get_permalink($related_post_id);
                $alt          = __('Post') . ' ' . htmlspecialchars(get_the_title($related_post_id));
                $thumb_img    = '<img src="" alt="' . $alt . '">';
                if (has_post_thumbnail($related_post_id)) {
                    $thumb_img = get_the_post_thumbnail($related_post_id, 'thumb_wide');
                    $thumb_img = str_replace('alt=""', "alt='$alt'", $thumb_img);
                } else {
                    $thumb_img = '<img src="' . self::NO_IMAGE . '" alt="' . $alt . '" width="300" height="225">';
                }
                
                $related_posts[$related_post_id] = array(
                    'post_title' => $related_post->post_title,
                    'permalink' => $permalink,
                    'thumbnail' => $thumb_img
                );
            }
        }
        
        $post = $orig_post;
        wp_reset_query();
        
        return $related_posts;
    }
}