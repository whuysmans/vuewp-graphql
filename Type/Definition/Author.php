<?php

namespace CI\GraphQLWP\Type\Definition;

use GraphQL\Type\Definition\Type;

class Author extends WPObjectType {

    private static $instance;

    static function getInstance($config=[]) {
        return static::$instance ?: static::$instance = new static($config);
    }    

    static function getFieldSchema() {

        return [
            'name' => [
                'type'=>Type::string()
            ],
            'url' => [
                'type'=>Type::string(),
            ],
            'avatar' => [
                'type' => Type::string()
            ],
            'id' => [
                'type' => Type::int()
            ]
        ];
    }
}