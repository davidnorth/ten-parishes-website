
Please review @framework.md


The initial plan is just to implement a very minimal PHP framework along with a few test pages to check routing works.


Let's build out just the following


GET @pages/index.php -> responds to /

GET @pages/about/team.php -> responds to /about/team

GET @pages/parishes/[parish_slug]/artists/[artist_slug].php -> responds to /parishes/wiveliscombe/artists/mary-smith or similar

GET @pages/admin/index.php -> responds to /admin . Don't bother about auth, just use to test switching layouts

@pages/contact.php -> handles both GET and POST as described in @framework.md. have a form and dummy response when posted. Post redirects back to GET /contact


Add _init.php file at @pages/_init.php
This will set the layout that will wrap whatever the page renders
@pages/admin/_init.php will specify a different layout







