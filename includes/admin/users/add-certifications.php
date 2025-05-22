<?php
/**
 * Adds the certification form fields beneath the Certificates & Accreditations list.
 */
// Schema for organizations and certifications
$cert_schema = [
  'SAMS' => [
    'organizationName' => 'Society of Accredited Marine Surveyors',
    'certifications' => [
      ['name' => 'Accredited Marine Surveyor (AMS®)', 'abbreviation' => 'AMS'],
      ['name' => 'Surveyor Associate (SA)', 'abbreviation' => 'SA'],
      ['name' => 'Affiliate Member (AFF)', 'abbreviation' => 'AFF'],
    ],
  ],
  'NAMSGlobal' => [
    'organizationName' => 'National Association of Marine Surveyors',
    'certifications' => [
      ['name' => 'Certified Marine Surveyor (NAMS-CMS)', 'abbreviation' => 'NAMS-CMS'],
      ['name' => 'Associate Marine Surveyor (NAMS-AMS)', 'abbreviation' => 'NAMS-AMS'],
      ['name' => 'Apprentice Marine Surveyor (NAMS-Apprentice)', 'abbreviation' => 'NAMS-Apprentice'],
    ],
  ],
  'ACMS' => [
    'organizationName' => 'American Council of Marine Surveyors',
    'certifications' => [
      ['name' => 'Certified Marine Surveyor (ACMS)', 'abbreviation' => 'ACMS'],
      ['name' => 'Certified Container Inspector (CCI)', 'abbreviation' => 'CCI'],
      ['name' => 'Certified Food Cargo Inspector (CFCI)', 'abbreviation' => 'CFCI'],
      ['name' => 'Certified Marine Engine Surveyor (CMES)', 'abbreviation' => 'CMES'],
    ],
  ],
  'IIMS' => [
    'organizationName' => 'International Institute of Marine Surveyors',
    'certifications' => [
      ['name' => 'Diploma in Yacht & Small Craft Surveying (DipY&SCS)', 'abbreviation' => 'DipY&SCS'],
      ['name' => 'Diploma in Commercial Ship Surveying (DipCSS)', 'abbreviation' => 'DipCSS'],
      ['name' => 'Diploma in Marine Corrosion (DipMC)', 'abbreviation' => 'DipMC'],
      ['name' => 'Diploma in Marine Warranty Surveying (DipMWS)', 'abbreviation' => 'DipMWS'],
      ['name' => 'Diploma in Marine Accident Investigation (DipMAI)', 'abbreviation' => 'DipMAI'],
    ],
  ],
  'USSA' => [
    'organizationName' => 'United States Society of Admirals',
    'certifications' => [
      ['name' => 'Certified Marine Surveyor (USSA-CMS)', 'abbreviation' => 'USSA-CMS'],
      ['name' => 'Yacht & Small Craft Inspector (YSCI)', 'abbreviation' => 'YSCI'],
    ],
  ],
  'ABYC' => [
    'organizationName' => 'American Boat & Yacht Council',
    'certifications' => [
      ['name' => 'Marine Electrical Certification (ABYC-E)', 'abbreviation' => 'ABYC-E'],
      ['name' => 'Marine Systems Certification (ABYC-S)', 'abbreviation' => 'ABYC-S'],
      ['name' => 'Marine Corrosion Certification (ABYC-C)', 'abbreviation' => 'ABYC-C'],
      ['name' => 'Marine Diesel Engines Certification (ABYC-D)', 'abbreviation' => 'ABYC-D'],
      ['name' => 'A/C & Refrigeration Certification (ABYC-ACR)', 'abbreviation' => 'ABYC-ACR'],
      ['name' => 'Standards Certified Technician (ABYC-ST)', 'abbreviation' => 'ABYC-ST'],
      ['name' => 'Composite Boat Builder Certification (ABYC-CBB)', 'abbreviation' => 'ABYC-CBB'],
      ['name' => 'Certified Master Technician (ABYC-MT)', 'abbreviation' => 'ABYC-MT'],
    ],
  ],
  'MTA' => [
    'organizationName' => 'Maritime Training Academy',
    'certifications' => [
      ['name' => 'Diploma in Superyacht Surveying (DipSS)', 'abbreviation' => 'DipSS'],
      ['name' => 'Diploma in Marine Salvage (DipMS)', 'abbreviation' => 'DipMS'],
      ['name' => 'Diploma in Yacht & Small Craft Surveying (DipY&SCS)', 'abbreviation' => 'DipY&SCS'],
      ['name' => 'Diploma in Marine Accident Investigation (DipMAI)', 'abbreviation' => 'DipMAI'],
    ],
  ],
  'NRVIA' => [
    'organizationName' => 'National RV Inspectors Association',
    'certifications' => [
      ['name' => 'Certified RV Inspector (NRVIA)', 'abbreviation' => 'NRVIA'],
      ['name' => 'Master Certified RV Inspector (NRVIA-Master)', 'abbreviation' => 'NRVIA-Master'],
    ],
  ],
  'NRVTA' => [
    'organizationName' => 'National RV Training Academy',
    'certifications' => [
      ['name' => 'Advanced RV Systems Inspector (ARSI)', 'abbreviation' => 'ARSI'],
      ['name' => 'Pre-Delivery Inspection Specialist (PDI-S)', 'abbreviation' => 'PDI-S'],
      ['name' => 'RV Fundamentals (INSP-200)', 'abbreviation' => 'INSP-200'],
      ['name' => 'Principles of RV Inspection (INSP-201)', 'abbreviation' => 'INSP-201'],
      ['name' => 'Advanced RV Inspector Training (INSP-301)', 'abbreviation' => 'INSP-301'],
    ],
  ],
  'RVTAA' => [
    'organizationName' => 'Recreational Vehicle Technician Association of America',
    'certifications' => [
      ['name' => 'Registered RV Service Technician (RVTAA-R)', 'abbreviation' => 'RVTAA-R'],
      ['name' => 'Certified RV Service Technician (RVTAA-C)', 'abbreviation' => 'RVTAA-C'],
      ['name' => 'Advanced RV Service Technician (RVTAA-A)', 'abbreviation' => 'RVTAA-A'],
      ['name' => 'Master Certified RV Service Technician (RVTAA-M)', 'abbreviation' => 'RVTAA-M'],
      ['name' => 'Advanced Generator Technician (AGT)', 'abbreviation' => 'AGT'],
      ['name' => 'Solar Power Systems Technician (SPST)', 'abbreviation' => 'SPST'],
    ],
  ],
  'RVTI' => [
    'organizationName' => 'RV Technical Institute',
    'certifications' => [
      ['name' => 'Level 1 RV Technician (RVTI-1)', 'abbreviation' => 'RVTI-1'],
      ['name' => 'Level 2 RV Technician (RVTI-2)', 'abbreviation' => 'RVTI-2'],
      ['name' => 'Level 3 Specialist Technician (RVTI-3)', 'abbreviation' => 'RVTI-3'],
      ['name' => 'Level 4 Master Technician (RVTI-4)', 'abbreviation' => 'RVTI-4'],
    ],
  ],
  'RVIA_RVDA' => [
    'organizationName' => 'RV Industry Association / RV Dealers Association',
    'certifications' => [
      ['name' => 'RV Service Technician Certification (RVIA-STC)', 'abbreviation' => 'RVIA-STC'],
      ['name' => 'RV Standards and Compliance Certification (RVIA-RCMC)', 'abbreviation' => 'RVIA-RCMC'],
      ['name' => 'RVDA Master Certified RV Technician (RVDA-Master)', 'abbreviation' => 'RVDA-Master'],
      ['name' => 'General RV Service Technician Certification (Certified RV Technician)', 'abbreviation' => 'Certified RV Technician'],
    ],
  ],
];
?>
<?php $current_user = wp_get_current_user(); ?>
<script type="text/javascript">
  var lcsiCurrentUserName = '<?php echo esc_js( $current_user->display_name ); ?>';
</script>

<div class="row lcsi-cert-form-row">
  <div class="col-3">
    <label for="lcsi_certform_org">Organization</label>
    <select id="lcsi_certform_org" name="lcsi_certform_org">
      <option value="">Select Organization</option>
      <?php foreach ( $cert_schema as $org_key => $org_data ) : ?>
        <option value="<?php echo esc_attr( $org_key ); ?>">
          <?php echo esc_html( $org_key . ' — ' . $org_data['organizationName'] ); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-3">
    <label for="lcsi_certform_cert">Certification</label>
    <select id="lcsi_certform_cert" name="lcsi_certform_cert" disabled>
      <option value="">Select Certification</option>
    </select>
  </div>
  <div class="col-2">
    <label for="lcsi_certform_abbr">Abbreviation</label>
    <input type="text" id="lcsi_certform_abbr" name="lcsi_certform_abbr" readonly>
  </div>
  <div class="col-2" id="lcsi_certform_date_wrap">
    <label for="lcsi_certform_date">Date Issued</label>
    <input type="date" id="lcsi_certform_date" name="lcsi_certform_date">
  </div>
  <div class="col-2">
    <button type="button" id="lcsi_certform_add" class="button">Add Certification</button>
  </div>
</div>
