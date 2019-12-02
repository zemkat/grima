# PublicLogin - login to grima using your API key

This grima stores your API key in your web browser. Grima needs an API
key in order to talk to Alma on your behalf. [Here](../../docs/APIKEY.md)
are instuctions on [how to get an Alma API key](../../docs/APIKEY.md).

The API key will be shared with whomever runs this grima
([grima-public.zemkat](https://grima-public.zemkat.org) is run by Kathryn
Lybarger, and the key is not stored on our server).

Your institutional policies are unlikely to allow this to be your
standard workflow, so once you've tried out public grima for a few weeks,
you'll likely want to install your own copy. Follow the instructions at
[Getting Started](../../docs/GETTINGSTARTED.md).

This grima is called automatically if you try to access another grima without
an API key (when the grima server is not configured with a user database).
After entering the API key, you will be redirected to the grima you initially
tried.

## Input
* Alma API key and API server as described [here](../../docs/APIKEY.md).

## API requirements
* (none)
