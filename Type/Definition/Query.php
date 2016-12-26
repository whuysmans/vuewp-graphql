<?php

namespace CI\GraphQLWP\Type\Definition;

use \GraphQL\Type\Definition\Type;
use \GraphQL\Type\Definition\ListOfType;
use \GraphQLRelay\Relay;
use \Mohiohio\GraphQLWP\Schema as WPSchema;

class Query extends WPObjectType {

    static function getFieldSchema() {
        return [
            'wp_query' => [
                'type' => static::getWPQuery(),
                'resolve' => function($root, $args) {
                    global $wp_query;
                    return $wp_query;
                }
            ]
        ];
    }
}
