(function(){

if (document.readyState == "complete") {
  setup();
} else {
  document.addEventListener( "DOMContentLoaded", setup );
}

function setup() {
  for ( const elt of document.querySelectorAll( "input[name='password']" ) ) {
    elt.form.addEventListener("submit", onSubmit);
  }
}

function onSubmit( event ) {
  const form = event.target;
  if (form instanceof HTMLFormElement) {
    const passwordElt = form.elements.namedItem("password");
    const usernameElt = form.elements.namedItem("username");
    const institutionElt = form.elements.namedItem("institutionElt");
    if ( (passwordElt && passwordElt.value)
      && (!passwordElt.value.startsWith( "PBKDF2-" ))
    ) {
      event.preventDefault();
      const username = usernameElt ? usernameElt.value : "user";
      const institution = institutionElt ? institutionElt.value : "institution";
      const password = passwordElt.value;
      hash( password, `grima-clientside-login-v1:${institution}:${username}` )
      .then( (hash) => {
        passwordElt.value = hash;
        form.submit();
      } )
      .catch( err => {
        passwordElt.setCustomValidity( err.toString() );
      } );
      return false;
    }
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
    .digest( "SHA-512", bin( salt_seed ) )
    .then( salt => window.crypto.subtle
    .importKey( "raw", bin(password), {name}, false, ["deriveBits"])
    .then( (pw) => window.crypto.subtle
    .deriveBits( {name, salt, iterations, hash}, pw, 128 ) )
    .then( (key) => `PBKDF2-${hash}\$${iterations}\$${hex(salt)}\$${hex(key)}` ) )
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
