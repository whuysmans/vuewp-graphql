<?php

namespace Mohiohio\GraphQLWP\Type\Definition;

use \GraphQL\Type\Definition\Type;
use \GraphQL\Type\Definition\ListOfType;

class Yoast extends WPObjectType {

    private static $instance;

    static function getInstance($config=[]) {
        return static::$instance ?: static::$instance = new static($config);
    }    

    static function getFieldSchema() {

        return [

            'title' => [
                'type' => Type::string()
            ],
            'viewport' => [
                'type' => Type::string()
            ],
            'description' => [
                'type' => Type::string()
            ],
            'og_locale' => [
                'type' => Type::string()
            ],
            'og_type' => [
                'type' => Type::string()
            ],
            'og_description' => [
                'type' => Type::string()
            ],
            'og_url' => [
                'type' => Type::string()
            ],
            'og_site_name' => [
                'type' => Type::string()
            ],
            'twitter_card' => [
                'type' => Type::string()
            ],
            'twitter_description' => [
                'type' => Type::string()
            ],
            'twitter_title' => [
                'type' => Type::string()
            ],
            'generator' => [
                'type' => Type::string()
            ]
        ];
    }
}