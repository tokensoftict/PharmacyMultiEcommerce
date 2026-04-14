<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Classes\ApplicationEnvironment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentMethod
 * 
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int|null $app_id
 * @property string $path
 * @property string $code
 * @property int $status
 * @property string|null $template_settings
 * @property array|null $template_settings_value
 * @property string|null $checkout_template
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property App|null $app
 * @property Collection|Order[] $orders
 *
 * @package App\Models
 */
class PaymentMethod extends Model
{
	protected $table = 'payment_methods';

	protected $casts = [
		'app_id' => 'int',
		'status' => 'int',
		'template_settings_value' => 'json'
	];

	protected $fillable = [
		'name',
		'description',
		'app_id',
		'path',
		'code',
		'status',
		'template_settings',
		'template_settings_value',
		'checkout_template'
	];

    protected $appends = ['bank_list'];
	public function app()
	{
		return $this->belongsTo(App::class);
	}

	public function orders()
	{
		return $this->hasMany(Order::class);
	}
    public function isDefault()
    {
        if(!isset( ApplicationEnvironment::getApplicationRelatedModel()?->payment_method_id)) return false;
        return $this->id  === ApplicationEnvironment::getApplicationRelatedModel()?->payment_method_id;
    }


    public function getBankListAttribute()
    {
        if($this->code == "Bank") {
            return BankAccount::query()->where('status', 1)->with('bank')->whereIn('id', $this->template_settings_value)->get()->map(function ($bank) {
                return [
                    'id' => $bank->id,
                    'name' => $bank->bank->name,
                    'account_name' => $bank->account_name,
                    'account_number' => $bank->account_number,
                ];
            });
        } else {
            return [];
        }
    }

    public function getDescriptionAttribute()
    {
        if($this->code == "Bank")  return $this->attributes['description']. $this->appendBankTransferAccount();
        return $this->attributes['description'];
    }


    public function appendBankTransferAccount()
    {
        if($this->id == "1") {
            $settingsValue = json_decode('[{"bank": "PS GENERAL DRUGS", "name": "STERLING BANK", "number": "0079729045"}]',true);
        } else {
            $settingsValue = json_decode('[{"bank": "PS GENERAL DRUGS", "name": "GTBank", "number": "0736145025"}]', true);
        }
        $html = "";
        foreach($settingsValue as $setting) {
            $html.="<p><b>Bank Name</b> : ".$setting['bank']."</p>";
            $html.="<p><b>Account Name</b> : ".$setting['name']."</p>";
            $html.="<p><b>Account Number</b> : ".$setting['number']."</p>";
        }

        return $html;
    }
}
