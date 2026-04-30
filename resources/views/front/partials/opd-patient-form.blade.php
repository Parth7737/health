<form class="row g-3">
  <div class="col-md-2">
    <label class="form-label">Search By</label>
    <div class="form-check">
      <input class="form-check-input" type="radio" name="searchBy" id="searchByPhone" checked>
      <label class="form-check-label" for="searchByPhone">Phone</label>
    </div>
    <div class="form-check">
      <input class="form-check-input" type="radio" name="searchBy" id="searchByHealthId">
      <label class="form-check-label" for="searchByHealthId">Health ID</label>
    </div>
  </div>
  <div class="col-md-2">
    <label class="form-label">Country <span class="text-danger">*</span></label>
    <select class="form-select" required><option>Select</option></select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Phone <span class="text-danger">*</span></label>
    <input type="text" class="form-control" required>
  </div>
  <div class="col-md-2">
    <label class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control" required>
  </div>
  <div class="col-md-2">
    <label class="form-label">Guardian Name</label>
    <input type="text" class="form-control">
  </div>
  <div class="col-md-2">
    <label class="form-label">Appointment Date <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="opd-appointment-date" required>
  </div>
  <div class="col-md-2">
    <label class="form-label">Gender</label>
    <select class="form-select"><option>Male</option><option>Female</option><option>Other</option></select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Date Of Birth</label>
    <input type="text" class="form-control" id="opd-dob">
  </div>
  <div class="col-md-2">
    <label class="form-label">Age <span class="text-danger">*</span></label>
    <div class="input-group">
      <input type="number" class="form-control" placeholder="Year">
      <input type="number" class="form-control" placeholder="Month">
    </div>
  </div>
  <div class="col-md-2">
    <label class="form-label">Case</label>
    <input type="text" class="form-control">
  </div>
  <div class="col-md-2">
    <label class="form-label">Patient Photo</label>
    <input type="file" class="form-control">
  </div>
  <div class="col-md-2">
    <label class="form-label">Blood Group</label>
    <select class="form-select"><option>B+</option></select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Marital Status</label>
    <select class="form-select"><option>Single</option></select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Email</label>
    <input type="email" class="form-control">
  </div>
  <div class="col-md-2">
    <label class="form-label">Known Allergies & Clinical History</label>
    <input type="text" class="form-control">
  </div>
  <div class="col-md-2">
    <label class="form-label">Address</label>
    <input type="text" class="form-control">
  </div>
  <div class="col-md-2">
    <label class="form-label">Nationality</label>
    <select class="form-select"><option>Indian</option></select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Religion</label>
    <select class="form-select"><option>Select</option></select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Dietry</label>
    <select class="form-select"><option>Keto Diet</option></select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Aadhar card No</label>
    <input type="text" class="form-control">
  </div>
  <div class="col-md-2">
    <label class="form-label">Is Staff?</label>
    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="isStaff" id="isStaffYes"><label class="form-check-label" for="isStaffYes">Yes</label></div>
    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="isStaff" id="isStaffNo" checked><label class="form-check-label" for="isStaffNo">No</label></div>
  </div>
  <div class="col-md-2">
    <label class="form-label">Insured Patient?</label>
    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="insuredPatient" id="insuredYes"><label class="form-check-label" for="insuredYes">Yes</label></div>
    <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="insuredPatient" id="insuredNo" checked><label class="form-check-label" for="insuredNo">No</label></div>
  </div>
  <div class="col-md-2">
    <label class="form-label">Category</label>
    <select class="form-select"><option>General</option></select>
  </div>
  <div class="col-md-4">
    <label class="form-label">Brief Summary of Case</label>
    <textarea class="form-control"></textarea>
  </div>
  <div class="col-md-2">
    <label class="form-label">MLC Patient</label>
    <select class="form-select"><option>No</option></select>
  </div>
  <div class="col-md-2">
    <label class="form-label">TPA</label>
    <select class="form-select"><option>Select</option></select>
  </div>
  <div class="col-md-2">
    <label class="form-label">TPA Reference No</label>
    <input type="text" class="form-control">
  </div>
  <div class="col-md-2">
    <label class="form-label">Department <span class="text-danger">*</span></label>
    <select class="form-select" required><option>Select</option></select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Unit</label>
    <select class="form-select"><option>Select</option></select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Consultant Doctor</label>
    <select class="form-select"><option>Select</option></select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Slot</label>
    <select class="form-select"><option>Select Slot</option></select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Standard Charge (₹)</label>
    <input type="text" class="form-control" readonly>
  </div>
  <div class="col-md-2">
    <label class="form-label">Applied Charge (₹) <span class="text-danger">*</span></label>
    <input type="number" class="form-control" value="0" required>
  </div>
  <div class="col-md-2">
    <label class="form-label">Payment Mode</label>
    <select class="form-select"><option>Cash</option></select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Live Consultation</label>
    <select class="form-select"><option>No</option></select>
  </div>
  <div class="col-md-12">
    <label class="form-label">Symptoms Type</label>
    <select class="form-select"><option>Select</option></select>
  </div>
  <div class="col-md-4">
    <label class="form-label">Symptoms Title</label>
    <input type="text" class="form-control">
  </div>
  <div class="col-md-4">
    <label class="form-label">Symptoms Description</label>
    <input type="text" class="form-control">
  </div>
  <div class="col-12 d-flex justify-content-end gap-2 mt-4">
    <button type="button" class="btn btn-info">Save & Print Health Card</button>
    <button type="button" class="btn btn-primary">Save & Print</button>
    <button type="submit" class="btn btn-success">Save</button>
  </div>
</form> 