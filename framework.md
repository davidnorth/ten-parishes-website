# How it works

If no HTML file is found by the HTTP server, public.php will handle the request.

Fist a Request object is constructed e.g.
$req->path
$req->method
$req->isGET()
$req->params

$req->params combines POST data, query string and dynamic path segments into a single array for ease of use in the page logic.


It looks at the request path and tries to find tha matching path within '@

The value in this hash corresponds to a php file in @pages which is then executed
e.g.

request: /about/team
matches @pages/about/team.php

Dynamic path segments example:
request: /parishes/wiveliscombe/artists/mary-smith
matches @pages/parishes/[parish_slug]/artists/[artist_slug].php

And these values go into the $req->params 

If no match, render @public/404.html
If there is a match, 

That file is executed with $req made available to it.

That file will include any page logic and for GET requests, the page template.
The page may return a response, stopping execution of the page php file e.g.

<?php
if ($req->isPost()) {
    $newName = $req->params['name'];
    $db->update("products", ["name" => $newName], ["id" => $request->id]);
    return redirect("/products/{$req->params['id']}");
}
<h1>FOr post, I won't be rendered</h1>


_init.php files
In the @pages directory, you can have _init.php files which are automatically included 
before any page logic is executed for that directory and its subdirectories. THey are executed in order
These can for example enforce auth rules (potentially cancelling the rest of the request) or 
set the current layout

All _init.php files encounted traversing the path are executed in order
$req is made available to them also and they can return a response.
So they are middleware essentially

Rendering
If a layout has been set in an _init.php file, it will wrap whatever is rendered by the specific
page php file.



# Directory structure
@pages          <-- Logic & Templates
@layouts        <-- php layout tempaltes
@framework      <-- Reusable code, not specific to this app, like the router
@lib            <-- Shared code for this app like utility functions
@storage        <-- SQLite DB 
@public         <-- Document Root. Cached HTML written here. Static assets
@public/@pages  <-- Front Controller. Entry point
@scripts <-- utility scripts. build tasks, maintainance etc


