
document.addEventListener('DOMContentLoaded', function() {
  const certData = {
    'SAMS': {'Accredited Marine Surveyor (AMS®)': 'AMS', 'Surveyor Associate (SA)': 'SA', 'Affiliate Member (AFF)': 'AFF'},
    'NAMSGlobal': {'Certified Marine Surveyor (NAMS-CMS)': 'NAMS-CMS', 'Associate Marine Surveyor (NAMS-AMS)': 'NAMS-AMS', 'Apprentice Marine Surveyor (NAMS-Apprentice)': 'NAMS-Apprentice'},
    'ACMS': {'Certified Marine Surveyor (ACMS)': 'ACMS', 'Certified Container Inspector (CCI)': 'CCI', 'Certified Food Cargo Inspector (CFCI)': 'CFCI', 'Certified Marine Engine Surveyor (CMES)': 'CMES'},
    'IIMS': {'Diploma in Yacht & Small Craft Surveying (DipY&SCS)': 'DipY&SCS', 'Diploma in Commercial Ship Surveying (DipCSS)': 'DipCSS', 'Diploma in Marine Corrosion (DipMC)': 'DipMC', 'Diploma in Marine Warranty Surveying (DipMWS)': 'DipMWS', 'Diploma in Marine Accident Investigation (DipMAI)': 'DipMAI'},
    'USSA': {'Certified Marine Surveyor (USSA-CMS)': 'USSA-CMS', 'Yacht & Small Craft Inspector (YSCI)': 'YSCI'},
    'ABYC': {'Marine Electrical Certification (ABYC-E)': 'ABYC-E', 'Marine Systems Certification (ABYC-S)': 'ABYC-S', 'Marine Corrosion Certification (ABYC-C)': 'ABYC-C', 'Marine Diesel Engines Certification (ABYC-D)': 'ABYC-D', 'A/C & Refrigeration Certification (ABYC-ACR)': 'ABYC-ACR', 'Standards Certified Technician (ABYC-ST)': 'ABYC-ST', 'Composite Boat Builder Certification (ABYC-CBB)': 'ABYC-CBB', 'Certified Master Technician (ABYC-MT)': 'ABYC-MT'},
    'MTA': {'Diploma in Superyacht Surveying (DipSS)': 'DipSS', 'Diploma in Marine Salvage (DipMS)': 'DipMS', 'Diploma in Yacht & Small Craft Surveying (DipY&SCS)': 'DipY&SCS', 'Diploma in Marine Accident Investigation (DipMAI)': 'DipMAI'},
    'NRVIA': {'Certified RV Inspector (NRVIA)': 'NRVIA', 'Master Certified RV Inspector (NRVIA-Master)': 'NRVIA-Master'},
    'NRVTA': {'Advanced RV Systems Inspector (ARSI)': 'ARSI', 'Pre-Delivery Inspection Specialist (PDI-S)': 'PDI-S', 'RV Fundamentals (INSP-200)': 'INSP-200', 'Principles of RV Inspection (INSP-201)': 'INSP-201', 'Advanced RV Inspector Training (INSP-301)': 'INSP-301'},
    'RVTAA': {'Registered RV Service Technician (RVTAA-R)': 'RVTAA-R', 'Certified RV Service Technician (RVTAA-C)': 'RVTAA-C', 'Advanced RV Service Technician (RVTAA-A)': 'RVTAA-A', 'Master Certified RV Service Technician (RVTAA-M)': 'RVTAA-M', 'Advanced Generator Technician (AGT)': 'AGT', 'Solar Power Systems Technician (SPST)': 'SPST'},
    'RVTI': {'Level 1 RV Technician (RVTI-1)': 'RVTI-1', 'Level 2 RV Technician (RVTI-2)': 'RVTI-2', 'Level 3 Specialist Technician (RVTI-3)': 'RVTI-3', 'Level 4 Master Technician (RVTI-4)': 'RVTI-4'},
    'RVIA_RVDA': {'RV Service Technician Certification (RVIA-STC)': 'RVIA-STC', 'RV Standards and Compliance Certification (RVIA-RCMC)': 'RVIA-RCMC', 'RVDA Master Certified RV Technician (RVDA-Master)': 'RVDA-Master', 'General RV Service Technician Certification (Certified RV Technician)': 'Certified RV Technician'}
  };

  const orgSelect = document.getElementById('lcsi_certform_org');
  const certSelect = document.getElementById('lcsi_certform_cert');
  const abbrInput = document.getElementById('lcsi_certform_abbr');
  const dateInput = document.getElementById('lcsi_certform_date');
  const dateWrap = document.getElementById('lcsi_certform_date_wrap');
  const addBtn = document.getElementById('lcsi_certform_add');
  const certsJsonInput = document.getElementById('certs_json');
  let certList = [];

  try {
    const existing = certsJsonInput.value.trim();
    certList = existing ? JSON.parse(existing) : [];
  } catch (e) {
    certList = [];
  }

  function resetCertFields() {
    certSelect.innerHTML = '<option value="">Select Certification</option>';
    certSelect.disabled = true;
    abbrInput.value = '';
    dateWrap.style.display = 'none';
    dateInput.value = '';
  }

  resetCertFields();

  orgSelect.addEventListener('change', function() {
    const orgKey = this.value;
    resetCertFields();
    if (certData[orgKey]) {
      Object.keys(certData[orgKey]).forEach(cert => {
        const opt = document.createElement('option');
        opt.value = cert;
        opt.textContent = cert;
        certSelect.appendChild(opt);
      });
      certSelect.disabled = false;
    }
  });

  certSelect.addEventListener('change', function() {
    const orgKey = orgSelect.value;
    const certName = this.value;
    if (orgKey && certName && certData[orgKey][certName]) {
      abbrInput.value = certData[orgKey][certName];
      dateWrap.style.display = 'block';
    } else {
      abbrInput.value = '';
      dateWrap.style.display = 'none';
    }
  });

  addBtn.addEventListener('click', function() {
    const orgKey = orgSelect.value;
    const certName = certSelect.value;
    const abbr = abbrInput.value;
    const dateIssued = dateInput.value;
    if (!orgKey || !certName || !abbr || !dateIssued) {
      alert('Please fill out all fields before adding.');
      return;
    }
    // Append to table
    const tbody = document.querySelector('.wrap table tbody') || document.querySelector('table.wp-list-table tbody');
    const row = document.createElement('tr');
    row.innerHTML = '<td>' + orgSelect.options[orgSelect.selectedIndex].text + '</td>' +
                    '<td>' + certName + '</td>' +
                    '<td>' + abbr + '</td>' +
                    '<td>' + dateIssued + '</td>' +
                    '<td><button class="button-link delete-cert">Remove</button></td>';
    tbody.appendChild(row);

    // Update list and hidden input
    certList.push({ orgKey, organizationName: orgSelect.options[orgSelect.selectedIndex].text, certification: certName, abbreviation: abbr, dateIssued });
    certsJsonInput.value = JSON.stringify(certList);

    // Show admin notice
    const notice = document.createElement('div');
    notice.className = 'notice notice-success is-dismissible';
    notice.innerHTML = '<p>Certification added.</p>';
    const wrap = document.querySelector('.wrap');
    wrap.insertBefore(notice, wrap.firstChild);

    // Reset fields
    resetCertFields();
  });

  // Handle removal
  document.querySelector('.wrap').addEventListener('click', function(e) {
    if (e.target && e.target.matches('.delete-cert')) {
      const row = e.target.closest('tr');
      const idx = Array.from(row.parentNode.children).indexOf(row) - 1;
      row.remove();
      certList.splice(idx, 1);
      certsJsonInput.value = JSON.stringify(certList);
    }
  });
});
