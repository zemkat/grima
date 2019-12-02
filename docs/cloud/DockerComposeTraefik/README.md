# Warning

Don't really use this. It merely demonstrates grima can scale horizontally.

It is insecure: traefik has complete control over all of your containers, even
ones unrelated to grima. It was just the first load balancer I found that
worked. Docker discontinued their version of this in 2018, so it's not like
the insecurity wasn't officially blessed, but it does defeat the purpose of
containerization.

It is useless: grima doesn't need to scale horizontally. If you need to redeploy,
then your users can wait a few seconds. If you need more capacity then something
is malfunctioning. A staff of 500 should require 1 core and 512MB ram.

It is still useless: even if you did have a staff of 500, this is running in
docker-compose, which only runs on one (virtual) machine. Grima is using the
apache webserver, which already makes use of all available cores (it already
"load balances" a single server). So running multiple apaches won't result
in more effective usage of existing cores (it will run slower due to overhead).

Why is it here? To check that grima is correctly using cloud native ideas.

Hopefully it will be replaced by kubernetes and docker swarm examples that
use load balancers that work across multiple servers.
