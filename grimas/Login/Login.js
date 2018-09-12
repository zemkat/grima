(function(){

// Edge lacks:
// * TextEncoder for UTF-8 bytes to use strings in crypto
// * crypto.subtle.digest("SHA-1") to query well known passwords
// * crypto.subtle.deriveBits("PBKDF2") to hide human password from server

if (document.readyState == "complete") {
  setup();
} else {
  document.addEventListener( "DOMContentLoaded", setup );
}

function setup() {
  for ( const elt of document.querySelectorAll( "input[name='password']" ) ) {
    elt.form.addEventListener("submit", onSubmit);
    elt.addEventListener("input", onInput );
  }
}

function onInput( event ) {
  const elt = event.target;
  if ( elt.timer ) {
    clearTimeout( elt.timer );
  }
  markPasswordGood( elt );
  elt.timer = setTimeout( () => checkForWellKnownPassword(elt).catch(()=>{}), 300 );
}

function onSubmit( event ) {
  const form = event.target;
  if (form instanceof HTMLFormElement) {
    const passwordElt = form.elements.namedItem("password");
    const usernameElt = form.elements.namedItem("username");
    const institutionElt = form.elements.namedItem("institution");
    if ( (passwordElt && passwordElt.value)
      && (!passwordElt.value.startsWith( "PBKDF2-" ))
    ) {
      event.preventDefault();
      const username = usernameElt ? usernameElt.value : "user";
      const institution = institutionElt ? institutionElt.value : "institution";
      const password = passwordElt.value;
      const checkPromise = checkForWellKnownPassword( passwordElt )
      const hashPromise = hash( password, `grima-clientside-login-v1:${institution}:${username}` )
      Promise.all( [ checkPromise, hashPromise ] )
      .then( ([_,hash]) => {
        passwordElt.value = hash;
        form.submit();
      } )
      .catch( err => markPasswordBad( elt, err.toString(), "Cannot send this password" ) )
      return false;
    }
  }
}

function markPasswordBad( elt, validityMessage, buttonText ) {
  elt.setCustomValidity( validityMessage.toString() );
  for (const btnelt of elt.form.querySelectorAll('input[type="submit"]') ) {
    btnelt.classList.add( "btn-danger" );
    btnelt.value = buttonText;
  }
}

function markPasswordGood( elt ) {
  elt.setCustomValidity( "" );
  for (const btnelt of elt.form.querySelectorAll('input[type="submit"]') ) {
    btnelt.classList.remove( "btn-danger" );
    btnelt.value = "Submit";
  }
}

const checked = new Map();
function checkForWellKnownPassword( elt ) {
  if ( (elt instanceof HTMLInputElement)
    && (window.crypto)
    && (window.crypto.subtle)
  ) {
    if (checked.has(elt.value)) {
      const wellKnown = checked.get(elt.value);
      if (wellKnown) {
        const err = `That password has been used by ${wellKnown} compromised accounts.`;
        const btn = "Don't send such a well-known password to server";
        markPasswordBad( elt, err, btn );
        return Promise.reject( err );
      } else {
        markPasswordGood( elt );
        return Promise.resolve();
      }
    }
    return window.crypto.subtle
    .digest("SHA-1", bin(elt.value) )
    .then( sha1 => hex(sha1) )
    .then( sha1 => fetch( `https://api.pwnedpasswords.com/range/${sha1.substring(0,5)}`)
      .then( response => response.text() )
      .then( text => {
        for (const line of text.split(/\r\n/g)) {
          const [ rest, wellKnown ] = line.split(/:/g);
          if (sha1.substring(5) === rest.toLowerCase()) {
            const err = `That password has been used by ${wellKnown} compromised accounts.`;
            const btn = "Don't send such a well-known password to server";
            markPasswordBad( elt, err, btn );
            checked.set( elt.value, wellKnown );
            return Promise.reject(err);
          }
        }
        markPasswordGood( elt );
        checked.set( elt.value, 0 );
        return Promise.resolve();
      })
    );
  } else {
    return Promise.resolve();
  }
}

function hash( password, salt_seed ) {
  const name = "PBKDF2";
  const hash = "SHA-512";
  const iterations = 1000000; // 0.7 seconds on my 2013 laptop
  if (!(window.crypto && window.crypto.subtle)) {
    if (window.location.protocol === "http:") {
      return Promise.reject( "Client side crypto not available. Ask your server admin to use https.");
    } else {
      return Promise.reject( "Client side crypto not available. Please use a supported browser.");
    }
  }
  return window.crypto.subtle
    .digest( hash, bin( salt_seed ) )
    .then( salt => window.crypto.subtle
    .importKey( "raw", bin(password), {name}, false, ["deriveBits"])
    .then( (pw) => window.crypto.subtle
    .deriveBits( {name, salt, iterations, hash}, pw, 128 ) )
    .then( (key) => `PBKDF2-${hash}\$${iterations}\$${hex(salt)}\$${hex(key)}` ) )
    .catch( (err) => {
      if (err.name === "PBKDF2") {
        return Promise.reject( "Client side crypto not available. Please don't use Edge.");
      } else {
        throw err;
      }
    });
}

function bin(str) {
  return new TextEncoder("utf-8").encode(str);
}

function hex(bin) {
  return Array.prototype.slice
    .call(new Uint8Array(bin))
    .map(x => [x >> 4, x & 15])
    .map(ab => ab.map(x => x.toString(16)).join(""))
    .join("");
}

})();
