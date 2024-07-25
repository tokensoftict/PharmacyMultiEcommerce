<?php
namespace App\Traits;

use App\Classes\PermissionAttribute;
use App\Classes\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait DynamicDataTableFormModal
{
    use DynamicDataTableRowAction, DynamicDataTableToolBarButton;
    protected $cacheModel = null;

    public array $data;

    public array $actionPermission = [];

    public String $modalName = "";

    public array $formData = [];

    public String $modalTitle = "New";

    public String $saveButton = "Save";

    public $modelId;

    public array $newValidateRules;

    public array $updateValidateRules;


    protected function parseData($model)
    {
        foreach($this->formData as $key=>$value)
        {
            if($key === "password") {
                if(!empty($this->formData[$key])){
                    $this->formData[$key] = bcrypt($this->formData[$key]);
                    continue;
                }
                continue;
            }

            if($key === "username"){
                $this->formData[$key] = explode("@", $this->formData['email'])[0];
            }


            $this->formData[$key] = $this->formData[$key] === "" ? NULL : $this->formData[$key];

            if(isset($this->data[$key]['type']) && $this->data[$key]['type'] === 'hidden'){
                $this->formData[$key] = $this->data[$key]['value'];
            }
        }
        return $model;
    }


    public final function initControls()
    {
        $validateRules = [];
        foreach ($this->data as $key => $value) {

            if($value['type'] == "hidden"){
                $this->formData[$key] = $value['value'];
            }else {
                $this->formData[$key] = "";
            }
            if(isset($this->newValidateRules[$key])) {
                $validateRules['formData.' . $key] = $this->newValidateRules[$key];
            }

        }
        $this->updateValidateRules = $this->newValidateRules = $validateRules;

    }


    public final function customView(): string
    {
        return 'livewire.shared.modal';
    }

    protected function loadModel($id)
    {
        return $this->model::findorfail($id);
    }


    protected function checkEmailAndPasswordForUpdate($id) :void
    {
        if(array_key_exists('email', $this->formData)){
            $this->updateValidateRules['formData.email'] = "required|email|unique:users,email,".$id;
        }else{
            unset($this->updateValidateRules['formData.email']);
        }

        if(array_key_exists('password',$this->formData) && !empty($this->formData['password'])){
            $this->updateValidateRules['formData.password'] = "required|min:6|max:36";
        }else{
            unset($this->updateValidateRules['formData.password']);
        }
    }

    public final function update($id) : void
    {
        $this->checkEmailAndPasswordForUpdate($id);
        $this->validate($this->updateValidateRules);
        DB::transaction(function() use ($id){
            $model =  $this->loadModel($id);
            $model = $this->parseData($model);
            $model->update($this->formData);
            Cache::forget($model->getTable());
            if(method_exists($this, "onUpdate")) {
                $this->onUpdate($model);
            }
        });
        $this->refreshTable();
    }


    #[PermissionAttribute('Delete', 'Destroy', 'destroy')]
    public function destroy($id) :void
    {
        DB::transaction(function () use ($id){
            $model = $this->loadModel($id);
            if(method_exists($this, "onDestroy")) {
                $this->onDestroy($model);
            }
            $model->delete();
            Cache::forget($model->getTable());
        });
        $this->refreshTable();
    }

    #[PermissionAttribute('Create', 'Create', 'create')]
    public function create()
    {
        foreach($this->data as $key=>$value)
        {
            if($value['type'] == "hidden"){
                $this->formData[$key] = $value['value'];
                unset($this->data[$key]['editDisplay']);
            }else{
                $this->formData[$key] = "";
            }
        }

        $this->modalTitle = "New";

        $this->saveButton = "Save";

        $this->dispatch("openModal", ['clearField' => true]);
    }

    #[PermissionAttribute('Toggle', 'Toggle', 'toggle')]
    public function toggle($id) : void
    {
        DB::transaction(function () use ($id){
            $model = $this->model::find($id);
            $model->status = !$model->status;
            $model->save();
            if(method_exists($this, "onToggle")) {
                $this->onToggle($model);
            }
            Cache::forget($model->getTable());
        });
        $this->dispatch('$refresh');
    }

    public function save()
    {
        DB::transaction(function (){
            $this->validate($this->newValidateRules);
            $model = new $this->model();
            $this->parseData($model);
            $model = $this->model::create($this->formData);
            if(method_exists($this, "onCreate")) {
                $this->onCreate($model);
            }
            Cache::forget($model->getTable());
        });
        $this->refreshTable();
    }

    #[PermissionAttribute('Update', 'Update', 'update')]
    public function edit($id) : void
    {
        $this->modelId = $id;

        $this->modalTitle = $this->saveButton = "Update";

        $data = $this->loadModel($id);

        $this->formData = $data->toArray();

        foreach ($this->data as $key =>$data){

            if($data['type'] == "password"){
                $this->formData[$key] = "";
            }

            if($data['type'] == "datepicker"){
                $this->formData[$key] = (new Carbon($this->formData[$key]))->format("Y-m-d");
            }

            if($data['type'] == "hidden"){
                if(isset($this->data[$key]['editCallback'])){
                    $editFunction = $this->data[$key]['editCallback'];
                    $this->data[$key]['editDisplay'] = $this->$editFunction($this->formData[$key]);
                }else {
                    $this->data[$key]['editDisplay'] = $this->formData[$key];
                }
            }
        }

        $this->dispatch("openModal", []);
    }



    public final function refreshTable() : void
    {
        $this->dispatch('closeModal');
        $this->dispatch('$refresh');
    }


}
