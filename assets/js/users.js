
jQuery(document).ready(function($){
      nameSelect.prop('disabled', false);
    } else {
      nameSelect.prop('disabled', true);
    }
  });

  // Handle adding built certification
  
  const nonExpiringCerts = [
    "Accredited Marine Surveyor (AMS)",
    "Surveyor Associate (SA)",
    "Certified Marine Surveyor (CMS)",
    "Associate Marine Surveyor",
    "Apprentice Marine Surveyor",
    "Certified RV Technician",
    "Service Manager",
    "Service Writer/Advisor",
    "Parts Manager",
    "Parts Specialist",
    "USCG Approved Equipment",
    "STCW Certification",
    "State-Licensed Propane Technician"
  ];

  $('#lcsi_cert_name').on('change', function() {
    const selected = $(this).val();
    if (nonExpiringCerts.includes(selected)) {
      $('#lcsi_expires_wrap').hide();
    } else {
      $('#lcsi_expires_wrap').show();
    }
  });


$('#lcsi-add-cert-built').on('click', function(){
    var name = $('#lcsi_cert_name').val();
    var issue = $('#lcsi_cert_issue').val();
    var expires = $('#lcsi_cert_expires').val();
    if(!name) return alert('Please select a certification.');
    var wrapper = $('#lcsi-certs-wrapper');
    var index = wrapper.children('.lcsi-cert-row').length;
    var built = '<div class="lcsi-cert-row" data-index="'+index+'">'
      + '<input type="text" name="lcsi_certs['+index+'][title]" value="'+name+'" />'
      + '<input type="text" name="lcsi_certs['+index+'][abbr]" placeholder="Abbrev" style="width:80px;" />'
      + '<select name="lcsi_certs['+index+'][placement]">'
      + '<option value="post">Post-nominal</option>'
      + '<option value="pre">Pre-nominal</option>'
      + '</select>'
      + '<input type="text" name="lcsi_certs['+index+'][organization]" placeholder="Organization" />'
      + '<input type="date" name="lcsi_certs['+index+'][issue_date]" value="'+issue+'" />'
      + '<input type="date" name="lcsi_certs['+index+'][expires]" value="'+expires+'" />'
      + '<input type="hidden" id="lcsi_certs_'+index+'_logo_id" name="lcsi_certs['+index+'][logo_id]" />'
      + '<button type="button" class="button lcsi-upload-logo" data-target="lcsi_certs_'+index+'_logo_id">Select Logo</button>'
      + '<button type="button" class="button lcsi-remove-cert">Remove</button>'
      + '</div>';
    wrapper.append(built);
    // reset builder
    $('#lcsi_cert_category').val('');
    $('#lcsi_cert_name').empty().append('<option value="">Select Cert</option>').prop('disabled', true);
    $('#lcsi_cert_issue, #lcsi_cert_expires').val('');
  });

  // Manual add certification
  $('#lcsi-add-cert').on('click', function(){
    var wrapper = $('#lcsi-certs-wrapper');
    var index = wrapper.children('.lcsi-cert-row').length;
    // template defined previously or replicate similar to above if needed
  });

  // Remove certification row
  $('#lcsi-certs-wrapper').on('click', '.lcsi-remove-cert', function(){
    $(this).closest('.lcsi-cert-row').remove();
  });

  // Media uploader
  var frame;
  $('#lcsi-certs-wrapper').on('click', '.lcsi-upload-logo', function(e){
    e.preventDefault();
    var button = $(this);
    var target = $('#' + button.data('target'));
    if (frame) frame.open();
    else {
      frame = wp.media({ title: 'Select Logo', multiple: false });
      frame.on('select', function(){
        var attachment = frame.state().get('selection').first().toJSON();
        target.val(attachment.id);
        button.after('<img src="'+attachment.sizes.thumbnail.url+'" class="lcsi-cert-logo" />');
      });
      frame.open();
    }
  });
});


jQuery(function($){
    $('#address_zip').on('blur', function(){
        var zip = $(this).val();
        console.log('ZIP blur, calling AJAX for', zip);
        if(zip.length === 5 && /^[0-9]+$/.test(zip)){
            $.get(lcsi_ajax.ajax_url, {action:'lcsi_get_timezone', zip:zip}, function(response){
                console.log('AJAX response:', response);
                if(response.success){
                    $('#timezone').val(response.data);
                }
            });
        }
    });
        }
    });
});

// Auto-select timezone based on ZIP and Country
jQuery(function($){
    function fetchAndSelectTimezone() {
        var zip = $('#address_zip').val();
        var country = $('#address_country').val() || 'US';
        if(zip.length === 5 && /^[0-9]+$/.test(zip)) {
            // Call AJAX timezone lookup
            $.get(lcsi_ajax.ajax_url, { action: 'lcsi_get_timezone', zip: zip, country: country }, function(response){
                if(response.success) {
                    var tz = response.data;
                    $('#timezone option').each(function(){
                        if($(this).val() === tz) {
                            $(this).prop('selected', true);
                        }
                    });
                } else {
                    console.warn('Timezone lookup failed:', response.data);
                }
            });
        }
    }
    $('#address_zip').on('blur', fetchAndSelectTimezone);
    $('#address_country').on('change', fetchAndSelectTimezone);
});

// Certificates form dynamic behavior
jQuery(function($){
  var schemas = window.certSchemas || {};
  // populate organizations dropdown
  $.each(schemas, function(org, list){
    $('#cert_org').append($('<option>').val(org).text(org));
  });
  $('#cert_org').on('change', function(){
    var org = $(this).val();
    $('#cert_name').empty();
    $.each(schemas[org], function(full, abbr){
      $('#cert_name').append($('<option>').val(full+'|'+abbr).text(full));
    });
    $('#cert_name').trigger('change');
  }).trigger('change');

  $('#cert_name').on('change', function(){
    var parts = $(this).val().split('|');
    $('#cert_abbr').val(parts[1]||'');
  });

  var certs = [];
  $('#add_cert_btn').on('click', function(){
    var parts = $('#cert_name').val().split('|');
    certs.push({
      org: $('#cert_org').val(),
      full: parts[0],
      abbr: parts[1],
      date: $('#cert_date').val()
    });
    $('#certs_json').val(JSON.stringify(certs));
  });
});
