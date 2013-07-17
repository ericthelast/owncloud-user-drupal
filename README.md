owncloud-user-drupal
====================

The owncloud authentication using Drupal as a backend.

This app is another flavor of the original app that was
built for Drupal 6, worked with Owncloud 4, and relied
on connecting to the database directly for user info.

With this flavor, instead of connecting directly to your
Drupal database, it uses [REST](http://en.wikipedia.org/wiki/Representational_state_transfer) calls to get the user info.

To use this app, your Drupal site must be able to communicate
using REST calls.  The easiest way to do this is to install
the [Services](https://drupal.org/project/services) module, 
enable the rest server module, and use session authentication
for security.

You'll also need to create an endpoint and add the following 
resources:

 - system.connect
 - user.login
 - user.logout
 - user.index
 - user.retrieve

This app also relies on the [httpful](https://github.com/nategood/httpful) 
library from [nategood](https://github.com/nategood) for making the
REST calls.
