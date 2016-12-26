<?php
namespace Mohiohio\WordPress;

/**
 * Setup Custom Post Types 
 */
class CustomPostType extends QuickConfig
{
    //https://developer.wordpress.org/resource/dashicons/
    function __construct($type, $name, $info=array()){
        
        if(strlen($type) > 20){
            throw new \Exception('CPT type names have a max lenght of 20 chars');
        }
        
        parent::__construct($type,$name,$info); 
        
        $this->setup_post_type();
    }
    
    protected function default_menu_icon(){
        return null;
    }
    
    protected function default_taxonomies(){
        return array();
    }
    
    protected function default_public(){
        return true;
    }
    
    protected function default_show_ui(){
        return true;
    }
    
    protected function default_supports(){
        return array(
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'revisions',
            'page-attributes'
        );
    }
    
    protected function default_rewrite_with_front() {
        return true;
    }
    
    protected function setup_post_type()
    {
        add_action( 'init', function() {
            
            /**
            * Properties of $me are pulled from the $info array passed
            * to constructor. If they don't exist we look for a default_{property} method
            */
            
            $plural = $this->plural;
            
            register_post_type( $this->type, [
                'label' => $plural,
                'labels' => [
                    'name' => $plural,
                    'singular_name' => $this->name,
                    'add_new_item' => 'Add New '.$this->name,
                    'edit_item' => 'Edit '.$this->name,
                    'new_item' => 'New '.$this->name,
                    'view_item' => 'View '.$this->name,
                    'search_items' => 'Search '.$plural,
                    'parent_item_colon' => 'Parent '.$this->name
                ],
                'public' => $this->public,
                'show_ui' => $this->show_ui,
                'rewrite' => ['slug' => $this->slug, 'with_front' => $this->rewrite_with_front],
                'menu_position' => $this->menu_position,
                'menu_icon' => $this->menu_icon,
                'hierarchical' => $this->hierarchical,
                'supports'=> $this->supports,
                'taxonomies' => $this->taxonomies,
                'has_archive' => $this->has_archive
            ]);
            
        },99); // why 99 ?
    }
}

