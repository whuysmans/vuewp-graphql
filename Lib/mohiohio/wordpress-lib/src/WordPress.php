<?php
namespace Mohiohio\WordPress;

class WordPress {

    static function render_template($slug, $name = null, $view_params=array(), $return=false,$default_path=null)
    {
        $done = false;
        $old_globals = [];

        if(is_array($slug)){
            extract($slug);
        }

        if($view_params)
        {
            global $wp_query;
            foreach($view_params as $key => $value){

                $old_globals[$key] = $wp_query->get($key,null);

                $wp_query->set($key, $value);
            }
        }

        if($return) ob_start();

        if($default_path) {

            $templates = [];
            $name = (string) $name;
            if ( '' !== $name )
                $templates[] = "{$slug}-{$name}.php";

            $templates[] = "{$slug}.php";

            if(! $done = locate_template($templates,true,false) ){

                $done = true;
                load_template($default_path.'.php',false);
            }
        }

        if(!$done) {
            get_template_part($slug, $name);
        }

        if($old_globals){
            //Reset any global variables back to how they were
            foreach($old_globals as $key => $old_value){
                $wp_query->set($key, $old_value);
            }
        }

        if($return)
            return ob_get_clean();
    }

    static function render_page($slug,$view_params=[],$layout='layouts/default',$return=false)
    {
        return static::render_template([
            'slug' => $layout,
            'view_params' => array_merge(
                ['content_for_layout' => static::render_template($slug,null,$view_params,true)],
                $view_params
            ),
            'return' => $return
        ]);
    }

    static public function get_user_role($user=null)
    {
        if(is_numeric($user))
            $user = new WP_User($user);

        if(!$user)
            $user = wp_get_current_user();

        $user_roles = $user->roles;
        return $user_roles ? array_shift($user_roles) : null;
    }

    static public function get_option($namespace,$key=null,$default=null)
    {
        $options = get_option($namespace);

        if($key)
            $return = isset($options[$key]) ? $options[$key] : null;
        else
            $return = $options;

        return $return ?: $default;
    }

    static function send_404()
    {
        global $wp_query;
        status_header('404');
        $wp_query->set_404();
    }

    static function get_post_content($post_id)
    {
        return $post_id ? apply_filters('the_content', get_post_field('post_content', $post_id)) : null;
    }

    static function get_post_by_slug($slug, $type='post')
    {
        global $wpdb;
        $page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s", $slug, $type ) );
        if ( $page )
            return get_post( $page, $output );
    }

}
