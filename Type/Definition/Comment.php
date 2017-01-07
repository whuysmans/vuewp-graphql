<?php

namespace CI\GraphQLWP\Type\Definition;

use GraphQL\Type\Definition\Type;

class Comment extends WPObjectType {

    private static $instance;

    static function getInstance($config=[]) {
        return static::$instance ?: static::$instance = new static($config);
    }    

    static function getFieldSchema() {

        return [
            'comment_ID' => [
                'type' => Type::int()
            ],
            'comment_post_ID' => [
                'type' => Type::int()
            ],
            'comment_author' => [
                'type' => Type::string()
            ],
            'comment_author_url' => [
                'type' => Type::string()
            ],
            'comment_data' => [
                'type' => Type::string()
            ],
            'comment_content' => [
                'type' => Type::string()
            ],
            'comment_post_url' => [
                'type' => Type::string()
            ],
            'comment_post_title' => [
                'type' => Type::string()
            ]
        ];
    }
}