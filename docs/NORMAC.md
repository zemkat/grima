# Working with MARC

If you want to work with the content of Bibs and Holdings records in
Alma, you'll have to work with them as MARC records. Grima uses the
Normac MARC library to allow you to read, add, delete, and modify records.
Many of these types of changes can be more easily handled with Normalization
Rules in Alma, but many cannot, especially if changes involve a set of
related records (such as copying parts of a MARC bibliographic record to
an electronic portfolio).

Below are some recipes for working with MARC records with Normac.
For most of these examples, assume we have already loaded a bib record
from Alma with code like this:

```
	$bib = new Bib();
	$bib->loadFromAlma($mms_id);
```

After making changes to the bib, it should be updated in Alma:

```
	$bib->updateAlma();
```


## Normac recipes

Delete all fields **by tag**:
```
 	oreach ($bib->getFields("909") as $field) {
		$field->delete();
	}
```


Delete all fields **by tag pattern**:
``` 
	foreach ($bib->getFields("9XX") as $field) {
		$field->delete();
	}
```


Add a **control field** (00X):
``` 
	$bib->addControlField('006','m     o  d        ');
```


Add a **data field** (with indicators and subfields):
```
	$bib->addDataField('502','  ',
		array(
			'b' => 'M.S.',
			'c' => 'University of Kentucky',
			'd' => '2018',
		)
	);
```


Change **field/subfield content**:
```
	foreach ($bib->getFields("502") as $field) {
		if ($field['b'] == "UK") {
			$field['b'] = "University of Kentucky";
		}
	}
```


Delete **subfields** by code:
```
	foreach ($bib->getFields("856") as $field) {
		foreach ($field->getSubfields('z') as $subfield) {
			$subfield->delete();
		}
	}
```

Print **content of subfields** by code:
```
	foreach ($bib->getFields("856") as $field) {
		foreach ($field->getSubfields('u') as $subfield) {
			print $subfield->data;
		}
	}
```

**Append a new subfield** at the end of a field:
```
	foreach ($bib->getFields("856") as $field) {
		$field->appendSubfield('z','Click here for resource');
	}

	# OR:

	$subfield = new ISO2709Subfield('z','Click here for resource');
	foreach ($bib->getFields("856") as $field) {
		$field->appendSubfield($subfield);
	}
```

**Insert a new subfield** before another subfield:
```
	# add $3 UKnowledge before the first $u
	foreach ($bib->getFields("856") as $field) {
		$field->insertSubfieldBefore('u', '3', 'UKnowledge');
	}

	# OR:

	$subfield = new ISO2709Subfield('3','UKnowledge');
	foreach ($bib->getFields("856") as $field) {
		$field->insertSubfieldBefore('u', $subfield);
	}
```
