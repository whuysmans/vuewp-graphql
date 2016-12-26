<?php

namespace TheFold\Mohiohio;

class CustomTaxonomy extends QuickConfig
{
    public $object_type;

    function __construct($type, $name, $object_type=null, $info=array()){
        
        parent::__construct($type,$name,$info); 

        $this->object_type = $object_type;

        $this->register_taxonomy();
    }

    protected function register_taxonomy()
    {
        
        add_action( 'init', function() {

            /**
             * Properties of $this are pulled from the $info array passed
             * to constructor. If they don't exist we look for a default_{property} method
             */
            
            $plural = $this->plural;

            register_taxonomy($this->type, $this->object_type, array( 
                // Hierarchical taxonomy (like categories)
                'hierarchical' => $this->hierarchical,
                'labels' => array(
                    'name' => _x( $plural, 'taxonomy general name' ),
                    'singular_name' => _x( $this->name, 'taxonomy singular name' ),
                    'search_items' =>  __( 'Search '.$plural ),
                    'all_items' => __( 'All '.$plural ),
                    'parent_item' => __( 'Parent '.$this->name ),
                    'parent_item_colon' => __( 'Parent '.$this->name ),
                    'edit_item' => __( 'Edit '.$this->name ),
                    'update_item' => __( 'Update '.$this->name ),
                    'add_new_item' => __( 'Add New '.$this->name ),
                    'new_item_name' => __( 'New '.$this->name ),
                    'menu_name' => __( $plural ),
                ),
                // Control the slugs used for this taxonomy
                'rewrite' => array(
                    'slug' => $this->slug, // This controls the base slug that will display before each term
                    'with_front' => $this->rewrite_with_front, // Don't display the category base before "/locations/"
                    'hierarchical' => $this->rewrite_hierarchical// This will allow URL's like "/locations/boston/cambridge/"
                ),
            )); 

	    register_taxonomy_for_object_type( $this->type, $this->object_type );

        },100);
    }
 
    protected function default_rewrite_with_front() {
        return false;
    }
    
    protected function default_rewrite_hierarchical() {
        return true;
    }
}
