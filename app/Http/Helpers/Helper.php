<?php


use App\Classes\ApplicationEnvironment;
use App\Classes\Settings;
use App\Models\PushNotification;
use App\Models\SalesRepresentative;
use App\Notifications\DevicePushNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;
use LivewireFilemanager\Filemanager\Models\Folder;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
use Spatie\Valuestore\Valuestore;

function _GET($url, $payload = []) : array|bool
{
    $response = Http::timeout(10000)->get($url);
    if($response->status() == 200 )
    {
        return json_decode($response->body(), true) ??  true;
    }
    return false;
}

function _FETCH($url) : array|bool
{
    $response = Http::timeout(10000)->get($url);

    if($response->status() == 200 )
    {
        return json_decode($response->body(), true) ??  true;
    }
    return false;
}

function _POST($url, $payload = []) : array|bool
{
    $response =   Http::timeout(10000)->post($url, $payload);

    if($response->status() == 200 )
    {
        return json_decode($response->body(), true) ??  true;
    }

    Storage::disk('local')->append('bulk-logs', $response->body(), null);

    return false;
}

if (!function_exists('isJson')) {
    function isJson($string)
    {
        if(is_array($string) || is_object($string)) return true;
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

function generateRandom($length = 25) {
    $characters = 'abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function str_plural($name){
    return Str::plural($name);
}
function getStoreSettings(){

    return json_decode(json_encode(Valuestore::make(storage_path('app/settings.json'))->all()));
}
function month_year($time = false, $pad = false)
{
    if (!$time) $time = time() + time_offset();
    else $time = strtotime($time);
    if ($pad) $pad = ". h:i:s A";
    else $pad = "";
    return date('F, Y' . $pad, $time);
}

function eng_str_date($time = false, $pad = false)
{
    if (!$time) $time = time() + time_offset();
    else $time = strtotime($time);
    if ($pad) $pad = ". h:i:s A";
    else $pad = "";
    return date('d/m/Y' . $pad, $time);
}
function human_date($date){
    return (new Carbon($date))->format('F jS, Y');
}
function twentyfourHourClock($time)
{
    return  date('H:i', strtotime($time));
}
function twelveHourClock($time)
{
    return  date('h:i A', strtotime($time));
}
function mysql_str_date($time = false, $pad = false)
{
    if (!$time) $time = time() + time_offset();
    else $time = strtotime($time);
    if ($pad) $pad = ". h:i:s A";
    else $pad = "";
    return date('Y-m-d' . $pad, $time);
}
function str_date($time = false, $pad = false)
{
    if (!$time) $time = time() + time_offset();
    else $time = strtotime($time);
    if ($pad) $pad = ". h:i:s A";
    else $pad = "";
    return date('l, F jS, Y' . $pad, $time);
}
function str_date2($time = false, $pad = false)
{
    if (!$time) $time = time() + time_offset();
    else $time = strtotime($time);
    if ($pad) $pad = ". h:i:s A";
    else $pad = "";
    return date('D, F jS, Y' . $pad, $time);
}
function format_date($date, $withTime = TRUE)
{
    if ($date == "0000-00-00 00:00:00") {
        return "Never";
    }

    $date = trim($date);
    $retVal = "";
    $date_time_array = explode(" ", $date);
    $time = $date_time_array[1];
    $time_array = explode(":", $time);

    $date_array = explode("-", "$date");
    $day = $date_array['2'];
    $month = $date_array['1'];
    $year = $date_array['0'];
    if ($year > 0) {
        @ $ddate = mktime(12, 12, 12, $month, $day, $year);
        @ $retVal = date("j M Y", $ddate);
    }

    if (!empty($time)) {
        $hr = $time_array[0];
        $min = $time_array[1];
        $sec = $time_array[2];
        @ $ddate = mktime($hr, $min, $sec, $month, $day, $year);
        @ $retVal = date("j M Y, H:i", $ddate);
        if (!$withTime) {
            @ $retVal = date("j M Y", $ddate);
        }
    }

    return $retVal;
}
function restructureDate($date_string)
{
    if (strtotime($date_string)) return $date_string;

    if (str_contains($date_string, "/")) {
        if (strtotime(str_replace("/", "-", $date_string))) return str_replace("/", "-", $date_string);

        // TODO: try to change the date format to make it easier for the system to parse
    }

    return $date_string;
}
function render($type = "append")
{
    echo "@render:$type=out>>";
}
function normal_case($str)
{
    return ucwords(str_replace("_", " ", Str::snake(str_replace("App\\", "", $str))));
}
function alert_success($msg)
{
    return alert('success', $msg);
}
function alert_info($msg)
{
    return alert('info', $msg);
}
function alert_warning($msg)
{
    return alert('warning', $msg);
}
function error($msg) : string
{
    return '<span class="text-danger d-block">'.$msg.'</span>';
}
function alert_error($msg)
{
    return alert('danger', $msg);
}
function alert($status, $msg)
{
    return '<div class="alert alert-' . $status . '">' . $msg . '</div>';
}
function money($amt)
{
    return number_format($amt, 2);
}
function formatNumber($amt)
{
    return number_format($amt);
}
function toCap($string)
{
    return strtoupper(strtolower($string));
}
function toSmall($string)
{
    return strtolower($string);
}
function toSentence($string)
{
    return ucwords(strtolower($string));
}
function generateRandomString($randStringLength)
{
    $timestring = microtime();
    $secondsSinceEpoch = (integer)substr($timestring, strrpos($timestring, " "), 100);
    $microseconds = (double)$timestring;
    $seed = mt_rand(0, 1000000000) + 10000000 * $microseconds + $secondsSinceEpoch;
    mt_srand($seed);
    $randstring = "";
    for ($i = 0; $i < $randStringLength; $i++) {
        $randstring .= mt_rand(0, 9);
    }
    return ($randstring);
}
function getRandomString_AlphaNum($length)
{
    //Init the pool of characters by category
    $pool[0] = "ABCDEFGHJKLMNPQRSTUVWXYZ";
    $pool[1] = "23456789";
    return randomString_Generator($length, $pool);
}   //END getRandomString_AlphaNum()
function randomString_Num($length)
{
    //Init the pool of characters by category
    $pool[0] = "0123456789";
    return randomString_Generator($length, $pool);
}
function getRandomString_AlphaNumSigns($length)
{
    //Init the pool of characters by category
    $pool[0] = "ABCDEFGHJKLMNPQRSTUVWXYZ";
    $pool[1] = "abcdefghjkmnpqrstuvwxyz";
    $pool[2] = "23456789";
    $pool[3] = "-_";
    return randomString_Generator($length, $pool);
}

function random_text($length)
{
    $pool[0] = "abcdefghjkmnpqrstuvwxyz";
    // $pool[1] = "-_";
    return randomString_Generator($length, $pool);
}

function randomString_Generator($length, $pools)
{
    $highest_pool_index = count($pools) - 1;
    //Now generate the string
    $finalResult = "";
    $length = abs((int)$length);
    for ($counter = 0; $counter < $length; $counter++) {
        $whichPool = rand(0, $highest_pool_index);    //Randomly select the pool to use
        $maxPos = strlen($pools[$whichPool]) - 1;    //Get the max number of characters in the pool to be used
        $finalResult .= $pools[$whichPool][mt_rand(0, $maxPos)];
    }
    return $finalResult;
}
function softwareStampWithDate($width = "100px") {
    return "<br>
    Generated @". date('Y-m-d H:i A') ;
}

function split_name($name)
{
    $name = trim($name);
    $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
    $first_name = trim(preg_replace('#' . $last_name . '#', '', $name));
    return array("firstname" => $first_name, "lastname" => $last_name);
}
function convert_date($date){
    return date('D, F jS, Y', strtotime($date));
}
function convert_date_with_time($date){
    return date('D, F jS, Y h:i a', strtotime($date));
}
function convert_date2($date){
    return date('Y/m/d', strtotime($date));
}


function todaysDate(){
    return date('Y-m-d');
}

function yesterdayDate(){
    return date('Y-m-d',strtotime('yesterday'));
}

function weeklyDateRange(){
    $dt = strtotime (date('Y-m-d'));
    $range =  array (
        date ('N', $dt) == 1 ? date ('Y-m-d', $dt) : date ('Y-m-d', strtotime ('last monday', $dt)),
        date('N', $dt) == 7 ? date ('Y-m-d', $dt) : date ('Y-m-d', strtotime ('next sunday', $dt))
    );

    return $range;
}

function monthlyDateRange(){
    $dt = strtotime (date('Y-m-d'));
    $range =  array (
        date ('Y-m-d', strtotime ('first day of this month', $dt)),
        date ('Y-m-d', strtotime ('last day of this month', $dt))
    );
    return $range;
}


function getUserMenu2($app_id)
{
    if(!in_array($app_id, [2,3]))  return "";
    $menus = loadMenu($app_id);
    $userMenus = '<li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false"><span class="uil fs-8 me-2 uil-chart-pie"></span>Home</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li>
                            <a class="dropdown-item ' . ('admin.dashboard' === \Route::currentRouteName() ? 'active' : '') . '" href="'.route(ApplicationEnvironment::$storePrefix."admin.dashboard").'">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil" data-feather="shopping-cart"></span>Dashboard</div>
                            </a>
                        </li>
                    </ul>
                </li>';
    $num = 1;
    $other = false;
    foreach ($menus as $index => $menu) {
        if($num > 3) {
            if($other === false) {
                $userMenus .= '<li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                            <span class="uil fs-8 me-2 uil-cube"></span>Other Modules</a>';
                $userMenus .= '<ul class="dropdown-menu navbar-dropdown-caret">';
                $userMenus.='';
                $other = true;
            }
            if (accessToModule($menu->id)) {
                $userMenus.='<li class="dropdown dropdown-inside">
                            <a class="dropdown-item dropdown-toggle" id="customization" href="#" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                    <div class="dropdown-item-wrapper">
                    <span class="uil fs-8 uil-angle-right lh-1 dropdown-indicator-icon"></span>
                    <span>
                    <span class="me-2 uil" data-feather="settings"></span>
                    ' . toSentence($menu->label) . '
                    </span>
                    </div></a> <ul class="dropdown-menu">';
                foreach ($menu->permissions as $link) {
                    if ($link->visibility == "1") {
                        $userMenus.= '<li><a class="dropdown-item ' . (ApplicationEnvironment::$storePrefix . $link->name === \Route::currentRouteName() ? 'active' : '') . '" href="' . route(ApplicationEnvironment::$storePrefix . $link->name) . '">
                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>' . Str::plural($link->label) . '</div>
                      </a></li>';
                    }
                }
                $userMenus.= '</ul></li>';
            }

           if(($menus->count() - 1) === $index) {
               $userMenus .= '';
               $userMenus .= '</ul>';
               $userMenus .= '</li>';
           }

        }else {
            if (accessToModule($menu->id)) {
                $userMenus .= '<li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle lh-1" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                            <span class="uil fs-8 me-2 uil-cube"></span>' . toSentence($menu->label) . '</a>';
                $userMenus .= '<ul class="dropdown-menu navbar-dropdown-caret">';
                foreach ($menu->permissions as $link) {
                    if ($link->visibility == "1") {
                        $userMenus .= '<li>
                                    <a class="dropdown-item ' . (ApplicationEnvironment::$storePrefix . $link->name === \Route::currentRouteName() ? 'active' : '') . '" href="' . route(ApplicationEnvironment::$storePrefix . $link->name) . '">
                                        <div class="dropdown-item-wrapper"><span class="me-2 uil"></span>' . Str::plural($link->label) . '</div>
                                    </a>
                                </li>';
                    }
                }
                $userMenus .= '</ul>';
                $userMenus .= '</li>';
            }
            $num++;
        }
    }
    return $userMenus;
}


function getUserMenu($app_id)
{
    $menus = loadMenu($app_id);
    $userMenus = '<a class="nav-link ' . ('admin.dashboard' === \Route::currentRouteName() ? 'active' : '') . '" href="'.route(ApplicationEnvironment::$storePrefix."admin.dashboard").'" role="button" data-bs-toggle="" aria-expanded="false"><div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="home"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">Dashboard</span></span></div></a>';

    foreach ($menus as $menu){
        if(accessToModule($menu->id)){
            $userMenus.='<p class="navbar-vertical-label">'.Str::upper($menu->label) .'</p>';
            foreach ($menu->permissions as $link){
                if($link->visibility == "1"){
                    $userMenus.='<a class="nav-link ' . (ApplicationEnvironment::$storePrefix.$link->name === \Route::currentRouteName() ? 'active' : '') . '" href="'.route(ApplicationEnvironment::$storePrefix.$link->name).'" role="button" data-bs-toggle="" aria-expanded="false"><div class="d-flex align-items-center"><span class="nav-link-icon"><span data-feather="home"></span></span><span class="nav-link-text-wrapper"><span class="nav-link-text">'.Str::plural($link->label).'</span></span></div></a>';
                }
            }
        }
    }

    return $userMenus;
}

/*
function getUserMenu()
{
    $groupMenu = loadUserMenu();

    $userMenus = '<li class="nav-item mb-1"><a wire:navigate class="nav-link '.("dashboard" === \Route::currentRouteName() ? 'active' : '').'" href="' . route("dashboard") . '"><i data-feather="box"></i> Dashboard</a></li>';

    if ($groupMenu) {
        $lastModule = '';
        $isFirstRun = true;

        foreach ($groupMenu as $menu) {
            if(in_array($menu->module_id, Settings::$reports)) continue;

            if(!canModuleDisplay($menu->module_id)) continue;

            if ($lastModule != $menu->module_id) {
                if ($lastModule != '' && !$isFirstRun) {
                    $userMenus .= '</nav></li>';
                }
                $isFirstRun = false;
                $userMenus .= '<li class="nav-item'.(str_contains(request()->route()->getPrefix(), strtolower($menu->module->name)) ? ' show' : '').'"><a '.(str_contains(request()->route()->getPrefix(), strtolower($menu->module->name)) ? 'class="nav-link with-sub active"' : 'class="nav-link with-sub"').' href="">
                <i data-feather="'.$menu->module->icon.'"></i>
                 '.$menu->module->label.'
                </a><nav class="nav nav-sub">';
            }
            if ($menu->visibility) $userMenus .= '<a wire:navigate class="'.($menu->route === \Route::currentRouteName() ? 'nav-sub-link active' : 'nav-sub-link').'" href="' . route($menu->route) . '">' . $menu->name . '</a>';
            $lastModule = $menu->module_id;
        }

        if (!$isFirstRun) {
            $userMenus .= '</nav></li>';
        }

    }

    return $userMenus;
}
*/

function computeUserMenu()
{
    $menus = loadMenu();
    $usermenu = '<li class="nav-label">Dashboard</li>';
    $usermenu.= '<li class="nav-item '.("backend.admin.dashboard" === \Route::currentRouteName() ? 'active' : ''). '"><a wire:navigate href="'.route(ApplicationEnvironment::$storePrefix.'backend.admin.dashboard').'" class="nav-link"><i data-feather="shopping-bag"></i> <span>Sales Dashboard</span></a></li>';

    foreach ($menus as $menu)
    {
        if(accessToModule($menu->id)) {
            $usermenu .= '<li class="nav-label mg-t-25">' . Str::upper($menu->label) . '</li>';
            foreach ($menu->permissions as $link) {
                if ($link->visibility == "1") {
                    $usermenu .= '<li class="nav-item ' . ($link->name === \Route::currentRouteName() ? 'active' : '') . '"><a href="' . route(ApplicationEnvironment::$storePrefix.$link->name) . '" class="nav-link"><i data-feather="globe"></i> <span>' . Str::plural($link->label) . '</span></a></li>';
                }
            }
        }
    }

    return $usermenu;
}

function loadMenu($app_id, $refresh = false)
{
    if($refresh === true) Cache::forget('module-with-permission-list-'.$app_id);
    return Cache::remember('module-with-permission-list-'.$app_id,86400, function() use($app_id){
        return \App\Models\Module::with(['permissions'=>function($query) use ($app_id){
            $query->whereHas('apps',function($query) use ($app_id){
                $query->where('app_id', $app_id);
            });
        }])->whereHas('apps', function ($query) use ($app_id){
            $query->where('app_id', $app_id);
        })->get();
    });
}

function permissions($app_id, $refresh = false)
{
    if($refresh === true) Cache::forget('permission-list-'.$app_id);
    return Cache::remember('permission-list-'.$app_id, 86400, function () use($app_id){
        return \App\Models\Permission::query()->whereHas('apps', function ($query) use ($app_id){
            $query->where('app_id', $app_id);
        });
    });
}

function banks()
{
    return Cache::remember('banks', 85400, function(){
        return DB::table('banks')->get();
    });
}

function states()
{
    return Cache::remember('states', 85400, function(){
        return DB::table('states')->get();
    });
}


function customerTypes()
{
    return  Cache::remember('customer_types', 85400, function(){
        return DB::table('customer_types')->get();
    });
}


function customerGroups()
{
    return  Cache::remember('customer_groups', 85400, function(){
        return DB::table('customer_groups')->get();
    });
}

function statesByCountry($country_id)
{
    return states()->filter(function($state) use ($country_id){
        return $state->country_id == $country_id;
    });
}

function countries()
{
    return Cache::remember('countries', 85400, function(){
        return DB::table('countries')->get();
    });
}


function productcategories()
{
    return Cache::remember('productcategories', 85400, function(){
        return DB::table('productcategories')->get();
    });
}

function towns()
{
    return Cache::remember('towns', 85400, function(){
        return DB::table('towns')->get();
    });
}

function classifications()
{
    return Cache::remember('classifications', 85400, function(){
        return DB::table('classifications')->get();
    });
}

function manufacturers()
{
    return Cache::remember('manufacturers', 85400, function(){
        return DB::table('manufacturers')->get();
    });
}


function productgroups()
{
    return Cache::remember('productgroups', 85400, function(){
        return DB::table('productgroups')->get();
    });
}


function userCanView($permission)
{
    return request()->user()->can($permission);
}

function accessToModule($module_id)
{
    if(!request()->user()) {
        return false;
    }
    if(request()->user()->hasAnyRole('Super Administrator')) return true;

    return  permissions(\App\Classes\ApplicationEnvironment::$id)->contains(function ($permission) use($module_id){
        return ($permission->module_id === $module_id && $permission->visibility == '1' && auth()->user()->can($permission->name));
    });
}

function backendAsset($folder, $asset = ""){
    return asset("backend/".$folder."/".$asset);
}
function frontendAsset($folder, $asset = ""){
    return asset("frontend/".$folder."/".$asset);
}

function adminAssets($asset){
    return backendAsset('admin', $asset);
}
function superMarketAssets($asset){
    return frontendAsset('supermarket', $asset);
}
function wholeSalesAssets($asset){
    return frontendAsset('wholesales', $asset);
}

function genRedirectURL($url)
{
    return config('app.AUTH_DOMAIN') === "auth.generaldrugcentre.com" ? "https://".$url : "http://".$url.":8000";
}

function getCurrentDomain()
{
    $domain = url('');
    $domain = str_replace('https://', '', $domain);
    $domain = str_replace("http://", '', $domain);

    return $domain;
}

function getCurrentLabel($options, $value) : string
{
    foreach ($options as $option)
    {
        if($option['id'] == $value) return  $option['text'];
    }
    return "";
}

function label($text, $type = 'default', $extra = 'sm')
{
    return '<span class="badge badge-phoenix fs-10 badge-phoenix-'.$type.'"><span class="badge-label">'.$text.'</span>'.
        (match ($type){
            'default', 'secondary' => '<span class="ms-1" data-feather="plus" style="height:12.8px;width:12.8px;"></span>',
            'primary' => '<span class="ms-1" data-feather="package" style="height:12.8px;width:12.8px;"></span>',
            'success' => '<span class="ms-1" data-feather="check" style="height:12.8px;width:12.8px;"></span>',
            'info' => '<span class="ms-1" data-feather="info" style="height:12.8px;width:12.8px;"></span>',
            'warning' => '<span class="ms-1" data-feather="alert-octagon" style="height:12.8px;width:12.8px;"></span>',
            'danger', 'error' => '<span class="ms-1" data-feather="x" style="height:12.8px;width:12.8px;"></span>',
            default => '<span class="ms-1" data-feather="plus" style="height:12.8px;width:12.8px;"></span>'
        })
        .'</span>';
}


function status($status){
    if(is_numeric($status))

        $st = statuses()->filter(function ($item) use($status){
            return $item->id == $status;
        });
    else

        $st = statuses()->filter(function ($item) use($status){
            return $item->name == $status;
        });


    return $st->first()->id ?? 0;
}

function status_name($status){
    if(is_numeric($status))

        $st = statuses()->filter(function ($item) use($status){
            return $item->id == $status;
        });
    else

        $st = statuses()->filter(function ($item) use($status){
            return $item->name == $status;
        });


    return $st->first()->name;
}

function showStatus($status)
{
    if(is_numeric($status))
        $st = statuses()->filter(function ($item) use($status){
            return $item->id == $status;
        });
    elseif(is_object($status))
        $st = statuses()->filter(function ($item) use($status){
            return $item->id == $status->id;
        });
    else
        $st = statuses()->filter(function ($item) use($status){
            return $item->name == $status;
        });

    $st = $st->first();

    if(!$st) return label($status);

    return label($st->name,$st->label);
}


function statuses()
{
    return Cache::remember('status',144000,function(){
        return \App\Models\Status::all();
    });
}


function getApplicationModel()
{
    return ApplicationEnvironment::getApplicationRelatedModel() ?? false;
}


function getAvailablePaymentOption($deliveryCode)
{
    $paymentOptions = [
        "Pickup" => [
            "Bank",
            "Pat",
            "Paystack",
            "Flutterwave"
        ],
        "Dwi" => [
            "Bank",
            "Paystack",
            "Flutterwave"
        ],
        "Doi" => [
            "Bank",
            "Paystack",
            "Flutterwave"
        ],
        "Dsd" => [
            "Bank",
            "Paystack",
            "Flutterwave"
        ]
    ];

    return $paymentOptions[$deliveryCode];
}

function generateUniqueNumber() {
    do {
        $number = '';
        while (strlen($number) < 10) {
            $randomBytes = random_int(0, 9); // Generates a cryptographically secure random digit
            $number .= $randomBytes;
        }
    } while (\App\Models\Order::where('invoice_no', $number)->exists());

    return $number;
}

function generateUniqueid($length = 10) {
    do {
        $characters = 'ABCDEFGHIJKLMNO0123456789PQRSTUVWXYZ';
        $string = '';

        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[random_int(0, strlen($characters) - 1)];
        }

        $order = \App\Models\Order::where('order_id', $string)->exists();
        $orderproduct = \App\Models\OrderProduct::where('order_product_id', $string)->exists();

    } while ($order || $orderproduct);

    return $string;
}

function carbonize($date) : Carbon {
    return (new Carbon($date));
}

function futureCarbon(int $number) : Carbon {
    return Carbon::now()->addMinute($number);
}

function sendNotificationToDevice(PushNotification $notification) : void
{
    foreach ($notification->push_notification_customers()->where('status_id', status('Pending'))->get() as $customer) {
        $customer->status_id = status('Dispatched');
        $customer->save();
        $customer->customer->notify(new DevicePushNotification($notification, $customer));
    }
}

function stock_image_folder()
{
    $stockImageFolder = Folder::where("slug", "stocks")->first();
    if(!$stockImageFolder){
        $stockImageFolder = new Folder();
        $stockImageFolder->parent_id = 1;
        $stockImageFolder->slug = "stocks";
        $stockImageFolder->name = "stocks";
        $stockImageFolder->save();
    }

    return $stockImageFolder;
}

function business_certificate()
{
    $business_certificate =  Folder::where("slug", "business-certificate")->first();
    if(!$business_certificate) {
        $business_certificate = new Folder();
        $business_certificate->parent_id = 1;
        $business_certificate->slug = "business-certificate";
        $business_certificate->name = "Business Certificate";
        $business_certificate->save();
    }
    return $business_certificate;
}

function premises_licence()
{
    $premises_licence =  Folder::where("slug", "business-premises-license")->first();
    if(!$premises_licence) {
        $premises_licence = new Folder();
        $premises_licence->parent_id = 1;
        $premises_licence->slug = "business-premises-license";
        $premises_licence->name = "Business Premises License";
        $premises_licence->save();
    }
    return $premises_licence;
}

function generateUniqueReferralCode()
{
    do {
        $code = 'SR' . strtoupper(Str::random(6));
    } while (SalesRepresentative::where('code', $code)->exists());

    return $code;
}

function proof_of_payment()
{
    $proofOfPayment = Folder::where("slug", "proof-of-payment")->first();
    if(!$proofOfPayment){
        $proofOfPayment = new Folder();
        $proofOfPayment->parent_id = 1;
        $proofOfPayment->slug = "proof-of-payment";
        $proofOfPayment->name = "Proof of Payment";
        $proofOfPayment->save();
    }

    return $proofOfPayment;
}

function publishToKafka($topic, $action, $message)
{
    $message = new Message(
        headers: ['AUTH' => bcrypt(config("app.KAFKA_HEADER_KEY"))],
        body: ["action" => $action, "data" => $message],
    );

    try {
        Kafka::publish()->onTopic($topic)->withMessage($message)->send();
    } catch (Exception $e) {
        Log::error($e);
    }
}


function normalizePhoneNumber($phone)
{
    // Remove spaces, dashes, or other non-numeric characters if needed
    $phone = preg_replace('/\D+/', '', $phone);

    // Replace +234 or 234 at the beginning with 0
    return preg_replace('/^(234|\+234)/', '0', $phone);
}