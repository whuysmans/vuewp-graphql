# graphql-wp
A simplified GraphQL endpoint for WordPress.
Forked from Tim Field for my [vuewp](https://github.com/whuysmans/vuewp) project.

You can find the original repo here: [graphql-wp](https://github.com/tim-field/graphql-wp).

Here are some changes I made:

### Added a count result for pagination
A 'count' result is returned with every response, like the 'X-WP-Total' header in the WP Rest Api. To get the number of posts in each response, the 'get_posts' method in WPQuery had to be replaced by WPQuery->query.

### Added acf response
ACF response was added to WPPost. This still needs some work.

### Solved CORS issue
I had to add the Acces-Control-Allow-Origin header in the Allow-Headers in index.php. On the client side, at the Lokka GraphQL js client, I had to set credentials to false. Still need to investigate further on this.

## Requires
+ php >= 5.6

