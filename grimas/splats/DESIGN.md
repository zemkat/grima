Most GrimaTasks's will use the standard head and jumbotron body

So we need to specify:
Splat "splat/generic.php", but..
    When you get to $t('head') go to        "splat/head.php", but..
    When you get to $t('head-meta') go to   "splat/head-meta.php", but..
    When you get to $t('head-style') go to  "splat/head-style.php", but..
    When you get to $t('head-script') go to "splat/head-script.php", but..
    When you get to $t('body') go to        "splat/jumbotron.php", but...
    When you get to $t('form') go to        "splat/form.php", but..
    When you get to $t('messages') go to    "splat/messages.php", but..


So (a) if no template specified, try "splat/$templateName.php"
