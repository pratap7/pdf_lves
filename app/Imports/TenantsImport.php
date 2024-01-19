<?php

namespace App\Imports;

use App\Models\Tenant;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class TenantsImport implements ToModel, WithCalculatedFormulas{

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function model(array $row) {
        ini_set('memory_limit', '-1');
        if ($row[3] == 'Unit #' || empty($row[0])) {
            return null;
        }
        $total_owed = (!empty($row[14])) ? trim($row[14], "$") : 0;
		$current_rent_due = (!empty($row[12])) ? trim($row[12], "$") : 0;
        $late_fee = (!empty($row[13])) ? trim($row[13], "$") : 0;
        $late_fee = round($late_fee,2);

        return new Tenant([
            'TENANT_1' => isset($row[0]) ? $row[0] : '',
            'TENANT_2'    => isset($row[1]) ? $row[1] : '',
            'LANDLORD_NAME' => isset($row[2]) ? $row[2] : '',
            'APARTMENT_UNIT' => isset($row[3]) ? $row[3] : '',
            'TENANT_ADDRESS' => isset($row[4]) ? $row[4] : '',
            'LANDLORD_ADDRESS' => isset($row[5]) ? $row[5] : '',
            'TENANT_CITY_STATE_ZIP' => isset($row[6]) ? $row[6] : '',
            'LANDLORD_CITY_STATE_ZIP' => isset($row[7]) ? $row[7] : '',
            'LANDLORD_PHONE' => isset($row[8]) ? $row[8] : '',
            'DATE_OF_SERVICE' => (isset($row[9]) && !empty($row[9])) ? $row[9] : '00/00/00',
            'RENT_PERIOD_START_DATE' => (isset($row[10]) && !empty($row[10])) ? $row[10] : '00/00/00',
            'RENT_PERIOD_END_DATE' => (isset($row[11]) && !empty($row[11])) ? $row[11] : '00/00/00',
            'CURRENT_RENT_DUE' => isset($row[12]) ? $row[12] : '',
            'CITY' => isset($row[15]) ? $row[15] : '',
            'JUSTICE_COURT_ADDRESS' => isset($row[16]) ? $row[16] : '',
            'LATE_FEES' => $late_fee,
            'TOTAL_OWED' => $total_owed,
            'DATE' => (isset($row[17]) && !empty($row[17])) ? $row[17] : '00/00/00',
            'SERVER_NAME' => isset($row[18]) ? $row[18] : '',
            'SERVER_BADGE' => isset($row[19]) ? $row[19] : '',
            'BATCH_CODE' => request()->input('batch_code'),
            'LOCATION' => request()->input('location'),
            'BATCH_ID' => time()
        ]);
    }
}
