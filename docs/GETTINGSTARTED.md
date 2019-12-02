# Getting Started!

This document leads you to the right documentation to get started.

## I want to try Grima on fake data!

Great, go to the [Grima sandbox](https://grima-sandbox.zemkat.org/) hosted by
Kathryn Lybarger and using (read-only) fake data from Ex Libris.

## I want to try Grima on my own libraries data!

You'll need to get an [Alma API Key](APIKEY.md) first.

Then go to the [public Grima](https://public-grima.zemkat.org/) and follow
instructions. This one is hosted by Kathryn Lybarger, but it will have access
to your institution's data by using your Alma API key.

You'll be sharing your API key with Kathryn, which is probably fine for
testing, but eventually you'll want to stop doing that. We don't store the key,
so once you stop using the public grima, you'll also stop sharing that key.

## I want my fellow librarians to use Grima!

Institutional concerns and policies will probably mean you need to install
grima at your institution. (Handing out API keys willy nilly to employees who
then hand them to Kathryn is unlikely to be a sustainable model.)

If each person who wants to use grima can install software on their own desktop
computer, see [Grima on the Desktop](DESKTOP.md). This uses the nice fact that
computers are ridiculously powerful, and that your desktop computer can pretend
to have another computer and another internet inside it running your own
website! Possibly your IT people will think that is too weird and want to be
more involved.

If you (or your IT people) can install web-apps onto a web-server, see [Grima
on the Server](SERVER.md). This includes both 20th century servers and cloud
native deployments.

## I want to write my own Grimas!

Grimas help simplify your workflow, but to do so they need to match your
workflow. Write the perfect grima yourself, by following these instructions
for [New Authors](NEWAUTHOR.md).

## I want to build my own grima images to deploy at my institution

Starting with 2019-12 release, we will provide stable images at
`docker.io/zemkat/grima-cloud:2019-12` etc. You may want to use
the instructions for deploying [Grima on the Cloud](CLOUD.md).

In order to build your own images, alter the recipes in the
[Makefile](../Makefile) (on GitHub) to point to your own image name. You'll
also need to update any of your cloud deployment configuations to use your
images as well.

The first build of cloud images requires around 15 minutes per architecture,
but using the build cache further builds (altering only the PHP) should only
take a few seconds.

