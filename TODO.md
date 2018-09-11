# TODO


## Code changes

### Use temp dir for config.sql by default.

  * Actually, check if config is writable, if so, use it (good for local install)
  * Otherwise, use the temp dir.

### On the login page
  * make institution a drop down
  * (DONE) verify login password does actually work
  * (DONE) value=wiggler?
  * drop down!
  * name fields properly for password saving
  * (DONE) password hash with argon or password verification with pbkdf
    * confusingly it uses PBKDF on client side and Argon on server side
    * but this does work because BOTH are used for each password, 
      * client sends pbkdf(pass),
      * server stores Argon(pbkdf(pass)),

### On the add institution page:

  * include a link to an Ex Libris documentation for API keys and such
    * https://developers.exlibrisgroup.com/dashboard/application
  * include default server as value not just place holder?
  * maybe a drop down for server
  * maybe also check that not everything is blank
  * check that API key works
  * name fields properly for password saving

### Logout
  * doesn't need submit button
  * maybe splat instead of form

### AdminResetPassword
  * Asks for username, checks you are admin, asks for new password, twice, resets

### AddUser / AddInstitution
  * Ask for password twice and verify they are equal

## Directory reorg:

  * for the source PHP
    * lib/
    * lib/templates/
    * grimas/
    * splats/ (or grimas/splats ?)
  * docs/ for the source of the docs (.MD mostly, imgs)
  * dist/ for the generated materials (phar, html, both docs/ and doxygen/)
  * README.md main splash (especially for github)

## Make a few urls:

### grima.zemkat.org should be a splash page
  * Overall documentation page
  * Install instructions for 
    *   SysAdmin
    *   Home user
  * Account creation instructions
    *   SysAdmin only
  * Usage instructions for
    * New user
  * Developer instructions
    * Technical user (make your own grimas) (doxygen type stuff)
  * Link to github releases
  * Link to sandbox
  * Link to UK's install

### sandbox
  * have APIKEY and server set to
    * l7xx9e8bb7a573e44e6ca9699cb998cffb72
    * https://api-na.hosted.exlibrisgroup.com
  * user accounts for guest
### uk (grima-uk.zemkat.org, uk.grima.zemkat.org, grima.zemkat.org/uk)
  * have APIKEY and server set to
    * l7xx4d51f845cb634bb6a1cc0400fd3227d0
    * https://api-na.hosted.exlibrisgroup.com
  * user accounts for:
    * kjlyb00, jaime, jennifer richmond, denise hunter, gwen curtis, marsha seamans, josh monroe, emma montgomery, elizabeth laumas, jen (ER), trishani, cindy, julene
  * send emails to each user letting them know URL and password

### CI
  * just clones itself every hour and deletes its config

## Make a bunch of screenshots
  * bookmarklet drop down
    * Is it easy to make a big bunch of bookmarks? (we started to look, found the answer was yes, but didnt quite do it)
  * Alma and skinny grima
  * Bookmarks Webpage for everybody where you can drag grimas
    * (have an input at the top that defaults to the given grima's website, so every grima install has it,
      butalso lets them change it to their own)


## prepare git for github release
  * get all commits squashed
  * get documentation into the doc workflow, so html is just automatically made
    * I think this is done
