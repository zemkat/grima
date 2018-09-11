const fs        = require('fs');
const path      = require('path');
const jsdom     = require('jsdom');
const { JSDOM } = jsdom;

function relativeVersion( filename, baseDir ) {
  "use strict";
  const onLoad = dom => {
    const window = dom.window;
    const document = window.document;
    makeLinksRelative( document, baseDir );
    const html = '<!DOCTYPE html>\n' + document.documentElement.outerHTML
    fs.writeFile( filename, html, (err) => err?console.log({err}):false );
  }
  const jsdom_init = ( dom ) => {
    const window = dom.window;
    window.addEventListener("load",()=>onLoad(dom));
  }
  const config = { runScripts: "dangerously", resources: "usable" };
  return JSDOM.fromFile( filename, config ).then(jsdom_init).catch( err => console.log(err) );

}

function makeLinkRelative( elt, attributeName, baseDir ) {
  const oldLink = elt.getAttribute( attributeName );
  const newLink = path.relative( baseDir, oldLink ) || ".";
  elt.setAttribute( attributeName, newLink );
}

function makeLinksRelative( document, baseDir ) {
  "use strict";
  for (const elt of document.querySelectorAll('a[href^="/"]')) {
	makeLinkRelative( elt, "href", baseDir );
  }
  for (const elt of document.querySelectorAll('link[href^="/"]')) {
	makeLinkRelative( elt, "href", baseDir );
  }
  for (const elt of document.querySelectorAll('script[src^="/"]')) {
	makeLinkRelative( elt, "src", baseDir );
  }
  for (const elt of document.querySelectorAll('img[src^="/"]')) {
	makeLinkRelative( elt, "src", baseDir );
  }
}

function recurse( dir, htmlBaseDir, realBaseDir ) {
  for (const dirEnt of fs.readdirSync( realBaseDir, {withFileTypes:true})) {
    if (dirEnt.isDirectory()) {
      const newDir = dirEnt.name;
      if (newDir==="dev") continue;
      const newHtmlBaseDir = path.join( htmlBaseDir, newDir );
      const newRealBaseDir = path.join( realBaseDir, newDir );
      recurse( newDir, newHtmlBaseDir, newRealBaseDir );
    } else if (dirEnt.name.endsWith(".html")) {
      const file = path.join( realBaseDir, dirEnt.name );
      relativeVersion( file, htmlBaseDir );
    }
  }
}

recurse( ".", "/", "./dist" );
