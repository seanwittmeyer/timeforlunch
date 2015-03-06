# time for lunch api
The system is designed to be as simple as possible, and for the sake of speedy development, we built out an api that should be able to do anything for the system. Documentation on this will happen here.

The site will spit out json-encoded responses, along with response header codes 
* 200 - ok
* 304 - no change
* 403 - houston we have a problem
* 404 - nothing to see here

### endpoints
* /api/orders - get all orders
* /api/orders/{id} - get single order
* /api/orders/new - create an order (and an user if not authenticated) via post
* /api/status/{id} - modify the status (open, pending, complete, expired, cancelled)

more soon.
-sw
