<?php

Route::middleware(['auth:sanctum'])->group(function(){
    Route::prefix("med-reminder")->namespace("MedReminder")->group(function(){
        Route::get("/list", ["as" => "med-reminder.list", "uses" => "ListReminderController"]);
        Route::post("/create", ["as" => "med-reminder.create", "uses" => "CreateReminderController"]);
        Route::post("{medReminder}/update", ["as" => "med-reminder.update", "uses" => "UpdateReminderController"]);
        Route::get("{medReminder}/remove", ["as" => "med-reminder.remove", "uses" => "RemoveReminderController"]);
        Route::get("today-history", ["as" => "med-reminder.today-history", "uses" => "ListAllSchedulesController"]);
    });
});
