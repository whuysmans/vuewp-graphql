<?php

namespace Mohiohio\WordPress\Setting;

class Section
{

    #@param string $id Slug-name to identify the section. Used in the 'id' attribute of tags.
    protected $id;
    #@param string $title Formatted title of the section. Shown as the heading for the section.
    protected $title;
    #@param string $callback Function that echos out any content at the top of the section (between heading and fields).
    protected $callback;
    #@param string $page The slug-name of the settings page on which to show the section. Built-in pages include 'general', 'reading', 'writing', 'discussion', 'media', etc. Create your own using add_options_page();
    protected $page;

    protected $added = false;

    function __construct($id, $title, $callback=null, $page=null) {
        $this->id = $id;
        $this->title = $title;
        $this->callback = $callback;
        $this->page = $page ?: $id;
    }

    function get_id(){
        return $this->id;
    }

    function add(){

        if(!$this->added){

            add_settings_section($this->id, $this->title, $this->callback, $this->page);

        }

        $this->added = true;
    }
}
