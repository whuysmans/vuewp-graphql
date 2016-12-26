<?php
namespace CI\GraphQLWP;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQLRelay\Relay;

use CI\GraphQLWP\Type\Definition\WPQuery;
use CI\GraphQLWP\Type\Definition\WPPost;
use CI\GraphQLWP\Type\Definition\WPTerm;

class Schema
{
    static protected $query = null;
    static protected $nodeDefinition = null;

    static function build() {
        static::init();
        return new \GraphQL\Schema(static::getQuery());
    }

    static function init() {
        WPPost::init();
        WPTerm::init();
        do_action('graphql-wp/schema_init');
    }


    static function getNodeDefinition() {

        return static::$nodeDefinition ?: static::$nodeDefinition = Relay::nodeDefinitions(
        function($globalID) {

            $idComponents = Relay::fromGlobalId($globalID);

            switch ($idComponents['type']){
                case WPPost::TYPE;
                return get_post($idComponents['id']);
                case WPTerm::TYPE;
                return get_term($idComponents['id']);
                default;
                return null;
            }
        },
        function($obj) {

            if ($obj instanceOf \WP_Post) {
                return WPPost::resolveType($obj);
            }
            if ($obj instanceOf \WP_Term) {
                return WPTerm::resolveType($obj);
            }
        });
    }

    static function getQuery() {
        return static::$query ?: static::$query = new ObjectType(static::getQuerySchema());
    }

    static function getQuerySchema() {

        $schema = apply_filters('graphql-wp/get_query_schema',[
            'name' => 'Query',
            'fields' => [
                'wp_query' => [
                    'type' => WPQuery::getInstance(),
                    'resolve' => function($root, $args) {
                        global $wp_query;
                        return $wp_query;
                    }
                ]
            ]
        ]);

        return $schema;
    }
}
