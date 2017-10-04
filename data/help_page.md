#W.A.C. Knowledge Yielder - API Guide

These are the commands/endpoints implemented in the [Westeran Airlines
Consortium Knowledge Yielder (WACKY)
server](https://wacky.jlparry.com). 

In all cases below, the response might be an error message instead
of the "happy path" response shown.

##Information requests

The <strong>Info</strong> server dishes public information about the state of the
current WACKY business session. It can be invoked from inside
your app, or from your browser's location field.

No WACKY data is changed by these requests, nor was any harmed
during the app's development.

If using these under program control, 
    `$response = file_get_contents('https://wacky.jlparry.com/xxx');`
will retrieve the response to a request sent to "xxx", which
refers to an endpoint described below, and may need additional
parameters.

If using these from your browser, `https://wacky.jlparry.com/xxx` would be 
the URL to put into your browser's location field, with "xxx" set per the following..

### /info/airlines/XXX

Purpose: List the participating airlines  
Returns: A list of active airlines, the data for an explicitly identified one,
or else an error message  

### /info/airports/XXX

Purpose: List the airports used by WAC
Returns: A list of known airports, the data for an explicitly identified one,
or an error message  

### /info/airplanes/XXX

Purpose: List the aircraft recognized by WAC
Returns: A list of allowed aircraft, the data for an explicitly identified one,
or an error message  

### /info/regions/XXX

Purpose: List the regions recognized by WAC
Returns: A list of allowed regions, the data for an explicitly identified one,
or an error message  
