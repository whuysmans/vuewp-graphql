# graphql-wp
A GraphQL endpoint for WordPress.
Forked from Tim Field for my [vuewp](https://github.com/whuysmans/vuewp) project.

You can find the original repo here: [graphql-wp](https://github.com/tim-field/graphql-wp).

These are the changes I made:

### Added a Yoast type
In order to retrieve SEO-data with the graphql responses, there is a dedicated type for SEO. In WPQuery the type is included in the response. There is a curl-call at avery route change, and the Yoast html output is captured and parsed for seo-attributes and values. UPDATE: This definitely has to be changed: in the current implementation, yoast data is async. I don't know much about seo, but I assume the data will arrive too late this way. SSR is probably the way to go...

### Added a count result for pagination
A 'count' result is returned with every response, like the 'X-WP-Total' header in the WP Rest Api. To get the number of posts in each response, the 'get_posts' method in WPQuery had to be replaced by WPQuery->query.

### Added acf response
ACF response was added to WPPost. This still needs some work.

### Solved CORS issue
I had to add the Acces-Control-Allow-Origin header in the Allow-Headers in index.php. On the client side, at the Lokka GraphQL js client, I had to set credentials to false. Still need to investigate further on this.

