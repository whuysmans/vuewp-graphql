<?php

namespace CI\GraphQLWP\Type\Definition;

use GraphQL\Type\Definition\Type;

class HeaderVideo extends WPObjectType {

    private static $instance;

    static function getInstance($config=[]) {
        return static::$instance ?: static::$instance = new static($config);
    }

    static function getFieldSchema() {

        return [
            'url' => [
                'type' => Type::string()
            ],
            'width' => [
                'type' => Type::int()
            ],
            'height' => [
                'type' => Type::int()
            ]
        ];
    }
}
