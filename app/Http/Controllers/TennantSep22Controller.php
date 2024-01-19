<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TenantsImport;
use App\Models\Tenant;
use DB;

class TennantSep22Controller extends Controller {
    public function __construct(){
        $this->middleware('auth');
      }

      public $clear = 'iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAMAAAAJbSJIAAAAclBMVEX///8AAAD39/fd3d3a2tr8/Pzw8PDz8/NdXV2ysrJ+fn4WFhZYWFjg4OBHR0fBwcHNzc1zc3NCQkIlJSVlZWWDg4M5OTkbGxsvLy+Ojo7n5+ekpKRwcHBMTEwKCgrExMSVlZUsLCycnJy0tLSJiYkZGRns73CNAAAEG0lEQVR4nO2dWXfaMBhE2Wwwm8MOSVga2v//FxvagyxoUySNjManc59HyneRLMnOg1otIYQQQgghhBBC/Gd0i+F41E7PaDwsutHtsvMytdgdyzKL6Nedpfb5K8NJJL/JILXKlyxjOGbD1Br/ZAXP1WNqhUccXjBB7gH8zQ7wy/apq3diHCyYL1LX7si6Eyh439H0VPby4N8rFnmvPE3vKjsEKXY2N51si37sWgH6xbfb6kI6Wds9zMEVqwZebgrc+3dwc4wp4hcYgTe7RO8VtbQHkGl+2uRbq0rPWdaxmr7XU14UrAPlyK+lNUdX9dQWiffAedptxgheGFel+uxkVbN5baXFoto2Zu6NetXvwrrIVLyGDGL1FHJuE7d8+D+J1ULKP0cvVMdn1xZF6B6TiNK7XrPOrGstLB7mC+DQLV9N0iY8hRfMk7hwy78Yw8DXrqdTLaevTnlzoA1/d3425nxaOsXNXvFRc13xMJ+TTk7xebNW0gtm9Xc7Y3pOagbMIcztRdh7/0yPWWo2TvEGGvb9am6goWfNMmREhkiaAxkiaQ5kiKQ5kCGS5kCGSJoDGSJpDmSIpDmQIZLmQIZImgMZImkOZIikOZAhkuZAhkiaAxkiaQ5kiKQ5kCGS5kCGSJoDGSJpDmSIpDmQIZLmQIZImgMZImkOZIikOZAhkuZAhkiaAxkiaQ5kiKQ5kCGS5kCGSJoDGSJpDmSIpDmQIZLmQIZImgMZImkOZIikOZAhkuZAhkiaAxkiaQ5kiKQ5kCGS5kCGSJoDGSJpDmSIpDmQIZLmQIZImgMZImkOZIikOZAhkuZAhkiaAxkiaQ5kiKQ5kCGS5kCGSJoDGSJpDmSIpDmQIZLmQIZImgMZImkOZIikOZAhkuZAhkiaAxkiaQ5k+EW6KZflet/wuLmmJzXXFY/utWS3K48H1/ix5rriYW7mHjjFPS/XZWB3LXnlFDeX605rrise5grjs1PcTOp2XnNhschNxY5Lh8k35W51c5P4wbHB8tpgW2td8VhcC3a7tLrVOptBbMbN3GYldbxavdXKTIt5rZXFwtwd734KM/tFI55E8xS2vzu3mZg27X6NpcXhtSrW4zJ4c6xpwDyt5ujSo5U1iK7LUyoGQUNoP4mOB6FUzKpC3Z/CC9Vyyj2K1gi67vZXSktxzvqimG+tKr33bmv4XQ+0z+bNLtFvjv5ibbef8p1ujjcF7gN66GzsHtrrgmmu5m+Lm+q2WVAv7Tv2p2Mv/Qkg75W7+X1pgb9+vrjviJR18PTKpqlrd2Ic6ndh+Lj/5OwQwc8VK3X9jxjBq3w2e/xXEhKwDf5Jd5xa40uWXoftf9DjHMdVzI/y2Xn5+C8+k8O76zcZD8leMRyPUpt98mOwKprzLxUhhBBCCCGEEEJE4ifUgCi+cjTNMgAAAABJRU5ErkJggg==';

      public $check = 'iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAQAAABLCVATAAAAhklEQVR4Ae3MMQ5AQBCF4Sds6UJaZ3ABnU6p4yoqnSi2cionQCSbV4kYswmR/aed9yH0aqEIuR+mx4baD3NcpmMGx3T/YWJfzPghJpExBSYkp4yVMCVWbLCkxAwh925hAGYEDKvcZIYhM5NhQuohw2pS6SUjoBYdQ4qMqoaMupaMuhz3CoV2nFZlUMaqz88AAAAASUVORK5CYII=';

      /**
       * @return \Illuminate\Support\Collection
       */
      public function importExportView(Request $request) {
          ini_set('memory_limit', '-1');
          $this->delRecords();
          $latest_batch = $this->getLatestBatch();
          $current_batch = (!empty($latest_batch)) ? $latest_batch->BATCH_CODE : "all";
          if ($request->has('batch_code') && !empty($request->batch_code)) {
              if ($request->batch_code == 'all') {
                  $current_batch = 'all';
                  $tenants = Tenant::get();
              } else {
                  $batch = $this->checkValidBatchCode($request->batch_code);
                  $current_batch = $batch->BATCH_CODE;
                  $tenants = Tenant::where('BATCH_CODE', $batch->BATCH_CODE)->get();
              }
          } else {
              $tenants = Tenant::where('BATCH_CODE', $current_batch)->get();
          }
          $batch_list = $this->getBatchList();
          return view('import', compact('tenants', 'current_batch', 'batch_list'));
      }

      private function checkValidBatchCode($batch_code) {
          $batch = Tenant::where('BATCH_CODE', $batch_code)->first();
          if (empty($batch)) {
              $batch = $this->getLatestBatch();
          }
          return $batch;
      }

      /**
       * @return \Illuminate\Support\Collection
       */
      public function import(Request $request) {
          Excel::import(new TenantsImport, request()->file('file'));
          $batch = $this->getLatestBatch();
          return redirect()->route('base',array('batch_code' => $batch->BATCH_CODE));
      }

      private function getLatestBatch(){
          $batch = Tenant::orderByDesc('BATCH_ID')->first();
          return $batch;
      }

      private function getBatchList() {
          $batch_list = DB::table('tenants')
              ->select('BATCH_CODE', DB::raw('count(*) as total'))
              ->groupBy('BATCH_CODE')
              ->get();
          return $batch_list;
      }

      public function getPdf($id) {
          $data = Tenant::where('ID', $id)->first();
          $file = $this->dbToPDF($data);
          return response()->download($file);
      }

      public function getPdf30Days($id) {
          $data = Tenant::where('ID', $id)->first();
          $file = $this->dbToPDF30Days($data);
          return response()->download($file);
      }

      public function makeZip($path = 'pdf', $batch_name = "test") {
          $zipper = new \Chumper\Zipper\Zipper;
          $files = glob(public_path($path . '/*'));
          $zipper->make($p = public_path('download/' . $batch_name . ".zip"))->add($files)->close();
          return $p;
      }

      public function downloadNotice($batch = 'all') {
          $this->delRecords();
          $page = 1;
          if ($batch == 'all' || empty($batch) || !isset($batch)) {
                $data = Tenant::limit(500)->get();
          } else {
                $data = Tenant::where('BATCH_CODE', $batch)->get();
          }
          foreach ($data as $model) {
            $this->dbToPDF($model, 'zip');
          }
          $file = $this->makeZip('pdf', $batch);
          return response()->download($file);
      }

      public function downloadNotice30Days($batch = 'all') {
          $this->delRecords();
          $page = 1;
          if ($batch == 'all' || empty($batch) || !isset($batch)) {
                $data = Tenant::limit(500)->get();
          } else {
                $data = Tenant::where('BATCH_CODE', $batch)->get();
          }
          foreach ($data as $model) {
            $this->dbToPDF30Days($model, 'zip');
          }
          $file = $this->makeZip('pdf', $batch);
          return response()->download($file);
      }

      public function validateBatchCode(Request $request) {
          $code = $request->input('batch_code');
          if (Tenant::where('BATCH_CODE', '=', $code)->exists()) {
              abort(404);
          }
          return response('Not Found', 200)->header('Content-Type', 'text/plain');
      }

        public function delAll(){
          $this->delRecords();
      }

      public function updateTenantData(Request $request) {
          $tenant = Tenant::where('ID', $request->ID)->first();
          $tenant->TENANT_1 = $request->tenant_1;
          $tenant->TENANT_2 = $request->tenant_2;
          $tenant->LANDLORD_NAME = $request->landlord_name;
          $tenant->TENANT_ADDRESS = $request->tenant_address;
          $tenant->LANDLORD_ADDRESS = $request->landlord_address;
          $tenant->TENANT_CITY_STATE_ZIP = $request->tenant_city_state_zip;
          $tenant->LANDLORD_CITY_STATE_ZIP = $request->landlord_city_state_zip;
          $tenant->LANDLORD_PHONE = $request->landlord_phone;
          $tenant->DATE_OF_SERVICE = $request->date_of_service;
          $tenant->RENT_PERIOD_START_DATE = $request->rent_start_date;
          $tenant->RENT_PERIOD_END_DATE = $request->rent_end_date;
          $tenant->CURRENT_RENT_DUE = $request->current_rent_due;
          $tenant->LATE_FEES = $request->late_fees;
          $tenant->TOTAL_OWED = $request->total_owed;
          $tenant->CITY = $request->city;
          $tenant->JUSTICE_COURT_ADDRESS = $request->justice_court_address;
          $tenant->DATE = $request->date;
          $tenant->SERVER_NAME = $request->server_name;
          $tenant->SERVER_BADGE = $request->server_badge;
          if ($tenant->save()) {
              return back()->with('success', 'Tenant Updated Successfully!');
          }
          return back()->with('error', 'Error Occurred in updating!');
      }

      public function dbToPDF($model = null, $type = 'single') {
          $tenant_name = $model->TENANT_1;
          if(!empty($model->TENANT_2)){
              $tenant_name .= ", " . $model->TENANT_2;
          }
          $tenant_name .= ", etal.";
          $tenant_address = $model->TENANT_ADDRESS;
          if(!empty($model->APARTMENT_UNIT)){
              $tenant_address .= " #". $model->APARTMENT_UNIT;
          }
          $row = [
              'tenant_name' => strtoupper($tenant_name),
              'landlord_name' => strtoupper($model->LANDLORD_NAME),
              'tenant_address' => strtoupper($tenant_address),
              'landlord_address' => strtoupper($model->LANDLORD_ADDRESS),
              'tenant_city_state_zip' => strtoupper($model->TENANT_CITY_STATE_ZIP),
              'landlord_city_state_zip' => strtoupper($model->LANDLORD_CITY_STATE_ZIP),
              'dos' => strtoupper($model->DATE_OF_SERVICE),
              'landlord_phone' => $model->LANDLORD_PHONE,
              'rent_start_date' => $model->RENT_PERIOD_START_DATE,
              'rent_end_date' => $model->RENT_PERIOD_END_DATE,
              'late_fee' => $model->LATE_FEES,
              'total_owed' => round($model->TOTAL_OWED, 2),
              'city' => strtoupper($model->CITY),
              'court_address' => strtoupper($model->JUSTICE_COURT_ADDRESS),
              'date' => $model->DATE,
              'c1' => false,
              'c2' => false,
              'c3' => true,
              'server_name' => strtoupper($model->SERVER_NAME),
              'server_badge' => strtoupper($model->SERVER_BADGE),
              'current_rent_due' => $model->CURRENT_RENT_DUE
          ];
          $pdf = new \TCPDF('P', 'mm', 'USLETTER');
          $pdf->SetCreator(PDF_CREATOR);
          $pdf->SetAuthor('Nicola Asuni');
          $pdf->SetTitle('TCPDF Example 002');
          $pdf->SetSubject('TCPDF Tutorial');
          $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
          $pdf->setPrintHeader(false);
          $pdf->setPrintFooter(false);
          $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
          $pdf->SetAutoPageBreak(TRUE, 7);
          $pdf->SetMargins(16, 4, 15);
          $pdf->AddPage();

          $pdf->SetAlpha(1);
          $x = $pdf->GetX();
          $y = $pdf->GetY();
          $pdf->SetXY($x, $y);
          $pdf->SetFont('times', 'B', 10);
          $pdf->Write(0, 'SUMMARY EVICTION SEVEN-DAY NOTICE TO PAY RENT OR QUIT', '', 0, 'C', true, 0, false, false, 0);
          $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
          $pdf->Line($pdf->GetX() + 30, $pdf->GetY() - 0.5, $pdf->GetX() + 155, $pdf->GetY() - 0.5, $style);
          $pdf->SetFont('times', 'B', 8);
          $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 0.5, false);
          $pdf->Write(0, '(NRS 40.253)', '', 0, 'C', true, 0, false, false, 0);

          $pdf->Ln(1);
          $pdf->SetFont('times', '', 10);
          $addr_1 = $model->TENANT_ADDRESS . ', #'. $model->APARTMENT_UNIT;
          $addr_2 = $model->TENANT_CITY_STATE_ZIP;
          $l_addr1 = $model->LANDLORD_ADDRESS;
          $l_addr2 = $model->LANDLORD_CITY_STATE_ZIP;
          $rent_start = $model->RENT_PERIOD_START_DATE;
          $rent_end = $model->RENT_PERIOD_END_DATE;
          $tbl = <<<EOD
                  <table cellspacing="0" cellpadding="1" border="1">
                  <tr>
                      <td style="text-align: center;"><strong>TO: {$tenant_name}</strong> <br/>{$addr_1} <br>{$addr_2}<br></td>
                      <td style="text-align: center;"><strong>FROM: {$model->LANDLORD_NAME}</strong><br>{$l_addr1}<br>{$l_addr2}<br>{$model->LANDLORD_PHONE}</td>
                  </tr>
              </table>
              EOD;

          $pdf->writeHTML($tbl, true, false, false, false, '');
          $pdf->SetXY(17, $pdf->GetY() + 1, false);
          $pdf->SetFont('times', '', 10);
          $pdf->Cell(0, 4, 'DATE OF SERVICE:___________', 0, 0, 'L');
          $pdf->SetX(42);
          $pdf->SetFont('times', 'B', 10);
          $pdf->Cell(35, 4, $row['dos'], 0, 0, 'C');

          $pdf->Ln(5);
          $pdf->SetLeftMargin(18);
          $pdf->SetFont('times', '', 10);
          $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 1, false);
          $pdf->writeHTML("PLEASE TAKE NOTICE that you are in default in payment of rent for the above-described premises for the period  {$rent_start} - {$rent_end}. You are in default in amount of:");
          $pdf->Ln(1);

          $pdf->SetLeftMargin(50);
          $html_1 = '<div><strong>Rent:  $<u>' . $row['current_rent_due'] . '</u></strong></div>';
          $pdf->WriteHTML($html_1, true, false, true, false);
          $html6 = '<div><strong>Late Fees:  $<u>' . $row['late_fee'] . '</u></strong></div>';
          $pdf->WriteHTML($html6, true, false, true, false);
          $html7 = '<div><strong>Total Owed: $<u>' . $row['total_owed'] . '</u></strong></div>';
          $pdf->WriteHTML($html7, true, false, true, false);
          $pdf->Ln(2);

          $pdf->SetLeftMargin(10);
          $pdf->SetRightMargin(10);
          $pdf->SetXY(18, $pdf->GetY() + 1 , false);
          $pdf->setFontSpacing(0);
          $pdf->SetFont('times', '', 9);

          $locations = array(
              1 => 'Las Vegas',
              2 => 'Henderson',
              3 => 'North Las Vegas'
            );

          if($model->LOCATION == 0){
              $current_location = 'Las Vegas';
          } else {
              $current_location = $locations[$model->LOCATION];
          }

          $html8 = "TENANTS ARE ADVISED THAT THE LAS VEGAS JUSTICE COURT HAS INFORMATION ON ITS WEBSITE CONCERNING THE AVAILABLITY OF MEDIATION, GOVERNMENT SPONSORED RENTAL ASSISTANCE, AND ELECTRONIC FILING FOR THE TENANT AFFIDAVIT, AMONG OTHER MATTERS. A TENANT MAY ACCESS THIS INFORMATION AT <a href='http://www.lasvegasjusticecourt.us'>http://www.lasvegasjusticecourt.us</a>";
          $pr1 = "Rental Assistance is available at <a href='https://chap.clarkcountynv.gov'>https://chap.clarkcountynv.gov</a>";
          if($model->LOCATION == 2){
              $html8 = "Tenants are advised that the Henderson Justice Court has information on its website concerning the availability of mediation and government sponsored rental assistance, among other matters. A tenant may access this information at <a href='www.clarkcountynv.gov/hjc'>www.clarkcountynv.gov/hjc</a>.";
          }
          if($model->LOCATION == 3) {
              $html8 = '<div>Tenants are advised that information concerning the availability of mediation and government sponsored rental assistance may be accessed at <a href="www.clarkcountynv.gov/government/departments/justice_courts/jurisdictions/north_las_vegas/index.php">www.clarkcountynv.gov/government/departments/justice_courts/jurisdictions/north_las_vegas/index.php</a></div>';
              $pr1 = "Rental assistance is available at <a href='www.link2hope.org'>www.link2hope.org</a>";
          }

          $pdf->WriteHTML($html8, true, false, true, false);
          $pdf->Ln(1);

          $html9 = '<div>'. $pr1 .', If you have a pending application for rental assistance, or you if your landlord has refused to participate in the rental assistance process or has refused to accept rental assistance on your behalf, you have the right to assert those facts as a defense to this eviction at any point in the proceedings. Should you assert this defense to the court, the court will determine if your case is designated as one that may be paused until a determination on your rental assistance application is made or until a hearing is held for you to prove the validity of your claim of the Landlord’s refusal. <br/><br/>Your Landlord IS NOT requesting an exemption from any pause in this eviction case due to a realistic threat of foreclosure of the rental property if unable to evict you.<br/><br/>Additionally, if the court determines that your case is designated as one mandating mediation, you may receive an order setting a hearing and notification of mediation after you file an affidavit contesting the eviction notice. The eviction case will be paused for not more than 30 days to facilitate mediation.</div>';
          $pdf->WriteHTML($html9, true, false, true, false);
          $pdf->Ln(2);

          $pdf->writeHTML('Your failure to pay rent or vacate the premises before the close of business on the seventh judical day<sup>1</sup> following the Date of Service of this notice may result in your landlord applying to the Justice Court for an eviction order. If the court determines you are guilty of an unlawful detainer, the court may issue a summary order for your removal or an order providing for your NON-ADMITTANCE, directing the sheriff/constable to post the order in a conspicuous place on the premises not later than 24 hours after the order is received by the sheriff/constable. The sheriff/constable shall then remove you not earlier than 24 hours but not later than 36 hours after posting of the order. Pursuant to NRS 118A.390, you may seek relief if a landlord unlawfully removes you from the premises, excludes you by blocking or attempting to block your entry upon the premises, or willfully interrupts or causes or permits the interruption of an essential service required by the rental agreement or per chapter 118A of the Nevada Revised Statutes');

          $pdf->Ln(3);
          $addr1 = $this->removeZipFromAddress($row['tenant_address']);
          $html_text1 = "<b>YOU ARE HEREBY ADVISED OF YOUR RIGHT T CONTEST THIS NOTICE by filing an Affidavit no later than by the close of business<sup>2</sup> on the seventh judical day following the Date of Service of this notice, with the Justice Court for the city of " . $model->CITY.", stating that you have tendered payment or are not in default of rent. You can fill out the forms and file electronically at <a href='https://nevada.tylerhost.net/SRL/srl/'>https://nevada.tylerhost.net/SRL/srl/</a> (choose “SUMMARY EVICTION: Tenant’s Answer”). You can file your forms in-person at the ".$model->CITY. " Justice Court located at " . $model->JUSTICE_COURT_ADDRESS . ".<br>";
          if($model->LOCATION == 2){
              $html_text1 = "<b>YOU ARE HEREBY ADVISED OF YOUR RIGHT T CONTEST THIS NOTICE by filing an Affidavit no later than by the close of business<sup>2</sup> on the seventh judical day following the Date of Service of this notice, with the Justice Court for the township of " . $model->CITY.", stating that you have tendered payment or are not in default of rent. You can file your forms in-person at the ".$model->CITY. " Justice Court located at 243 S WATER ST; HENDERSON NV 89015.<br>";
          }
          if($model->LOCATION == 3){
              $html_text1 = "<b>YOU ARE HEREBY ADVISED OF YOUR RIGHT T CONTEST THIS NOTICE by filing an Affidavit no later than by the close of business<sup>2</sup> on the seventh judical day following the Date of Service of this notice, with the Justice Court for the city of North Las Vegas, stating that you have tendered payment or are not in default of rent. You can file your forms in-person at the North Las Vegas Justice Court located at " . $model->JUSTICE_COURT_ADDRESS . ".<br>";
          }
          $pdf->writeHTML($html_text1);
          $pdf->Ln(3);
          $pdf->SetFont('times', 'B', 11);
          $pdf->writeHTML('<div style="text-align:center;"><u>DECLARATION OF SERVICE OF SEVEN-DAY NOTICE TO PAY OR QUIT</u></div>');
          $pdf->Ln(4);
          $pdf->SetFont('times', '', 9);
          $pdf->Write(0, 'On  ');
          $pdf->writeHTML("<u> " . $row['dos'] . " </u>, I served a Seven-Day Notice to Pay or Quit to the following address in the following manner:");
          $pdf->Ln(2);

          $pdf->SetLeftMargin(62);
          $pdf->writeHTML("&nbsp;&nbsp;<u>" . $row['tenant_address'] .", &nbsp;" .$model->TENANT_CITY_STATE_ZIP."</u>");
          $pdf->Ln(2);
          $pdf->SetFont('times', '', 8);
          $pdf->Ln(1);

          $pdf->SetLeftMargin(18);
          $pdf->Ln(2);
          $pdf->Image('@' . base64_decode($row['c3'] ? $this->check : $this->clear), '', '', 4, 5, 'PNG', '');
          $pdf->SetX(25);
          $pdf->SetLeftMargin(25);
          $pdf->writeHTML("Because neither Tenant nor a person of suitable age or discretion could be found there, by posting a copy in a conspicuous place on the property, AND mailing a copy to the Tenant(s) at the place the property is situated.");
          $pdf->SetLeftMargin(18);
          $pdf->Ln(3);
          $pdf->SetLeftMargin(25);
          $pdf->writeHTML("<i>I declare under penalty of perjury under the laws of the State of Nevada that the foregoing is true and correct.
          </i>");
          $pdf->Ln(3);

          $pdf->SetFont('times', 'U', 8);
          $pdf->Cell(40, 5, '_____' . $row['dos'] . '_____', 0, false, 'L', false);
          $pdf->Cell(45, 5, '_____' . $row['server_name']. '_____', 0, false, 'C', false);
          $pdf->Cell(45, 5, '__________', 0, false, 'C', false);
          $pdf->Cell(45, 5, '_____' . $row['server_badge']. '_____', 0, false, 'C', false);

          $pdf->Ln(3);
          $pdf->SetFont('times', 'I', 8);
          $pdf->Cell(25, 5, '(DATE)', 0, 0, 'C');
          $pdf->SetX($pdf->GetX() + 15);
          $pdf->Cell(45, 5, "(SERVER’S NAME)", 0, false, 'C', false);
          $pdf->Cell(45, 5, '(SERVER’ SIGNATURE)', '', false, 'C', false);
          $pdf->setX($pdf->GetX() + 11);
          $pdf->writeHTML('(BADGE/LICENCE <sup>3</sup>)');
          $pdf->Ln(4);

          $pdf->SetFont('times', '', 9);
          $pdf->writeHTML("<br>Because of the global COVID-19 pandemic, you may be eligible for temporary protection from eviction under the laws of your State, Territory, locality, or tribal area, or under Federal law. Learn the steps you should take now: Visit <a href='www.cfpb.gov/eviction'>www.cfpb.gov/eviction</a> or call a housing counselor at 1-800-569-4287.");

          $pdf->Ln(5);
          $pdf->SetFont('times', '', 5);

          $footer_notes = "<sup>1</sup> " . strtoupper('judical Days do not include the date of service, Saturdays, Sundays or certain legal holidays.')."<br><sup>2</sup> ".strtoupper('Las Vegas Justice Court closes at 5:30pm on Mon-Thurs and 4pm on Fridays.')."<br/><sup>3</sup> ".strtoupper('A server who does not have a badge/license number may be an agent of an attorney licensed in Nevada. Notices served by agents must include an attorney declaration as proof of service.');
          if($model->LOCATION == 2){
              $footer_notes = "<sup>1</sup> ".strtoupper('judical days do not include the date of service, Fridays, Saturdays, Sundays, or certain legal holidays.')."<br><sup>2</sup> ".strtoupper('HENDERSON Justice Court is open Monday-Thursday 7am-4:30pm.')."<br/><sup>3</sup> ".strtoupper('A server who does not have a badge/license number may be an agent of an attorney licensed in Nevada. Notices served by agents must include an attorney declaration as proof of service.');
          }

          if($model->LOCATION == 3){
              $footer_notes = "<sup>1</sup> ".strtoupper('judical days do not include the date of service Friday, Saturday, Sunday or certain legal holidays.')."<br><sup>2</sup> ".strtoupper('North Las Vegas Justice Court is open Monday-Thursday 8am - 4:30pm.')."<br/><sup>3</sup> ".strtoupper('A server who does not have a badge/license number may be an agent of an attorney licensed in Nevada. Notices served by agents must include an attorney declaration as proof of service.');
          }

          $pdf->writeHTML($footer_notes, 0);
          $path = public_path('pdf');
          File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
          $fileName = $this->getFileName($model->TENANT_1, $model->TENANT_1);
          if ($type == 'zip') {
              $pdf->Output("$path/{$fileName }.pdf", 'F');
          } else if ($type == 'output') {
              $pdf->Output("$fileName.pdf");
          } else {
              $pdf->Output("$fileName.pdf", 'D');
          }
      }

      private function delRecords() {
          $file = new Filesystem;
          $file->cleanDirectory('public/pdf');
          $file->cleanDirectory('public/download');
          return true;
      }

      private function getFileName($tenantName, $fileName = "", $num = 2) {
          $fullFileName = $fileName . ".pdf";
          if (!file_exists("public/pdf/{$fullFileName}")) {
              return $fileName;
          } else {
              $file = $tenantName . "-{$num}";
              $num++;
              return $this->getFileName($tenantName, $file, $num);
          }
      }

      public function dbToPDF30Days($model = null, $type = 'single') {
          $tenant_name = $model->TENANT_1;
          if(!empty($model->TENANT_2)){
              $tenant_name .= ", " . $model->TENANT_2;
          }
          $tenant_name .= ", etal.";
          $tenant_address = $model->TENANT_ADDRESS;
          if(!empty($model->APARTMENT_UNIT)){
              $tenant_address .= " #". $model->APARTMENT_UNIT;
          }
          $row = [
              'tenant_name' => strtoupper($tenant_name),
              'landlord_name' => strtoupper($model->LANDLORD_NAME),
              'tenant_address' => strtoupper($tenant_address),
              'landlord_address' => strtoupper($model->LANDLORD_ADDRESS),
              'tenant_city_state_zip' => strtoupper($model->TENANT_CITY_STATE_ZIP),
              'landlord_city_state_zip' => strtoupper($model->LANDLORD_CITY_STATE_ZIP),
              'dos' => strtoupper($model->DATE_OF_SERVICE),
              'landlord_phone' => $model->LANDLORD_PHONE,
              'rent_start_date' => $model->RENT_PERIOD_START_DATE,
              'rent_end_date' => $model->RENT_PERIOD_END_DATE,
              'late_fee' => $model->LATE_FEES,
              'total_owed' => round($model->TOTAL_OWED, 2),
              'city' => strtoupper($model->CITY),
              'court_address' => strtoupper($model->JUSTICE_COURT_ADDRESS),
              'date' => $model->DATE,
              'c1' => false,
              'c2' => false,
              'c3' => true,
              'server_name' => strtoupper($model->SERVER_NAME),
              'server_badge' => strtoupper($model->SERVER_BADGE),
              'current_rent_due' => $model->CURRENT_RENT_DUE
          ];
          $pdf = new \TCPDF('P', 'mm', 'USLETTER');
          $pdf->SetCreator(PDF_CREATOR);
          $pdf->SetAuthor('Nicola Asuni');
          $pdf->SetTitle('TCPDF Example 002');
          $pdf->SetSubject('TCPDF Tutorial');
          $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
          $pdf->setPrintHeader(false);
          $pdf->setPrintFooter(false);
          $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
          $pdf->SetAutoPageBreak(TRUE, 7);
          $pdf->SetMargins(16, 4, 15);
          $pdf->AddPage();
          $pdf->SetAlpha(1);
          $x = $pdf->GetX();
          $y = $pdf->GetY();

          $pdf->SetXY($x, $y);
          $pdf->SetFont('times', 'B', 10);
          $pdf->Write(0, '30-DAY NOTICE TO PAY RENT OR QUIT (CARES ‘COVERED PROPERTIES’)', '', 0, 'C', true, 0, false, false, 0);
          $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
          $pdf->Line($pdf->GetX() + 30, $pdf->GetY() - 0.5, $pdf->GetX() + 155, $pdf->GetY() - 0.5, $style);
          $pdf->SetFont('times', 'B', 8);
          $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 0.5, false);
          $pdf->Write(0, '(CARES Act, HR 748 116th Congress, § 4024)', '', 0, 'C', true, 0, false, false, 0);

          $pdf->Ln(1);
          $pdf->SetFont('times', '', 10);
          $addr_1 = $model->TENANT_ADDRESS . ', #'. $model->APARTMENT_UNIT;
          $addr_2 = $model->TENANT_CITY_STATE_ZIP;
          $l_addr1 = $model->LANDLORD_ADDRESS;
          $l_addr2 = $model->LANDLORD_CITY_STATE_ZIP;
          $rent_start = $model->RENT_PERIOD_START_DATE;
          $rent_end = $model->RENT_PERIOD_END_DATE;
          $tbl = <<<EOD
                  <table cellspacing="0" cellpadding="1" border="1">
                      <tr>
                          <td style="text-align: center;"><strong>TO: {$tenant_name}</strong> <br/>{$addr_1} <br>{$addr_2}<br></td>
                          <td style="text-align: center;"><strong>FROM: {$model->LANDLORD_NAME}</strong><br>{$l_addr1}<br>{$l_addr2}<br>{$model->LANDLORD_PHONE}</td>
                      </tr>
                  </table>
              EOD;

          $pdf->writeHTML($tbl, true, false, false, false, '');
          $pdf->SetXY(17, $pdf->GetY() + 1, false);
          $pdf->SetFont('times', '', 10);
          $pdf->Cell(0, 4, 'DATE OF SERVICE:___________', 0, 0, 'L');
          $pdf->SetX(42);
          $pdf->SetFont('times', 'B', 10);
          $pdf->Cell(35, 4, $row['dos'], 0, 0, 'C');

          $pdf->Ln(5);
          $pdf->SetLeftMargin(18);
          $pdf->SetFont('times', '', 10);
          $pdf->SetXY($pdf->GetX(), $pdf->GetY() - 1, false);
          $pdf->writeHTML("PLEASE TAKE NOTICE that you are in default in payment of rent for the above-described premises for the period  {$rent_start} - {$rent_end}. You are in default in amount of:");
          $pdf->Ln(1);

          $pdf->SetLeftMargin(50);
          $html_1 = '<div><strong>Rent:  $<u>' . $row['current_rent_due'] . '</u></strong></div>';
          $pdf->WriteHTML($html_1, true, false, true, false);
          $html6 = '<div><strong>Late Fees:  $<u>' . $row['late_fee'] . '</u></strong></div>';
          $pdf->WriteHTML($html6, true, false, true, false);
          $html7 = '<div><strong>Total Owed: $<u>' . $row['total_owed'] . '</u></strong></div>';
          $pdf->WriteHTML($html7, true, false, true, false);
          $pdf->Ln(2);

          $pdf->SetLeftMargin(10);
          $pdf->SetRightMargin(10);
          $pdf->SetXY(18, $pdf->GetY() + 1 , false);
          $pdf->setFontSpacing(0);
          $pdf->SetFont('times', '', 9);

          $locations = array(
              1 => 'Las Vegas',
              2 => 'Henderson',
              3 => 'North Las Vegas'
            );

          if($model->LOCATION == 0){
              $current_location = 'Las Vegas';
          } else {
              $current_location = $locations[$model->LOCATION];
          }

          $html8 = '<div>TENANTS ARE ADVISED THAT THE LAS VEGAS JUSTICE COURT HAS INFORMATION ON ITS WEBSITE CONCERNING THE AVAILABLITY OF MEDIATION, GOVERNMENT SPONSORED RENTAL ASSISTANCE, AND ELECTRONIC FILING FOR THE TENANT AFFIDAVIT, AMONG OTHER MATTERS. A TENANT MAY ACCESS THIS INFORMATION AT <a href="http://www.lasvegasjusticecourt.us/index.php">http://www.lasvegasjusticecourt.us</a></div>';
          $html_s1 = "You can fill out forms and file electronically at <a href='https://nevada.tylerhost.net/SRL/srl'>https://nevada.tylerhost.net/SRL/srl</a> (choose “SUMMARY EVICTION: Tenant's Answer”). If you do not have internet access, ";
          $h_city = "Las Vegas";
          if($model->LOCATION == 2){
              $html_s1 = "";
              $html8 = '<div>Tenants are advised that the Henderson Justice Court has information on its website concerning the availability of mediation and government sponsored rental assistance, among other matters. A tenant may access this information at <a href="www.clarkcountynv.gov/hjc">www.clarkcountynv.gov/hjc</a>.</div>';
              $h_city = "HENDERSON";
          }
          if($model->LOCATION == 3){
              $html8 = "<div>Tenants are advised that information concerning the availability of mediation and
              government sponsored rental assistance may be accessed at: <a href='www.clarkcountynv.gov/government/departments/justice_courts/jurisdictions/north_las_vegas/index.php'>www.clarkcountynv.gov/government/departments/justice_courts/jurisdictions/north_las_vegas/index.php</a></div>";
              $html_s1 = "";
              $h_city = "North Las Vegas";
          }
          $pdf->WriteHTML($html8, true, false, true, false);
          $pdf->Ln(1);

          $html9 = '<div>Rental Assistance is available at <a href="https://chap.clarkcountynv.gov">https://chap.clarkcountynv.gov</a>. If you have a pending application for rental assistance, or if your landlord has refused to participate in the rental assistance process or has refused to accept rental assistance on your behalf, you have the right to assert those facts as a defense to this eviction at any point in the proceedings. Should you assert this defense to the court, the court will determine if your case is designated as one that may be paused until a determination on your rental assistance application is made or until a hearing is held for you to prove the validity of your claim of the Landlord’s refusal. <br/><br/>Your Landlord IS NOT requesting an exemption from any pause in this eviction case due to a realistic threat of foreclosure of the rental property if unable to evict you. The rental unit is a “covered property” under § 4024(a)(2).<br/><br/>Additionally, if the court determines that your case is designated as one mandating mediation, you may receive an order setting a hearing and notification of mediation after you file an affidavit contesting the eviction notice. The eviction case will be paused for not more than 30 days to facilitate mediation.</div>';
          $pdf->WriteHTML($html9, true, false, true, false);
          $pdf->Ln(2);

          $pdf->writeHTML('Your failure to pay rent or vacate the premises before the close of business on the 30th calendar day<sup>1</sup> following the Date of Service of this notice may result in your landlord applying to the Justice Court for an eviction order. If the court determines you are guilty of an unlawful detainer, the court may issue a summary order for your removal or an order providing for your non admittance, directing the sheriff or constable to post the order in a conspicuous place on the premises not later than 24 hours after the order is received by the sheriff or constable. The sheriff or constable shall then remove you not earlier than 24 hours but not later than 36 hours after posting of the order. Pursuant to NRS 118A.390, you may seek relief if a landlord unlawfully removes you from the premises, excludes you by blocking or attempting to block your entry upon the premises, or willfully interrupts or causes or permits the interruption of an essential service required by the rental agreement or per chapter 118A of the Nevada Revised Statutes.');

          $pdf->Ln(3);
          $addr1 = $this->removeZipFromAddress($row['tenant_address']);
          $html_text1 = "<b>YOU ARE HEREBY ADVISED OF YOUR RIGHT TO CONTEST THIS NOTICE by filing an Affidavit no later than by the close of business<sup>2</sup> on the thirtieth (30th) calendar day following the Date of Service of this notice, with the Justice Court for the city of " . $model->CITY.", stating that you have tendered payment or are not in default of rent. " . $html_s1 . "you can file your forms in-person at the " .$h_city. " Justice Court located at " . $model->JUSTICE_COURT_ADDRESS . ".<br>";

          if($model->LOCATION == 2){
              $html_text1 = "<b>YOU ARE HEREBY ADVISED OF YOUR RIGHT TO CONTEST THIS NOTICE by filing an Affidavit no later than by the close of business<sup>2</sup> on the 30th calendar day following the Date of Service of this notice, with the Justice Court for the township of " . $model->CITY.", stating that you have tendered payment or are not in default of rent. " . $html_s1 . "You can file your forms in-person at the " .$h_city. " Justice Court located at " . $model->JUSTICE_COURT_ADDRESS . ".<br>";
          }

          $pdf->writeHTML($html_text1);
          $pdf->Ln(3);
          $pdf->SetFont('times', 'B', 11);
          $pdf->writeHTML('<div style="text-align:center;"><u>DECLARATION OF SERVICE OF 30-DAY NOTICE TO PAY RENT OR QUIT</u></div>');
          $pdf->Ln(4);
          $pdf->SetFont('times', '', 9);
          $pdf->Write(0, 'On  ');
          $pdf->writeHTML("<u> " . $row['dos'] . " </u>, I served a 30 days Notice to Pay or Quit to the following address in the following manner:");
          $pdf->Ln(2);

          $pdf->SetLeftMargin(62);
          $pdf->writeHTML("&nbsp;&nbsp;<u>" . $row['tenant_address'] . ", &nbsp;" .$model->TENANT_CITY_STATE_ZIP."</u>");
          $pdf->Ln(2);
          $pdf->SetFont('times', '', 8);
          $pdf->Ln(1);

          $pdf->SetLeftMargin(18);
          $pdf->Ln(2);
          $pdf->Image('@' . base64_decode($row['c3'] ? $this->check : $this->clear), '', '', 4, 5, 'PNG', '');
          $pdf->SetX(25);
          $pdf->SetLeftMargin(25);
          $pdf->writeHTML("Because neither Tenant nor a person of suitable age or discretion could be found there, by posting a copy in a conspicuous place on the property, AND mailing a copy to the Tenant(s) at the place the property is situated.");
          $pdf->SetLeftMargin(18);
          $pdf->Ln(3);
          $pdf->SetLeftMargin(25);
          $pdf->writeHTML("<i>I declare under penalty of perjury under the laws of the State of Nevada that the foregoing is true and correct.
          </i>");
          $pdf->Ln(3);

          $pdf->SetFont('times', 'U', 8);
          $pdf->Cell(40, 5, '_____' . $row['dos'] . '_____', 0, false, 'L', false);
          $pdf->Cell(45, 5, '_____' . $row['server_name']. '_____', 0, false, 'C', false);
          $pdf->Cell(45, 5, '__________', 0, false, 'C', false);
          $pdf->Cell(45, 5, '_____' . $row['server_badge']. '_____', 0, false, 'C', false);

          $pdf->Ln(3);
          $pdf->SetFont('times', 'I', 8);
          $pdf->Cell(25, 5, '(DATE)', 0, 0, 'C');
          $pdf->SetX($pdf->GetX() + 15);
          $pdf->Cell(45, 5, "(SERVER’S NAME)", 0, false, 'C', false);
          $pdf->Cell(45, 5, '(SERVER’ SIGNATURE)', '', false, 'C', false);
          $pdf->setX($pdf->GetX() + 11);
          $pdf->writeHTML('(BADGE/LICENCE <sup>3</sup>)');
          $pdf->Ln(4);

          $pdf->SetFont('times', '', 9);
          $pdf->writeHTML("<br>Because of the global COVID-19 pandemic, you may be eligible for temporary protection from eviction under the laws of your State, Territory, locality, or tribal area, or under Federal law. Learn the steps you should take now: Visit <a href='www.cfpb.gov/eviction'>www.cfpb.gov/eviction</a> or call a housing counselor at 1-800-569-4287.");

          $pdf->Ln(5);
          $pdf->SetFont('times', '', 6);
          $footer_notes = strtoupper("<sup>1</sup>When counting calendar days do not include the date of service, but do include Saturdays, Sundays, and legal holidays.<br><sup>2</sup>Las Vegas Justice Court closes at 5:30pm on Mon-Thurs and 4pm on Fridays.<br/><sup>3</sup>A server who does not have a badge/license number may be an agent of an attorney licensed in Nevada. Notices served by agents must include an attorney declaration as proof of service.");
          if($model->LOCATION == 2){
              $footer_notes = strtoupper("<sup>1</sup>HENDERSON Justice Court is open Monday-Thursday 8am-4:30pm.<br/><sup>2</sup>A server who does not have a badge/license number may be an agent of an attorney licensed in Nevada. Notices served by agents must include an attorney declaration as proof of service.");
          }

          if($model->LOCATION == 3){
              $footer_notes = strtoupper("<sup>1</sup>North Las Vegas Justice Court hours of operation are Monday-Thursday 8am - 4:30pm.<br/><sup>2</sup>A server who does not have a badge/license number may be an agent of an attorney licensed in Nevada. Notices served by agents must include an attorney declaration as proof of service.");
          }

          $pdf->writeHTML($footer_notes, 0);
          $path = public_path('pdf');
          File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
          $fileName = $this->getFileName($model->TENANT_1, $model->TENANT_1);
          if ($type == 'zip') {
              $pdf->Output("$path/{$fileName}.pdf", 'F');
          } else if ($type == 'output') {
              $pdf->Output("$fileName.pdf");
          } else {
              $pdf->Output("$fileName.pdf", 'D');
          }
      }

      private function removeZipFromAddress($address) {
          $zipcode = preg_match('/(\d{4,5})/', $address, $matches);
          if($zipcode){
              return trim(str_replace($matches[0], '', $address));
          }
          return trim($address);
      }
}
