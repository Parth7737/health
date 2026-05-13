@php @$financial_information=$hospital->financialInformation; @endphp
@php @$tax_details=$hospital->taxDetails; @endphp
<div class="accordion accordion-popout mt-4" id="accordionSUBPopout">
	<div class="accordion-item financial_informationsteptab ">
		<h2 class="accordion-header" id="headingPopoutOne">
			<button type="button" class="accordion-button financial_informationstep @if(@$checkstepComplete['financial_informationstep']) theme-color @else pending-color @endif collapsed"
				data-bs-toggle="collapse"
				data-bs-target="#accordionSUBPopoutOne"
				aria-expanded="true" aria-controls="accordionSUBPopoutOne">
			Bank Details
			</button>
		</h2>
		<div id="accordionSUBPopoutOne"
			class="accordion-collapse financial_informationstepcollapse collapse"
			aria-labelledby="headingPopoutOne"
			data-bs-parent="#accordionSUBPopout">
			<div class="accordion-body">
				<div class="inside-left-info-box {{ @$financial_information? 'success' : 'pending' }} mt-4 bankpanel">
					<h4 class="colored-verticle-title">Bank Details
						<span class="status-dot">
							<svg xmlns="http://www.w3.org/2000/svg"
								height="24px" viewBox="0 -960 960 960"
								width="24px" fill="undefined">
								<path d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
							</svg>
						</span>
					</h4>
					<form onSubmit="return false" id="financialForm">
						<div class="row mt-3 g-5">
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="ifsc_code" name="ifsc_code" oninput="ifsccode(this);" class="form-control" placeholder="IFSC Code" value="{{ @$financial_information->ifsc_code }}" />
									<label for="ifsc_code">IFSC Code <span class="text-danger">*</span></label>
								</div>
							</div>
							
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="account_no" oninput="accountnumber(this);" name="account_no" class="form-control" placeholder="Account Number" value="{{ @$financial_information->account_no }}" oncopy="return false" onpaste="return false" oncut="return false"/>
									<label for="account_no">Account Number <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="account_no_confirmation" name="account_no_confirmation" class="form-control"  oninput="accountnumber(this);"  placeholder="Confirm Account Number" value="{{ @$financial_information->account_no }}" oncopy="return false" onpaste="return false" oncut="return false" />
									<label for="account_no_confirmation">Confirm Account Number <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="account_holder" name="account_holder" oninput="sanitize(this, 't');" class="form-control" placeholder="Account Holder's Name" value="{{ @$financial_information->account_holder }}" />
									<label for="account_holder">Account Holder's Name <span class="text-danger">*</span></label>
								</div>
							</div>							
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="bank_name" readonly name="bank_name" oninput="sanitize(this, 't');" class="form-control" placeholder="Bank Name" value="{{ @$financial_information->bank_name }}" />
									<label for="bank_name">Bank Name <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="bank_branch_name" name="bank_branch_name" readonly oninput="sanitize(this, 't');" class="form-control" placeholder="Bank Branch Name" value="{{ @$financial_information->bank_branch_name }}" />
									<label for="bank_branch_name">Bank Branch Name <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="bank_address" name="bank_address" readonly oninput="sanitize(this, 'm');" class="form-control" placeholder="Bank Address" value="{{ @$financial_information->bank_address }}" />
									<label for="bank_address">Bank Address <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="micr" oninput="micrnumber(this);" readonly name="micr" class="form-control" placeholder="400002018" value="{{ @$financial_information->micr }}" />
									<label for="micr">MICR <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<select class="form-select select2"
										id="account_type"
										name="account_type">
										<option value=""></option>
										<option value="Current" {{ @$financial_information->account_type == 'Current'?'selected':'' }}>Current</option>
										<option value="Saving" {{ @$financial_information->account_type == 'Saving'?'selected':'' }}>Saving</option>
									</select>
									<label for="account_type">Account Type <span class="text-danger">*</label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="authorised_signatory_name" name="authorised_signatory_name" oninput="sanitize(this, 't');" class="form-control" placeholder="Authorised Signatory Name" value="{{ @$financial_information->authorised_signatory_name }}" />
									<label for="certificate_no">Authorised Signatory Name <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="email" id="bank_email" name="bank_email" oninput="sanitize(this, 'email');" class="form-control" placeholder="Bank Email" value="{{ @$financial_information->bank_email }}" />
									<label for="bank_email">Bank Email <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<select class="form-select select2" id="neft_enabled" name="neft_enabled" readonly>
										<option value=""></option>
										<option value="Yes" {{ @$financial_information->neft_enabled == 'Yes'?'selected':'' }}>Yes</option>
										<option value="No" {{ @$financial_information->neft_enabled == 'No'?'selected':'' }}>No</option>
									</select>
									<label for="neft_enabled">Bank NEFT/RTGS Enabled <span class="text-danger">*</label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" oninput="bsrnumber(this);" id="bsr_code" name="bsr_code" class="form-control" placeholder="1234567" value="{{ @$financial_information->bsr_code }}" />
									<label for="bsr_code">BSR Code <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<label for="formFile" class="form-label">Upload Cancelled Cheque <span class="text-danger">*</span></label>
								<div class="file-upload-section">
									<div class="file-upload-wrapper">
										<svg xmlns="http://www.w3.org/2000/svg"
											height="24px"
											viewBox="0 -960 960 960"
											width="24px" fill="#6200ea">
											<path
												d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
										</svg>
										<p><strong>Browse</strong></p>
									</div>
									<input type="file"
										class="file-input d-none" name="cancelled_cheque" accept=".pdf"/>
									<div
										class="uploaded-file file-upload-display d-none">
										<span
											class="file-name">Sample.pdf</span>
										<i class="fas fa-trash "></i>
										<button
											class="remove-file-btn bg-transparent border-0 p-0">
											<svg xmlns="http://www.w3.org/2000/svg"
												height="24px"
												viewBox="0 -960 960 960"
												width="24px"
												fill="undefined">
												<path
													d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
											</svg>
										</button>
									</div>
									<div class="preivew-certificate">
										@if(@$financial_information->cancelled_cheque)
											<label class="mt-2"><strong>Preview</strong>&nbsp; <a href="{{ asset('public/storage/'.@$financial_information->cancelled_cheque) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
										@endif
									</div>
								</div>
								<small class="small text-muted">Supported file types: PDF (Max: 10MB)</small>
							</div>
						</div>
						@if($hospital->status != 'Empannelled' && @$hospital->status != 'Empanelment Not Recommended by DEC')
						<div class="col-md-12">
							<div
								class="d-flex justify-content-end">
								<button
									class="btn btn-primary savefinancialinforamation">SAVE</button>
							</div>
						</div>
						@endif
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="accordion-item taxdetailssteptab">
		<h2 class="accordion-header" id="headingPopoutTwo">
			<button type="button"
				class="accordion-button taxdetailsstep @if(@$checkstepComplete['taxdetailsstep']) theme-color @else pending-color @endif collapsed"
				data-bs-toggle="collapse"
				data-bs-target="#accordionSUBPopoutTwo"
				aria-expanded="false"
				aria-controls="accordionSUBPopoutTwo">
			Tax Details
			</button>
		</h2>
		<div id="accordionSUBPopoutTwo" class="accordion-collapse taxdetailsstepcollapse collapse"
			aria-labelledby="headingPopoutTwo"
			data-bs-parent="#accordionSUBPopout">
			<div class="accordion-body">
				<div class="inside-left-info-box {{ @$tax_details?'success':'pending' }} mt-4 taxpanel">
					<h4 class="colored-verticle-title">Tax Details
						<span class="status-dot">
							<svg xmlns="http://www.w3.org/2000/svg"
								height="24px" viewBox="0 -960 960 960"
								width="24px" fill="undefined">
								<path d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
							</svg>
						</span>
					</h4>
					<form onSubmit="return false" id="taxdetailsForm">
						<div class="row mt-3 g-5">
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="pan_no" oninput="pancard(this);" name="pan_no" class="form-control" placeholder="AAAAA1111A" value="{{ @$tax_details->pan_no }}" />
									<label for="pan_no">PAN Number <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="pan_name" name="pan_name" class="form-control vidt" oninput="sanitize(this, 't');" placeholder="Name on PAN" value="{{ @$tax_details->pan_name }}" />
									<label for="pan_name">Name on PAN <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<label for="formFile" class="form-label">Upload Pan Card <span class="text-danger">*</span></label>
								<div class="file-upload-section">
									<div class="file-upload-wrapper">
										<svg xmlns="http://www.w3.org/2000/svg"
											height="24px"
											viewBox="0 -960 960 960"
											width="24px" fill="#6200ea">
											<path
												d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
										</svg>
										<p><strong>Browse</strong></p>
									</div>
									<input type="file" class="file-input d-none" name="pan_certificate" accept=".pdf"/>
									<div
										class="uploaded-file file-upload-display d-none">
										<span
											class="file-name">Sample.pdf</span>
										<i class="fas fa-trash "></i>
										<button
											class="remove-file-btn bg-transparent border-0 p-0">
											<svg xmlns="http://www.w3.org/2000/svg"
												height="24px"
												viewBox="0 -960 960 960"
												width="24px"
												fill="undefined">
												<path
													d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
											</svg>
										</button>
									</div>									
								</div>
								<small class="small text-muted">Supported file types: PDF (Max: 10MB)</small>
								<div class="preivew-certificate">
									@if(@$tax_details->pan_certificate)
										<label class="mt-2"><strong>Preview</strong>&nbsp; <a href="{{ asset('public/storage/'.@$tax_details->pan_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
									@endif
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="tan_no" oninput="tannumber(this);" name="tan_no" class="form-control" placeholder="DELH12345L" value="{{ @$tax_details->tan_no }}" />
									<label for="tan_no">TAN Number <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="tan_holder_name" oninput="sanitize(this, 't');" name="tan_holder_name" class="form-control" placeholder="TAN Holder Name" value="{{ @$tax_details->tan_holder_name }}" />
									<label for="tan_holder_name">TAN Holder Name <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="gst_no" name="gst_no" class="form-control" placeholder="22ABCDE1234F1Z1" oninput="gstNumber(this);" value="{{ @$tax_details->gst_no }}" />
									<label for="gst_no">GST Number </label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="gst_name" name="gst_name" readonly oninput="sanitize(this, 't');" class="form-control" placeholder="Name on GST Certificate" value="{{ @$tax_details->gst_name }}" />
									<label for="gst_name">Name on GST Certificate </label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<label for="formFile" class="form-label">Upload Gst Certificate <span class="text-danger">*</span></label>
								<div class="file-upload-section">
									<div class="file-upload-wrapper">
										<svg xmlns="http://www.w3.org/2000/svg"
											height="24px"
											viewBox="0 -960 960 960"
											width="24px" fill="#6200ea">
											<path
												d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
										</svg>
										<p><strong>Browse</strong></p>
									</div>
									<input type="file" class="file-input d-none" name="gst_certificate" accept=".pdf"/>
									<div
										class="uploaded-file file-upload-display d-none">
										<span
											class="file-name">Sample.pdf</span>
										<i class="fas fa-trash "></i>
										<button
											class="remove-file-btn bg-transparent border-0 p-0">
											<svg xmlns="http://www.w3.org/2000/svg"
												height="24px"
												viewBox="0 -960 960 960"
												width="24px"
												fill="undefined">
												<path
													d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
											</svg>
										</button>
									</div>
								</div>
								<small class="small text-muted">Supported file types: PDF (Max: 10MB)</small>
								<div class="preivew-certificate">
									@if(@$tax_details->gst_certificate)
										<label class="mt-2"><strong>Preview</strong>&nbsp; <a href="{{ asset('public/storage/'.@$tax_details->gst_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
									@endif
								</div>
							</div>
						</div>
						<div class="row g-5 mt-2">
							<div class="col-md-12 col-lg-12">
								<label for="cyanosis" class="mb-2">Does your facility has the TDS Exemption? <span class="text-danger">*</span></label>
								<div class="d-flex">
									<div class="form-check">
										<input class="form-check-input"
											type="radio" name="tds_exemption"
											id="tds_exemption_yes" value="Yes" {{ @$tax_details->tds_exemption && $tax_details->tds_exemption=='Yes'?'checked':'' }}>
										<label class="form-check-label"
											for="tds_exemption_yes">
											Yes
										</label>
									</div>
									<div class="form-check ms-4">
										<input class="form-check-input"
											type="radio" name="tds_exemption"
											id="tds_exemption_no" value="No" {{ @$tax_details->tds_exemption && $tax_details->tds_exemption=='No'?'checked':'' }}>
										<label class="form-check-label"
											for="tds_exemption_no">
											No
										</label>
									</div>
								</div>
							</div>
						</div>
						<div class="row mt-3 g-5 exemption-field {{ @$tax_details->tds_exemption && $tax_details->tds_exemption=='Yes'?'':'d-none' }}">
							<div class="col-md-6 col-lg-3">
								<div
									class="form-floating form-floating-outline">
									@php $tds_exemptions = App\CentralLogics\Helpers::getCommanData('TdsExemption'); @endphp
									<select class="form-select select2"
										id="tds_exemption_id"
										name="tds_exemption_id">
										<option value=""></option>
										@foreach($tds_exemptions as $tds_exemption)
											<option value="{{ $tds_exemption->id }}" {{ @$tax_details->tds_exemption_id == $tds_exemption->id?'selected':'' }}>{{ $tds_exemption->name }}</option>
										@endforeach
									</select>
									<label for="tds_exemption_id">Name of Accreditation Board <span class="text-danger">*</label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="number" id="tds_rate" name="tds_rate" class="form-control" placeholder="Original TDS Rate" value="{{ @$tax_details->tds_rate }}" />
									<label for="tds_rate">Original TDS Rate <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="number" id="after_tds_rate" name="after_tds_rate" class="form-control" placeholder="After Exemption Applicable TDS Rate" value="{{ @$tax_details->after_tds_rate }}" />
									<label for="after_tds_rate">After Exemption Applicable TDS Rate <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="tds_exemption_certificate_no" name="tds_exemption_certificate_no" oninput="sanitize(this, 'm');" class="form-control" placeholder="TDS Exemption Certificate Number" value="{{ @$tax_details->tds_exemption_certificate_no }}" />
									<label for="tds_exemption_certificate_no">TDS Exemption Certificate Number <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="bs-rangepicker-singlee" name="tds_exemption_valid_from" class="form-control" placeholder="Valid From" value="{{ @$tax_details->tds_exemption_valid_from }}" />
									<label for="bs-rangepicker-singlee">TDS Exemption Valid From <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="text" id="bs-rangepicker-singlee-2" name="tds_exemption_valid_till" class="form-control" placeholder="Valid Till" value="{{ @$tax_details->tds_exemption_valid_till }}" />
									<label for="bs-rangepicker-singlee-2">Valid Till <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="form-floating form-floating-outline">
									<input type="number" id="tds_exemption_amount" name="tds_exemption_amount" class="form-control" placeholder="TDS Exemption Amount" value="{{ @$tax_details->tds_exemption_amount }}" />
									<label for="tds_exemption_amount">TDS Exemption Amount <span class="text-danger">*</span></label>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<label for="formFile" class="form-label">Upload TDS Exemption <span class="text-danger">*</span></label>
								<div class="file-upload-section">
									<div class="file-upload-wrapper">
										<svg xmlns="http://www.w3.org/2000/svg"
											height="24px"
											viewBox="0 -960 960 960"
											width="24px" fill="#6200ea">
											<path
												d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
										</svg>
										<p><strong>Browse</strong></p>
									</div>
									<input type="file" class="file-input d-none" name="tds_exemption_certificate" accept=".pdf"/>
									<div
										class="uploaded-file file-upload-display d-none">
										<span
											class="file-name">Sample.pdf</span>
										<i class="fas fa-trash "></i>
										<button
											class="remove-file-btn bg-transparent border-0 p-0">
											<svg xmlns="http://www.w3.org/2000/svg"
												height="24px"
												viewBox="0 -960 960 960"
												width="24px"
												fill="undefined">
												<path
													d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
											</svg>
										</button>
									</div>
								</div>
								<small class="small text-muted">Supported file types: PDF (Max: 10MB)</small>
								<div class="preivew-certificate">
									@if(@$tax_details->tds_exemption_certificate)
										<label class="mt-2"><strong>Preview</strong>&nbsp; <a href="{{ asset('public/storage/'.@$tax_details->tds_exemption_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
									@endif
								</div>
							</div>
						</div>
						
						@if($hospital->status != 'Empannelled' && @$hospital->status != 'Empanelment Not Recommended by DEC')
							<div class="col-md-12">
								<div
									class="d-flex justify-content-end">
									<button
										class="btn btn-primary savetaxdetails">SAVE</button>
								</div>
							</div>
						@endif
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
    bsRangePickerSingle = $('#bs-rangepicker-singlee'),
    bsRangePickerSingle2 = $('#bs-rangepicker-singlee-2');
	// if (bsRangePickerSingle.length) {
	// 	bsRangePickerSingle.daterangepicker({
	// 		singleDatePicker: true,
	// 		autoApply: true,
	// 		autoUpdateInput: true,
	// 		maxDate: moment().subtract(1, 'days'), // Restrict to past dates
	// 		locale: {
	// 			format: 'YYYY-MM-DD'
	// 		},
	// 	});
	// }
	// if (bsRangePickerSingle2.length) {
	// 	bsRangePickerSingle2.daterangepicker({
	// 		singleDatePicker: true,
	// 		autoApply: true,
	// 		autoUpdateInput: true,
	// 		minDate: moment().add(1, 'days'), // Restrict to future dates
	// 		locale: {
	// 			format: 'YYYY-MM-DD'
	// 		},
	// 	});
	// }

	if (bsRangePickerSingle.length) {
        bsRangePickerSingle.daterangepicker({
            singleDatePicker: true,
            autoApply: true,
            autoUpdateInput: false,
            minDate: moment().subtract(1, 'days'), // Allow past dates only
            locale: {
                format: 'YYYY-MM-DD'
            }
        }).on('apply.daterangepicker', function (ev, picker) {
			bsRangePickerSingle.val(picker.startDate.format('YYYY-MM-DD'));
			let validFromDate = picker.startDate.clone();		
            let fyEndYear = validFromDate.month() >= 3 ? validFromDate.year() + 1 : validFromDate.year();
            let financialYearEnd = moment(fyEndYear + '-03-31', 'YYYY-MM-DD'); 

            console.log('Valid From:', validFromDate.format('YYYY-MM-DD'));
            console.log('Financial Year End:', financialYearEnd.format('YYYY-MM-DD'));

            // Reinitialize "Valid Till" date picker with new min/max range
            if (bsRangePickerSingle2.length) {
                bsRangePickerSingle2.val(""); // Clear previous value
                bsRangePickerSingle2.daterangepicker({
                    singleDatePicker: true,
                    autoApply: true,
                    autoUpdateInput: true,
                    minDate: moment(validFromDate).add(1, 'days'),
					maxDate: financialYearEnd,
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                });
            }
        });

		@if(!@$tax_details->tds_exemption_valid_from)
			bsRangePickerSingle.val('');
		@endif
		@if(!@$tax_details->tds_exemption_valid_till)
			bsRangePickerSingle2.val('');
		@endif
    }
	@if(@$tax_details->tds_exemption_valid_till)
		if (bsRangePickerSingle2.length) {
			let today = moment(bsRangePickerSingle.val(), 'YYYY-MM-DD');
			let fyEndYear = today.month() >= 3 ? today.year() + 1 : today.year();
			let financialYearEnd = moment(fyEndYear + '-03-31', 'YYYY-MM-DD'); 

			bsRangePickerSingle2.daterangepicker({
				singleDatePicker: true,
				autoApply: true,
				autoUpdateInput: true,
				minDate: moment().add(1, 'days'), // Minimum: Next day
				maxDate: financialYearEnd, // Maximum: March 31st of the FY
				locale: {
					format: 'YYYY-MM-DD'
				}
			});
		}
	@endif

	$("input[name='tds_exemption']").on("change",function(){
		if($("input[name='tds_exemption']:checked").val() == 'Yes'){
			$(".exemption-field").removeClass('d-none');
		}else{
			$(".exemption-field").addClass('d-none');
		}
	})
	$('.savefinancialinforamation').click(function () {
        ldrshow();
		$('.error').remove();
        var step = 6;
        // Create a FormData object
        var formData = new FormData($('#financialForm')[0]);
        $.ajax({
            url: '{{route("hospital.empanelmentRegistration.financialForm", [$uuid, $hospital_id])}}', // Replace with your server endpoint
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false, // Prevent jQuery from automatically processing the data
            contentType: false, // Prevent jQuery from automatically setting content type
            success: function (response) {
                ldrhide();
                if(response.success) {
                    successMessage(response.message);
					$('.bankpanel').removeClass('pending').addClass('success');
					$('.financial_informationstep').removeClass('pending-color').addClass('theme-color');
					if(response.completedStep['financial_informationstep'] && response.completedStep['taxdetailsstep']) {
						CheckFinanceStepCompleteOrNot(8, true);
					} else {
						CheckFinanceStepCompleteOrNot(7, false);
					}
                } else {
                    errorMessage(response.message);
                }
            },
            error: function (xhr) {
               ldrhide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    });
	$('.savetaxdetails').click(function () {
        ldrshow();
		$('.error').remove();
        var step = 6;
        // Create a FormData object
        var formData = new FormData($('#taxdetailsForm')[0]);
        $.ajax({
            url: '{{route("hospital.empanelmentRegistration.taxdetailsForm", [$uuid, $hospital_id])}}', // Replace with your server endpoint
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false, // Prevent jQuery from automatically processing the data
            contentType: false, // Prevent jQuery from automatically setting content type
            success: function (response) {
                ldrhide();
                if(response.success) {
                    successMessage(response.message);
					$('.taxdetailsstep').removeClass('pending-color').addClass('theme-color');
					$('.taxpanel').removeClass('pending').addClass('success');
					if(response.completedStep['financial_informationstep'] && response.completedStep['taxdetailsstep']) {
						CheckFinanceStepCompleteOrNot(8, true);
					} else {
						CheckFinanceStepCompleteOrNot(7, false);
					}
                } else {
                    errorMessage(response.message);
                }           
            },
            error: function (xhr) {
               ldrhide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    });
	function CheckFinanceStepCompleteOrNot(step, isLoad = 0) {
		$('.nav-link').removeClass('active');
		$('.tab-pane').removeClass('show active');
		$(`.step${step}`).addClass('show active');
		$(`.navstep${step}`).addClass('active');
		setTimeout(() => {
			$(`.step${step}`).on('click', function(event) {
				if (event.target.closest('.nav-item .active')) {
					setSlider(event.target.closest('.nav-item'));
				}
			});
			$(`.step${(step-1)}Icon`).show();
			// Populate the content of the step
			// $(`.step${step}`).html(data.html || data);
			if(isLoad) {
				$(`.step${step}Icon`).hide();
				loadStep(step);
			}
		}, 1000);
	}

	$('#ifsc_code').on('change', function () {
		var ifsc_code = $("#ifsc_code").val();
		if (ifsc_code && ifsc_code.length === 11) {
			ldrshow();
			$('.error').remove();
			$.ajax({
				url: '{{route("getbankdetails")}}',
				headers: {
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				type: 'POST',
				data: {ifsc_code},
				success: function (response) {
					ldrhide();
					if(response.success) {
						$("#bank_name").val(response.data.bank);
						$("#bank_branch_name").val(response.data.branch);
						$("#bank_address").val(response.data.address);
						$("#micr").val(response.data.micr);
						$("#neft_enabled").val(response.data.neft_rtgs).trigger("change");
						successMessage(response.message);
					} else {
						errorMessage(response.message);
					}
				},
				error: function (xhr) {
					ldrhide();
					$('.error').remove();
					
					if (xhr.status === 422) { 
						let errors = xhr.responseJSON.errors;
						for (let field in errors) {
							$(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
						}
					} else {
						errorMessage('Something went wrong. Please try again later.');
					}
				}
			});
		}
	});

	$('#account_no_confirmation').on('change', function () {
		var ifsc_code = $("#ifsc_code").val();
		var account_no_confirmation = $("#account_no_confirmation").val();
		var account_no = $("#account_no").val();
		if(account_no != account_no_confirmation) {
			errorMessage('Account Number Does not matched!!');
			$("#account_no_confirmation").val("").focus();
			$("#account_holder").val("");
			$("#account_holder").removeAttr('readonly');
			return false;
		}
		if(!ifsc_code) {
			$("#ifsc_code").focus();
			errorMessage('Please Enter IFSC code');
			return false;
		}
		if (ifsc_code && ifsc_code.length === 11) {
			$("#account_holder").val("");
			ldrshow();
			$('.error').remove();
			$.ajax({
				url: '{{route("getaccountdetails")}}',
				headers: {
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				type: 'POST',
				data: {ifsc_code, account_no_confirmation},
				success: function (response) {
					ldrhide();
					if(response.success) {
						$("#account_holder").val(response.data.name_at_bank);
						$("#account_holder").attr('readonly', true);
						successMessage(response.message);
					} else {
						$("#account_holder").removeAttr('readonly');
						errorMessage(response.message);
					}
				},
				error: function (xhr) {
					ldrhide();
					$('.error').remove();
					$("#account_holder").removeAttr('readonly');
					if (xhr.status === 422) { 
						let errors = xhr.responseJSON.errors;
						for (let field in errors) {
							$(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
						}
					} else {
						errorMessage('Something went wrong. Please try again later.');
					}
				}
			});
		}
	});

	$('#gst_no').on('change', function() {
		var gst_no = $("#gst_no").val();
		if (gst_no) {
			ldrshow();
			$('.error').remove();
			$.ajax({
				url: '{{route("getGstDetails")}}',
				headers: {
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				type: 'POST',
				data: {gst_no},
				success: function (response) {
					ldrhide();
					if(response.success) {
						$("#gst_name").val(response.data.gstname);
						successMessage(response.message);
					} else {
						errorMessage(response.message);
					}
				},
				error: function (xhr) {
					ldrhide();
					$('.error').remove();
					
					if (xhr.status === 422) { 
						let errors = xhr.responseJSON.errors;
						for (let field in errors) {
							$(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
						}
					} else {
						errorMessage('Something went wrong. Please try again later.');
					}
				}
			});
		} else {
			$("#gst_name").val("");
		}
	});
</script>