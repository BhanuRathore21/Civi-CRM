{literal}
<script>

showHideIhm();
cj('input[name="custom_239"]').on('change', function() {
  showHideIhm();
});
showHideAck();
cj('input[name="custom_236"]').on('change', function() {
  showHideAck();
});

function showHideIhm() {
  var value = cj('input[name="custom_239"]:checked').val();

  // Common fields
  var block1 = [ 241, 242, 236 ];
  // In-Honor only fields
  var block2 = [ 46, 47 ];

  switch (value) {
    case 'Honor':
      // change Honoree / Deceased labels
      cj("label[for='custom_241']").text('Honoree First Name');
      cj("label[for='custom_242']").text('Honoree Last Name');
      // show/hide blocks of custom fields
      block1.forEach(function(c) { showHideRow(c, 'show'); });
      block2.forEach(function(c) { showHideRow(c, 'show'); });
      break;
    case 'Memory': 
      // change Honoree / Deceased labels
      cj("label[for='custom_241']").text('Deceased First Name');
      cj("label[for='custom_242']").text('Deceased Last Name');
      // show/hide blocks of custom fields
      block1.forEach(function(c) { showHideRow(c, 'show'); });
      block2.forEach(function(c) { showHideRow(c, 'hide'); });
      
      break;
    default: // no value selected, var is undefined
      // show/hide blocks of custom fields
      block1.forEach(function(c) { showHideRow(c, 'hide'); });
      block2.forEach(function(c) { showHideRow(c, 'hide'); });
      // reset the Acknowledge choice and hide it
      cj('input[name="custom_236"]').removeAttr('checked');
      showHideAck();
  }
}

function showHideAck() {
  var value = cj('input[name="custom_236"]:checked').val();

  // Acknowlegment fields
  var block1 = [ 237, 238, 236, 240 ]; // for all
  var block2 = [ 229 ]; // for email
  var block3 = [ 235, 52, 53, 230, 231, 232 ]; // for card

  switch (value) {
    case 'Email':
      block1.forEach(function(c) { showHideRow(c, 'show'); });
      block2.forEach(function(c) { showHideRow(c, 'show'); });
      block3.forEach(function(c) { showHideRow(c, 'hide'); });
      break;
    case 'Card':
      block1.forEach(function(c) { showHideRow(c, 'show'); });
      block2.forEach(function(c) { showHideRow(c, 'hide'); });
      block3.forEach(function(c) { showHideRow(c, 'show'); });
      break;
    default: // no value selected, var is undefined
      block1.forEach(function(c) { showHideRow(c, 'hide'); });
      block2.forEach(function(c) { showHideRow(c, 'hide'); });
      block3.forEach(function(c) { showHideRow(c, 'hide'); });
  }
}

function showHideRow(c, s) {
  if (s == 'show') {
    cj('#editrow-custom_' + c).show();
    cj('#helprow-custom_' + c).show();
  } else {
    cj('#editrow-custom_' + c).hide();
    cj('#helprow-custom_' + c).hide();
  }
}

</script>
{/literal}
