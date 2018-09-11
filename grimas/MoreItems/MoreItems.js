document.addEventListener('DOMContentLoaded',()=>{
    const barcode_elt = document.getElementById('barcode');
    const enumeration_a_elt = document.getElementById('enumeration_a');
    const chronology_i_elt = document.getElementById('chronology_i');
    const description_elt = document.getElementById('description');
    const onUse = function(e) {
        barcode_elt.className = "form-control";
    }
    const onUpdate = function(e) {
        const enumeration_a = enumeration_a_elt.value;
        const chronology_i = chronology_i_elt.value;
        const description = enumeration_a +
            ( chronology_i ? '(' + chronology_i + ')' : '' );
        description_elt.value = description;
    };
    enumeration_a_elt.addEventListener('input',onUpdate);
    chronology_i_elt.addEventListener('input',onUpdate);
    barcode_elt.addEventListener('input',onUse);
    barcode_elt.addEventListener('click',onUse);
    barcode_elt.addEventListener('focus',onUse);
});
