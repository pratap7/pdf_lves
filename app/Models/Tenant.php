<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model {

    protected $table = 'tenants';
    protected $primaryKey = 'ID';
    protected $fillable = [
        'TENANT_1',
        'TENANT_2',
        'APARTMENT_UNIT',
        'LANDLORD_NAME',
        'TENANT_ADDRESS',
        'LANDLORD_ADDRESS',
        'TENANT_CITY_STATE_ZIP',
        'LANDLORD_CITY_STATE_ZIP',
        'LANDLORD_PHONE',
        'DATE_OF_SERVICE',
        'RENT_PERIOD_START_DATE',
        'RENT_PERIOD_END_DATE',
        'CURRENT_RENT_DUE',
        'LATE_FEES',
        'TOTAL_OWED',
        'CITY',
        'JUSTICE_COURT_ADDRESS',
        'DATE',
        'SERVER_NAME',
        'SERVER_BADGE',
        'BATCH_ID',
        'BATCH_CODE',
        'STATUS',
        'LOCATION'
    ];
}
