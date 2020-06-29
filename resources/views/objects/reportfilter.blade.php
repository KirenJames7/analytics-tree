<div class="row clearfix utilization-filter-container">
    <fieldset>
        <legend>Business Unit Filter <i class="material-icons" style="font-size: .9em">filter_list</i></legend>
        <div class="scope-filter col-xs-12" style="margin-bottom: 10px">
            Business Unit : &nbsp;
        </div>
    </fieldset>
    <fieldset>
        <legend>Period Filters <i class="material-icons" style="font-size: .9em">filter_list</i></legend>
        <div class="utilization-filter col-xs-6" style="margin-bottom: 10px">
            Year : &nbsp;
            <select class="multidropdown" id="selyear" multiple="multiple" data-role="multiselect">
                <option></option>
            </select>
        </div>
        <div class="utilization-filter col-xs-6" style="margin-bottom: 10px">
            Month : &nbsp;
            <select class="multidropdown" id="selmonth" multiple="multiple" data-role="multiselect">
                <option></option>
            </select>
        </div>
    </fieldset>
    <br />
    <fieldset>
        <legend>Project Filters <i class="material-icons" style="font-size: .9em">filter_list</i></legend>
        <div class="utilization-filter col-xs-3" style="margin-bottom: 10px">
            Project HOD : &nbsp;
            <select class="multidropdown" id="selhodname" multiple="multiple" data-role="multiselect">
                <option></option>
            </select>
        </div>
        <div class="utilization-filter col-xs-3" style="margin-bottom: 10px">
            Project Type : &nbsp;
            <select class="multidropdown" id="selptype" multiple="multiple" data-role="multiselect">
                <option></option>
            </select>
        </div>
        <div class="utilization-filter col-xs-3" style="margin-bottom: 10px">
            Project Code : &nbsp;
            <select class="multidropdown" id="selpcode" multiple="multiple" data-role="multiselect">
                <option></option>
            </select>
        </div>
        <div class="utilization-filter col-xs-3">
            Resource Name : &nbsp;
            <select class="multidropdown" id="selrname" multiple="multiple" data-role="multiselect">
                <option></option>
            </select>
        </div>
    </fieldset>
    <fieldset>
        <legend>Value Filters <i class="material-icons" style="font-size: .9em">filter_list</i></legend>
        <div class="value-filter col-xs-6">
            Actual Effort %
            <input type="text" id="pae" value="" />
        </div>
        <div class="value-filter col-xs-6">
            Billed Effort %
            <input type="text" id="pbe" value="" />
        </div>
    </fieldset>
</div>