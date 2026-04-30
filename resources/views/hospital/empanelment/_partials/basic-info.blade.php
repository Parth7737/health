<div class="inside-left-info-box schemetab">
    <h4 class="colored-verticle-title">
    Basic Information
    </h4>
    <div class="row row-cols-4">
        <div class="col">
            <div class="infodata">
                <label>Name</label>
                <p><strong>{{ @$user->name }}</strong></p>
                <label>Gender</label>
                <p><strong>{{ @$user->gender }}</strong></p>
                <label>State</label>
                <p><strong>{{ @$user->state }}</strong></p>                    
            </div>
        </div>
        <div class="col">
            <div class="infodata">  
                <label>Email</label>
                <p><strong>{{ @$user->email }}</strong></p>    
                <label class="mb-3">Mobile Number</label>
                <p><strong>{{ @$user->mobile_no }}</strong></p>                            
                <label>Hospital Type</label>
                <p><strong>{{ @$user->hospital_type }}</strong></p>                  
            </div>
        </div>
    </div>
</div>
