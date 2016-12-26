<?php
namespace Mohiohio\WordPress;

/**
 *     $ID = Import::import_post(
       Import::map_data(
            array(
                'ID' => 'post_id',
                'post_title' => 'name',
                'post_content' => 'description',
                'post_date' => function($data){
                    return date('Y-m-d H:i:s',strtotime($data['start_time']));
                },
                'post_date_gmt' => function($data){
                    return gmdate('Y-m-d H:i:s',strtotime($data['start_time']));
                },
                'latitude' => function($data){ return $data['venue']['latitude']; },
                'longitude' =>function($data){ return  $data['venue']['longitude']; },
                'city' => function($data){ return  $data['venue']['city']; },
                'venue_id' => function($data){ return  $data['venue_id']; },

                'attending_count' => 'attending_count',
                'unsure_count' => 'unsure_count',

                'fb_id' => 'id',
            ),
            $detail
       ),
       Events\EVENT_CPT,
       'publish'
   );
*/

class Import
{
    static function import_post($post_data, $type='post', $status=null)
    {
        //Wordpress::log($post_data);
        
        $core['post_type'] = $type;
        
        if($status){
            $core['post_status'] = $status;
        }
        
        $core_fields = static::core_fields();
        
        foreach ($core_fields as $core_field){
            
            if(isset($post_data[$core_field])){
                $core[$core_field] = $post_data[$core_field];
                unset($post_data[$core_field]);
            }
        }
        
        if (!isset($core['ID']) && isset($core['post_name']) && isset($core['post_type']))
        {
            global $wpdb;
            
            $ID = $wpdb->get_var( $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = %s",$core['post_name'], $core['post_type']) );
            
            if(is_wp_error($ID))
            throw new \Exception($ID);
            
            $core['ID'] = $ID;
        }
        
        $ID = empty($core['ID']) ? wp_insert_post($core, true) : wp_update_post($core, true);
        
        if (is_wp_error($ID)){
            throw new \Exception($ID->get_error_message());
        }
        
        foreach ($post_data as $key => $value) {
            update_post_meta($ID, $key, $value );
        }
        
        return $ID;
    }
    
    /**
    *
    * Pass in an array that maps wp_field names to field names in the raw data
    * Example.
    *
    * array(
    'post_title' => 'name',
    'post_name' =>  'identifier',
    'post_content'=> 'blurb',
    'year' => function($data){
    return substr($data['date_start'],0,4);
},
'intergration_id' => 'id'
)
*
* @param $field_map is an associative array of wp_fields to import fields
* @param $data raw data that will be used to mapped to wp_fields ready to pass to import_post
* @return associative array ready for WP to use @see import_post
*
* */
static function map_data($field_map, $data, $cntxt=null)
{
    if( !is_array($field_map) ) {
        throw new \Exception('$field_map is not an array');
    }
    
    if( (!is_array($data) && !is_object($data)) || empty($data) ) {
        throw new \Exception('$data is not an array or object or is empty');
    }
    
    $post_data = array();
    
    foreach( $field_map as $wp_field => $data_field ) {
        
        $value = null;
        
        if($data_field instanceof \Closure) {
            $value = $data_field($data, $cntxt);
        }
        elseif(is_array($data)) {
            $value = isset($data[$data_field]) ?  $data[$data_field] : null;
        }
        else {
            $value = $data->$data_field;
        }
        
        $post_data[$wp_field] = $value;
    }
    
    return $post_data;
}

static function connect_posts($type, $from_id, $to_id, $data=array())
{
    p2p_type( $type )->connect( $from_id, $to_id, $data);
}

static function core_fields(){
    return array(
        'ID',
        'menu_order',
        'comment_status',
        'ping_status',
        'pinged',
        'post_author',
        'post_category',
        'post_content',
        'post_date',
        'post_date_gmt',
        'post_excerpt',
        'post_name',
        'post_parent',
        'post_password',
        'post_status',
        'post_title',
        'post_type',
        'tags_input',
        'to_ping',
        'tax_input',
        'guid'
    );
}

// Use this with
// set_post_thumbnail( $parent_post_id, $attach_id );
//
static function create_attachment($path, $basename=null, $replace=false, $uniquename=null, $parent_post_id=0){
    
    if(is_array($path)){
        
        $path += [
            'basename' => null,
            'replace' => false,
            'uniquename' => null,
            'parent_post_id' => 0,
            'extension' => null,
            'title' => null,
            'generate_metadata_cron' => false
        ];
        
        extract($path);
    }
    
    
    if(empty($path) || !is_string($path)){
        return null;
    }
    
    if(!$basename){
        $basename = pathinfo($path, \PATHINFO_FILENAME);
    }
    
    if(!$extension && !$extension = pathinfo($basename, \PATHINFO_EXTENSION)){
        $extension = pathinfo($path, \PATHINFO_EXTENSION);
    }
    
    $basename .= '.'.$extension;
    
    if(!$uniquename){
        $uniquename = md5($path);
    }
    
    //I don't know what this is for ?
    //$path = str_replace(WP_CONTENT_URL,WP_CONTENT_DIR,$path);
    
    $file = wp_upload_dir()['path'].'/'.$uniquename.'.'.$extension;
    $wp_filetype = wp_check_filetype($basename, null );
    
    $exists = file_exists($file);
    
    if($replace || !$exists){
        copy($path,$file);
    }
    
    if($exists){
        global $wpdb;
        
        if($attachment_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_name = %s", $uniquename))){
            return $attachment_id;
        }
    }
    
    $attachment_id = wp_insert_attachment(array(
        'guid' => $path,
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => $title ?: preg_replace('/\.[^.]+$/', '', $basename),
        'post_name' => $uniquename,
        'post_content' => '',
        'post_status' => 'inherit'
    ), $file, $parent_post_id);
    
    if ($generate_metadata_cron) {
        
        //You'll need to have an action that is ready to handle this
        wp_schedule_single_event(time(),$generate_metadata_cron,[$attachment_id,$file]);
    } else {
        static::generate_metadata($attachment_id, $file);
    }
    
    return $attachment_id;
}

static function generate_metadata($attachment_id, $file) {
    
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    wp_maybe_generate_attachment_metadata(get_post($attachment_id));
    //$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file );
    //wp_update_attachment_metadata( $attachment_id, $attachment_data );
}
}
