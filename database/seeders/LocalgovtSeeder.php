<?php

namespace Database\Seeders;

use App\Models\LocalGovt;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LocalgovtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function(){
            $states = File::get(storage_path('app/state.json'));
            $states = collect(json_decode(json: $states, associative: true));

            $lgas = File::get(storage_path('app/lga.json'));
            $lgas = collect(json_decode(json: $lgas, associative: true));

            $states->each(function($state) use ($lgas){
                $_state = str($state['name'])->replace("State", "")->squish();

                $dbState = State::where('name', 'LIKE', "%{$_state}%")->first();
                if(!$dbState) dd('cant find '.$_state);

                $filterLGA = $lgas->filter(function($lga) use ($state){
                    return $lga['state_id'] == $state['id'];
                });

                $filterLGA->each(function($lgafilter) use ($dbState){
                    LocalGovt::create(
                        [
                            'state_id' => $dbState->id,
                            'name' => $lgafilter['local_name']
                        ]
                    );
                });

            });
        });
    }
}
