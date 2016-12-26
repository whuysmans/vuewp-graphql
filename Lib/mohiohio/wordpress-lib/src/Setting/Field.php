<?php
namespace Mohiohio\WordPress\Setting;
use Mohiohio\WordPress\Setting\Section;

class Field
{
    public $name;
    public $title;
    public $display_callback;
    public $section;
    public $default;
    public $type;
    public $props = [];

    function __construct($name, $title=null, Section $section=null, $display_callback=null, $default=null, $type='text', $props=[])
    {
        if(is_array($name)){

            $name += [
                'title' => null,
                'section' => null,
                'display_callback' => null,
                'default' => null,
                'type' => 'text',
                'props' => []
            ];

            extract($name);
        }

        $this->name = $name;
        $this->title = $title;
        $this->display_callback = $display_callback;
        $this->section = $section;
        $this->default = $default;
        $this->type = $type;
        $this->props = $props;
    }

    function get_section() {
        return $this->section;
    }

    function get_display_callback($setting_group) {

        $options = get_option($setting_group);
        $value = isset($options[$this->name]) ? $options[$this->name] : $this->default;

        $props = $this->props += [
            'id'=> $setting_group.'_'.$this->name,
            'name' => "{$setting_group}[{$this->name}]",
            'placeholder'=> $this->default,
            'type'=>$this->type,
        ];

        return function() use ($value, $props) {

            if($this->display_callback instanceof \Closure) {

                call_user_func($this->display_callback, $value, $props);

            } else {

                switch($this->display_callback) {

                    case 'textarea':
                    echo static::textarea($value, $props);
                    break;

                    default:
                    echo static::input($value, $props);
                }
            }
        };
    }

    static function render_props($props){
        return array_reduce(array_keys($props), function($render,$key) use ($props){
             return $render.' '.$key.'="'. htmlspecialchars($props[$key]).'"';
        },'');
    }

    static function input($value, $props) {
        $props += [
            'value' => $value
        ];
        return "<input ".static::render_props($props)." />";
    }

    static function textarea($value, $props) {
        $props += [
            'rows'=>10,
            'cols'=>50,
        ];
        return "<textarea ".static::render_props($props)." >{$value}</textarea>";
    }

}
