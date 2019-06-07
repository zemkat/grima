              <form method="post" action="MoreItems.php">
                <input type="hidden" name="mms_id" value="<?=$e($item['mms_id'])?>">
                <input type="hidden" name="holding_id" value="<?=$e($item['holding_id'])?>">
                <input type="hidden" name="item_pid" value="<?=$e($item['item_pid'])?>">
                <input type="hidden" name="barcode_model" value="<?=$e($item['barcode'])?>">
				<div class="row">
				  <div class="form-row col-6 pb-4">
					<label class="col-form-label col-4" for="enumeration_a">Enum A:</label>
					<input class="col-8 form-control" name="enumeration_a" id="enumeration_a" size="20" value="<?=$e($item['enumeration_a'])?>"/>
				  </div>
				  <div class="form-row col-6 pb-4">
					<label class="col-4 col-form-label" for="enumeration_b">Enum B:</label>
					<input class="col-8 form-control" name="enumeration_b" id="enumeration_b" size="20" value="<?=$e($item['enumeration_b'])?>"/>
				  </div>
				  <div class="form-row col-6 pb-4">
					<label class="col-4 col-form-label" for="chronology_i">Chron I:</label>
					<input class="col-8 form-control" type="text" name="chronology_i" id="chronology_i" size="20" value="<?=$e($item['chronology_i'])?>">
				  </div>
				  <div class="form-row col-6 pb-4">
					<label class="col-4 col-form-label" for="chronology_j">Chron J:</label>
					<input class="col-8 form-control" type="text" name="chronology_j" id="chronology_j" size="20" value="<?=$e($item['chronology_j'])?>">
				  </div>
				  <div class="form-row col-12 pb-4">
					<label class="col-3 col-form-label" for="description">Description:</label>
					<input class="col-9 form-control" type="text" name="description" id="description" size="20" value="<?=$e($item['description'])?>"/>
				  </div>
				  <div class="form-row col-12 pb-4">
					<label class="col-3 form-check-label" for="barcode">Barcode:</label>
					<input class="col-9 form-control znew" type="text" name="barcode" id="barcode" size="20" placeholder="SCAN NEW BARCODE"/>
				  </div>
				</div>
                <input type="hidden" name="adding" value="true"> <!-- override button -->
                <input class="btn btn-primary btn-lg active" type="submit" value="Add Item">
              </form>
