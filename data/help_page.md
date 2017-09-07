#Panda Research Center - API Guide

These are the commands/endpoints implemented in the Panda Research Center (PRC)
server. Some are intended for use a a bot factory webapp, but some can
be invoked from your browser location field, for testing.

In all cases below, the response might be an error message instead
of the "happy path" response shown.

##Work API

These services perform work on behalf of your webapp, changing the
state of the PRC data. The services are only callable by your
webapp, i.e. they are not intended to be browser-accessible.

If using these under program control, 
    `$response = file_get_contents('https://umbrella.jlparry.com/xxx?key=YOUR_API_KEY');`
will retrieve the response to a request sent to "xxx", which
refers to an endpoint described below, and may need additional
parameters,

The "key" query parameter is required for any request meant to be used only
by a bot factory app request. The value is an API key for your webapp, good for
the current PRC trading session, and provided to you through the "registerme"
endpoint. This is different from the super secret token (password) you use to
login to the Umbrella webapp and change deployment settings.

These services are implemented by the <strong>Work</strong> controller.

The requests below are shown in roughly the order that your factory app
might reasonably use them.

### /work/registerme/TEAM/PASSWORD

Purpose: Establish a trading session on PRC for your factory  
Returns: "Ok KEY", where KEY is your API key, or an error message

Create an API key for your authenticated factory.
Without an API key, you cannot request any state-changing
services.

A factory can have only one API key valid at a time.
If you have one currently, this service will effectively "reboot" you,
i.e. eliminate any parts attributed to you and reset your starting funds
balance. This might apply if your app or server crashes, and your API key
is no longer accessible.

A reminder: you will lose any balance earned so far.

<strong>Do not store your password inside your team repo. I recommend storing
your password in a <em>properties</em> table of some sort inside your database.
If you redeploy your app on the deployment testing server, your database will
be reset and you will have to "register" again.</strong>

You could also save your API key in a text file (git ignored, of course) 
inside your <em>data</em> folder.

### /work/buybox

Purpose: purchase a box of random parts for your factory to use  
Returns: an array of parts certificates, in JSON format, or else an error message.  

Each box contains 10 random bot parts.
The purchase price of $100 per box is deducted from your factory's balance.


### /work/mybuilds

Purpose: Requests any newly built parts for this factory  
Returns: an array of parts certificates, in JSON format  

Each bot factory makes one specific part during a business cycle of the PRC.
The factory gets to "make" one part every 10 seconds. This method creates
certificates of authenticity for your recent parts built, for up to 10
parts (the maximum that can be queued for your factory).
The "last built" timestamp resets every time this method is called, so calling 
it every nine seconds will result in no production for your factory!

There is no cost to your factory for these parts.

### /work/recycle/PART1/PART2/PART3

Purpose: Ask the PRC to recycle up to three parts that you do not want  
Parameters: the certificate codes for the parts that comprise your bot  
Returns: "Ok AMOUNT" (where AMOUNT is the value credited to your account balance) or an error message  

The PRC will automatically credit your factory's balance, if the request
checks out.
The certificates for any pieces "consumed" will be voided.

### /work/buymybot/PART1/PART2/PART3

Purpose: Ask the PRC to buy an assembled bot from you  
Parameters: the certificate codes for the three parts that comprise your bot  
Returns: "Ok AMOUNT" (where AMOUNT is the value credited to your account balance) or an error message  

The PRC will automatically credit your account balance, if the request checks out.
The certificates for any pieces "consumed" will be voided.

### /work/rebootme

Purpose: Restart your bot factory's participation in the current trading session  
Returns: "Ok AMOUNT" (where AMOUNT is the starting balance assigned to you) or an error message  

If successful, any parts certificates assigned to your factory will be voided,
and your balance will be reset to the starting amount, i.e. $1000.
Your API key remains the same.

Use this service if your app or its database get messed up for any reason.

### /work/goodbye

Purpose: Destroy your plants' PRC trading session  
Returns: "Ok" or an error message  

Your API Key is invalidated, as well as any parts certificates you held, and
your balance is reset to zero. Your factory will need to "register" again

Use this is done developing or debugging for the day, and you want to hide your app's
performance (or lack thereof) from classmates.

##Information requests

The <strong>Info</strong> server dishes public information about the state of the
current PRC trading session. It can be invoked from inside
your app, or from your browser's location field.

No PRC data is changed by these requests, nor was any harmed
during their development.

If using these under program control, 
    `$response = file_get_contents('https://umbrella.jlparry.com/xxx');`
will retrieve the response to a request sent to "xxx", which
refers to an endpoint described below, and may need additional
parameters.

If using these from your browser, `https://umbrella.jlparry.com/xxx` would be 
the URL to put into your browser's location field, with "xxx" set per the following..

### /info/balance/TEAM

Purpose: Ask for the balance that a factory has  
Parameters: TEAM is the factory name  
Returns: "Ok AMOUNT" (where AMOUNT is the current balance for that factory) or an error message  

### /info/scoop/TEAM

Purpose: Get the scoop on a factory  
Parameters: TEAM is the factory name  
Returns: the public data known about a factory, or else an error message  

Use this to see what the PRC thinks a given factory has, in terms
of its balance and the parts it has on hand.

### /info/verify/CACODE

Purpose: Identify a part  
Parameters: CACODE is a certificate authentication code  
Returns: the data known about a part, identified by its certificate token value  

### /info/whomakes/PARTTYPE

Purpose: Identify the factories building a specific part  
Parameters: PARTTYPE is a two-letter part type (model and piece)  
Returns: A list of the factories making the designated part  

### /info/whoami?key=APIKEY

Purpose: Test if you have a PRC session  
Parameters: APIKEY is a factory's API key  
Returns: The factory name the PRC associates with your API key  

This should return your factory name, if you have a PRC session.  

<strong>DO NOT share the API key with anyone else!! Destroy your session data.
Eat or shred any piece of paper you wrote it down on.
I think you get the picture.</strong>

### /info/job/TEAM

Purpose: Identify a factory's job  
Parameters: TEAM is the factory name  
Returns: The specific part that a factory is manufacturing during the current trading session,
or an error message  

### /info/teams

Purpose: List the participating factories  
Returns: A list of active factories, or else an error message  
