Grima: Whispering into Alma's Ear
=======
by [Zemkat](https://works.bepress.com/kathrynlybarger/)

Welcome to **Grima**, the library that makes it easy to work with Alma APIs.

This is the main site for developers.

Please see [Getting Started](docs/GETTINGSTARTED.md){: .btn.btn-success} for
how to use Grima yourself, including a [grima
sandbox](https://grima-sandbox.zemkat.org) (read-only and fake-data
demonstration, but no setup), a [public grima](https://public-grima.zemkat.org)
(requires an [Alma API key](docs/APIKEY.md), but uses your own institutions
data), and lots of documentation on setting up Grima yourself, either on a
[desktop](docs/DESKTOP.md) or on a [server](docs/SERVER.md).

# Developer information

Whisper into Alma's ear using APIs to speed up workflows. With grima,
you can use small web-based tools to do tasks like:
* View / Print a MARC record
* View bibs, holdings, items in a hierarchy
* Delete a bib and all of its inventory
* Create a boundwith
* Quickly add lots of similar items to a set or serial
* ... whatever else you want to write with the grima library!

See the [new author information](docs/NEWAUTHOR.md) if you are familiar with
using grimas and want to write your own. See the [server
install](docs/SERVER.md) for how to install. See the [building
instructions](docs/BUILD.md) for information on the build process. See the [new
features](docs/WHATSNEW.md) for a list of new features.

## File structure
* [grimas](grimas) - most code, including the [main
library](grimas/grima-lib.php) and individual grimas like [PrintBib](grimas/PrintBib/PrintBib.php)
* [docs](docs) - documentation, including the [doxygen generated documentation](docs/dev)
of the main library, as well as several overview documents.
* [containers](containers) - where we keep the files for creating and deploying
containers, for example for [desktop](containers/desktop) and
[cloud](containers/cloud) containers, as well as the [documentation
builder](containers/docs-builder).

## License
This software is copyright Kathryn Lybarger and distributed under CC-BY-SA.

Unless otherwise stated, the software on this site is provided "as-is,"
without any express or implied warranty. In no event shall zemkat be held
liable for any damages arising from the use of the software. Please test
individual grimas on your own data (or sandbox data) before using extensively!

## Acknowledgements
* Thanks to [Jack Schmidt](https://github.com/jackschmidt) for everything.
* Thanks to the UK Libraries Cataloging and Database Integrity
for inspiration and testing.
* Thanks to Ex Libris for the underlying APIs, and to ELUNA for 
the community to work with them.
