		<div class="card">
		  <div class="card-header">
			<h2 class="card-title"><?=isset($titleRaw)?$titleRaw:$e($title)?></h2>
		  </div>
		  <div class="card-body">
			<?php foreach( (array) $body as $template) {
				$t($template);
			}?>
          </div>
        </div>
