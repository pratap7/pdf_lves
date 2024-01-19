<!DOCTYPE html>
<html>
<head>
    <title>Import & Export Tenant Report</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <script src="{{asset('public/js/parsley.min.js')}}"></script>
    <link rel="stylesheet" href="{{asset('public/css/parsley.css')}}">
    <link rel="stylesheet" href="{{asset('public/font-awesome-4.7.0/css/font-awesome.min.css')}}">
</head>
<body>
  <?php $locations = array(
    1 => 'Las Vegas',
    2 => 'Henderson',
    3 => 'North Las Vegas'
  );
  ?>
   <nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="#">Import Export Mac</a>
      </div>
      <ul class="nav navbar-nav navbar-right">
        <li>
        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span class="glyphicon glyphicon-log-out"></span> &nbsp;{{ __('Logout') }}</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </li>
      </ul>
    </div>
  </nav>
<div class="container">
<div class="panel panel-default">
  <div class="panel-heading">Import Spreadsheet</div>
      <div class="panel-body">
      @if ($message = Session::get('success'))
        <div class="alert alert-success alert-block">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{{ $message }}</strong>
        </div>
      @endif
      @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-block">
	      <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
      </div>
      @endif

      <form class="form-horizontal" id="import-form" action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
          <label class="control-label col-sm-2" for="email">Batch Code:</label>
          <div class="col-sm-10">
            <input type="text" class="form-control" data-parsley-minlength="3" id="batch_code" placeholder="Batch Code"  data-parsley-remote-message="This Batch Code is already used." maxlength="20" data-parsley-remote="{{route('validateBatchCode')}}" name="batch_code" data-parsley-required="true" data-parsley-required-message="Please Enter Batch Code to proceed!" required />
            <small style="color:green;">**This Batch Code is to remember the batch and will be used to generate its pdf.</small>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-sm-2" for="pwd">Spreadsheet File:</label>
          <div class="col-sm-10">
            <input type="file" class="form-control" id="file" name="file" data-parsley-required="true" data-parsley-fileextension='xls,xlsx' data-parsley-required-message="Please choose a file to upload." accept=".xls,.xlsx" required />
            <small style="color:green;">**Only xls and xlsx file allowed.</small>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-sm-2" for="pwd">Location:</label>
          <div class="col-sm-10">
            <select class="form-control" id="location" name="location" required>
            @foreach($locations as $lkey => $lvalue)
              <option value="{{$lkey}}">{{$lvalue}}</option>
            @endforeach
            </select>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-success">Import Data</button>
            <a class="btn btn-info" href="{{asset('public/sample/SAMPLE_SPREADSHEET.xlsx')}}">Sample - Las Vegas</a>
            <a class="btn btn-info" href="{{asset('public/sample/SAMPLE_SPREADSHEET.xlsx')}}">Sample - Henderson</a>
            <a class="btn btn-info" href="{{asset('public/sample/SAMPLE_SPREADSHEET.xlsx')}}">Sample - NLV</a>
          </div>
        </div>
      </form>
      <hr>
      <select name="batches" id="batches" style="width:120px; height:32px;" class="">
        <option value="all">All Records</option>
        @foreach($batch_list as $batch)
        <option value="{{$batch->BATCH_CODE}}" @if($batch->BATCH_CODE == $current_batch) selected @endif >{{$batch->BATCH_CODE}}</option>
        @endforeach
      </select>
      <a class="btn btn-info" href="{{ route('download-pdf', $current_batch) }}">Get Data</a>
      </div>
  </div>
<?php
    $loc = 'Las Vegas';
    if(isset($tenants[0]) ){
        $loc = $locations[$tenants[0]->LOCATION];
    }
?>
  <h2>Tenant Batch <span class="label label-success">{{$current_batch}}</span> <span class="label label-info">{{$loc}}</span></h2>
  <hr>
  @if(!empty($tenants->count()))
  <table class="table table-bordered">
    <thead style="background-color:aquamarine;">
      <tr>
        <th>#</th>
        <th>Tenant#1</th>
        <th>Tenant#2</th>
        <th>Landlord</th>
        <th>Tenant Address</th>
        <th>Date of service</th>
        <th>City</th>
        <th>Rent Start</th>
        <th>Rent End</th>
        <th>Current Rent Due</th>
        <th>Late Fees</th>
        <th>Court Address</th>
        <th>Server Name</th>
        <th>Server Badge</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
        @foreach($tenants as $tenant)
        @php
        if($loop->iteration %6 == 0){
            $class = 'default';
        } else if($loop->iteration %5 == 0){
            $class = 'danger';
        } else if($loop->iteration %4 == 0){
            $class = 'info';
        } else if($loop->iteration %3 == 0){
            $class = 'primary';
        } else if($loop->iteration %2 == 0){
            $class = 'warning';
        } else {
            $class = 'success';
        }
        @endphp
      <tr class="{{$class}}">
        <td>{{$tenant->ID}}</td>
        <td>{{$tenant->TENANT_1}}</td>
        <td>{{$tenant->TENANT_2 }}</td>
        <td>{{$tenant->LANDLORD_NAME }}</td>
        <td>{{$tenant->TENANT_ADDRESS}}</td>
        <td>{{$tenant->DATE_OF_SERVICE}}</td>
        <td>{{$tenant->CITY}}</td>
        <td>{{$tenant->RENT_PERIOD_START_DATE}}</td>
        <td>{{$tenant->RENT_PERIOD_END_DATE}}</td>
        <td>{{$tenant->CURRENT_RENT_DUE}}</td>
        <td>{{$tenant->LATE_FEES}}</td>
        <td>{{$tenant->JUSTICE_COURT_ADDRESS}}</td>
        <td>{{$tenant->SERVER_NAME}}</td>
        <td>{{$tenant->SERVER_BADGE}}</td>
        <td>
          <a href="javascript:void(0);" class="edit-tenant"
            data-ID="{{$tenant->ID}}"
            data-TENANT_1="{{$tenant->TENANT_1}}"
            data-TENANT_2="{{$tenant->TENANT_2}}"
            data-LANDLORD_NAME="{{$tenant->LANDLORD_NAME}}"
            data-TENANT_ADDRESS="{{$tenant->TENANT_ADDRESS}}"
            data-LANDLORD_ADDRESS="{{$tenant->LANDLORD_ADDRESS}}"
            data-TENANT_CITY_STATE_ZIP="{{$tenant->TENANT_CITY_STATE_ZIP}}"
            data-LANDLORD_CITY_STATE_ZIP="{{$tenant->LANDLORD_CITY_STATE_ZIP}}"
            data-LANDLORD_PHONE="{{$tenant->LANDLORD_PHONE}}"
            data-DATE_OF_SERVICE="{{$tenant->DATE_OF_SERVICE}}"
            data-RENT_PERIOD_START_DATE="{{$tenant->RENT_PERIOD_START_DATE}}"
            data-RENT_PERIOD_END_DATE="{{$tenant->RENT_PERIOD_END_DATE}}"
            data-CURRENT_RENT_DUE="{{$tenant->CURRENT_RENT_DUE}}"
            data-LATE_FEES="{{$tenant->LATE_FEES}}"
            data-TOTAL_OWED="{{$tenant->TOTAL_OWED}}"
            data-CITY="{{$tenant->CITY}}"
            data-JUSTICE_COURT_ADDRESS="{{$tenant->JUSTICE_COURT_ADDRESS}}"
            data-DATE="{{$tenant->DATE}}"
            data-SERVER_NAME="{{$tenant->SERVER_NAME}}"
            data-SERVER_BADGE="{{$tenant->SERVER_BADGE}}"
           ><i class="fa fa-pencil"></i> </a>
          &nbsp;
          <a href="{{route('get-pdf',$tenant->ID)}}" title="Download 7 Days Notice"><i class="fa fa-file-pdf-o"></i></a>
          <a href="{{route('get-pdf-30days',$tenant->ID)}}" title="Download 30 Days Notice"><i class="fa fa-file-pdf-o"></i></a>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <a class="btn btn-warning" style="margin-bottom:10px;" href="{{ route('download-pdf', $current_batch) }}">Download PDF<br><small style="color:green;">7 Days Notice</small></a>
  <a class="btn btn-warning" style="margin-bottom:10px;" href="{{ route('download-pdf-30days', $current_batch)}}">Download PDF<br><small style="color:green;">30 Days Notice</small></a>
  @else
  <div class="alert alert-info">No Record Found!</div>
  @endif
</div>
<script>
  $(document).ready(function(){
    window.ParsleyValidator
        .addValidator('fileextension', function (value, requirement) {
        	var tagslistarr = requirement.split(',');
            var fileExtension = value.split('.').pop();
                var arr=[];
                $.each(tagslistarr,function(i,val){
                    arr.push(val);
                });
            if(jQuery.inArray(fileExtension, arr)!='-1') {
              console.log("is in array");
              return true;
            } else {
              console.log("is NOT in array");
              return false;
            }
        }, 32)
        .addMessage('en', 'fileextension', 'Only Allowed valid excel file.');

        // Validate form
        $('#import-form').parsley();

        // redirect to specific batch
        $('#batches').on('change',function(){
          var batch = $(this).val();
          window.location.href = "?batch_code="+batch;
        });

        $('.edit-tenant').on('click',function(){
            $('#tenantID').val($(this).attr('data-ID'));
            $('#tenant_1').val($(this).attr('data-TENANT_1'));
            $('#tenant_2').val($(this).attr('data-TENANT_2'));
            $('#landlord_name').val($(this).attr('data-LANDLORD_NAME'));
            $('#tenant_address').val($(this).attr('data-TENANT_ADDRESS'));
            $('#landlord_address').val($(this).attr('data-LANDLORD_ADDRESS'));
            $('#tenant_city_state_zip').val($(this).attr('data-TENANT_CITY_STATE_ZIP'));
            $('#landlord_city_state_zip').val($(this).attr('data-LANDLORD_CITY_STATE_ZIP'));
            $('#landlord_phone').val($(this).attr('data-LANDLORD_PHONE'));
            $('#date_of_service').val($(this).attr('data-DATE_OF_SERVICE'));
            $('#rent_start_date').val($(this).attr('data-RENT_PERIOD_START_DATE'));
            $('#rent_end_date').val($(this).attr('data-RENT_PERIOD_END_DATE'));
            $('#current_rent_due').val($(this).attr('data-CURRENT_RENT_DUE'));
            $('#late_fees').val($(this).attr('data-LATE_FEES'));
            $('#total_owed').val($(this).attr('data-TOTAL_OWED'));
            $('#city').val($(this).attr('data-CITY'));
            $('#justice_court_address').val($(this).attr('data-JUSTICE_COURT_ADDRESS'));
            $('#date').val($(this).attr('data-DATE'));
            $('#server_name').val($(this).attr('data-SERVER_NAME'));
            $('#server_badge').val($(this).attr('data-SERVER_BADGE'));
            $('#tenantEditModal').modal('show');
        });
  });
</script>

<div class="modal fade" id="tenantEditModal" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h3 class="modal-title">Edit Tenant Data</h3>
        </div>
        <div class="modal-body">
        <form class="form-horizontal" id="tenant-edit-form" action="{{ route('updateTenantData') }}" method="POST">
          @csrf
          <input type="hidden" name="ID" id="tenantID" value="0" />
          <div class="form-group">
            <label class="control-label col-sm-2" for="email">Tenant #1:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="tenant_1" placeholder="Tenant Name" maxlength="20" name="tenant_1" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="email">Tenant #2:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="tenant_2" placeholder="Tenant Name" maxlength="20" name="tenant_2" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Landlord Name:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="landlord_name" maxlength="20" name="landlord_name" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Tenant Address:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="tenant_address" maxlength="50" name="tenant_address" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Landlord Address:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="landlord_address" maxlength="50" name="landlord_address" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Tenant City, State & Zip:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="tenant_city_state_zip" maxlength="50" name="tenant_city_state_zip" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Landlord City, State & Zip:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="landlord_city_state_zip" maxlength="50" name="landlord_city_state_zip" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Landlord Phone:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="landlord_phone" maxlength="15" name="landlord_phone" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Date Of Service:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="date_of_service" name="date_of_service" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Rent Period Start Date:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="rent_start_date" name="rent_start_date" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Rent Period End Date:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="rent_end_date" name="rent_end_date" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Current Rent Due:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" maxlength="10" id="current_rent_due" name="current_rent_due" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Late Fees:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="late_fees" maxlength="10" name="late_fees" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Total Owed:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" maxlength="10" id="total_owed" name="total_owed" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">City:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" mqxlength="20" id="city" name="city" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Justice Court Address:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" maxlength="50" id="justice_court_address" name="justice_court_address" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Date:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="date" name="date" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Server Name:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="server_name" name="server_name" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="pwd">Server Badge:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="server_badge" name="server_badge" data-parsley-required="true" />
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <button type="submit" class="btn btn-success">Update Data</button>
            </div>
          </div>
        </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
