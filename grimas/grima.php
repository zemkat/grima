<?php
#
#       grima.php - pass messages to grima minions, display usage
#
#       (c) 2017 Kathryn Lybarger. CC-BY-SA
#
require_once("grima-setup.php");
readfile('top.html');
require_once("grima-lib.php");

function do_redirect($url) {
        if(!headers_sent()) header("Location: $url");
        $url_html = htmlspecialchars($url);
        echo "<!DOCTYPE html><html><head><title>Redirect</title><meta http-equiv=refresh content='1; url=$url'></head><body><a href='$url_html'>Go here: $url_html</a></body></html>\n";
        exit;
}

$msg = isset($_REQUEST["msg"]) ? $_REQUEST["msg"] : "";

#
#       print <mms_id>
#
if (preg_match("/^print( .*)/",$msg,$m)) {
        do_redirect("print.php?mms_id=" . ltrim($m[1]));
}

#
#      hierarchy <mms_id>
#
if (preg_match("/^hierarchy( .*)/",$msg,$m)) {
        do_redirect("hierarchy.php?mms_id=" . ltrim($m[1]));
}

#
#       filter <mms_id> with <filter>
#
if (preg_match("/(norm|normalize|filter) (\d+) (with (.*))/",$msg,$m)) {
        if (is_mmsid($m[2])) {
                do_redirect("zf.php?mms_id=" . $m[2] . "&filter=" . $m[4]);
        } else {
                # MMS ID form
        }
}

#
#       add mfhd <template> to <mms_id>
#
#
if (preg_match("/^add mfhd(.*)/",$msg,$m)) {
        do_redirect("add_mfhd.php?query=" . ltrim($m[1]));
}

#
#       add tree <template> to <mms_id> with barcode <barcode>
#
#

#
#       insert oclcnum <number> into <mms_id>
#
if (preg_match("/insert oclcnum(.*)/",$msg,$m)) {
        do_redirect("insert-oclcnum.php?query=" . urlencode(ltrim($m[1])));
}

#
#       overlay <mms_id> with <oclcnum>
#       maybe more direct, but needs both APIs
#

#
#       unmerge phys/electronic
#

#
#       combine multiple bib/mfhd/item chains
#

# FORMS
# PRINT
# add this to object?
?>
      <div class='panel panel-default bib'>
        <div class='panel-heading'>
          <h1 class='panel-title'>Print Record</h1>
        </div>
        <div class='panel-body'>
          <dl class='dl-horizontal'>
            <form method='post' action='print.php'>
              <p>Print record 
                <input name='mms_id' size='20'/>
                <input type='submit' value='submit' />
              </p>
            </form>
           </dl>
         </div>
      </div>
      <div class='panel panel-default bib'>
        <div class='panel-heading'>
          <h1 class='panel-title'>View Hierarchy</h1>
        </div>
        <div class='panel-body'>
          <dl class='dl-horizontal'>
            <form method='post' action='hierarchy.php'>
              <p>View 
               <input name='mms_id' size='20'/>
               <input type='submit' value='submit' />
              </p>
            </form>
          </dl>
        </div>
      </div>
      <div class='panel panel-default bib'>
        <div class='panel-heading'>
          <h1 class='panel-title'>
            <span class='titleproper'>Insert OCLC Number</span>
          </h1>
        </div>
        <div class='panel-body'>
          <dl class='dl-horizontal'>
            <form method='post' action='insert-oclcnum.php'>
              <p>Insert oclcnum <input name='oclcnum' size='15' /> into
                <input name='mms_id' size='20'/>
                <input type='submit' value='submit' />
              </p>
            </form>
          </dl>
        </div>
      </div>
    </div>
  </body>
</html>
