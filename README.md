# graphql-wp
A simplified GraphQL endpoint for WordPress.
Forked from Tim Field for my [vuewp](https://github.com/whuysmans/vuewp) project.

You can find the original repo here: [graphql-wp](https://github.com/tim-field/graphql-wp).

Here are some changes I made:

### Added a count result for pagination
A 'count' result is returned with every response, like the 'X-WP-Total' header in the WP Rest Api. To get the number of posts in each response, the 'get_posts' method in WPQuery had to be replaced by WPQuery->query.

### Added acf response
ACF response was added to WPPost. This still needs some work.

## Requires
+ php >= 5.6

