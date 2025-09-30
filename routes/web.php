<?php

// --- HOME & STATIC PAGES ---
$router->get('', 'HomeController@index');
$router->get('/', 'HomeController@index');
$router->get('home', 'HomeController@index');
$router->get('home/index', 'HomeController@index');
$router->get('login', 'HomeController@login');
$router->get('signup', 'HomeController@signup');
$router->get('dashboard', 'DashboardController@index');

// --- MOOD TRACKER ---  
$router->get('myMood', 'MoodTrackerController@myMood');
$router->post('moodlog/log', 'MoodLogController@log');

// --- WATER TRACKER ---
$router->get('waterIntake', 'WaterTrackerController@waterIntake');
$router->get('waterIntake/add', 'WaterTrackerController@addWaterIntake');
$router->get('waterIntake/edit', 'WaterTrackerController@editWaterIntake');
$router->get('waterIntake/view', 'WaterTrackerController@viewWaterIntake');

// --- BMI TRACKER ---
$router->get('bmiTracker', 'BmiTrackerController@bmiTracker');
$router->get('bmiTracker/add', 'BmiTrackerController@addBmi');
$router->get('bmiTracker/edit', 'BmiTrackerController@editBmi');
$router->get('bmiTracker/view', 'BmiTrackerController@viewBmi');
// --- FOOD INTAKE TRACKER ---
$router->get('foodIntake', 'FoodIntakeController@foodIntake');
$router->get('foodIntake/add', 'FoodIntakeController@addFoodIntake');
$router->get('foodIntake/edit', 'FoodIntakeController@editFoodIntake');
$router->get('foodIntake/view', 'FoodIntakeController@viewFoodIntake');

// --- SLEEP TRACKER ---
$router->get('sleepTracker', 'SleepController@index');
$router->get('sleep', 'SleepController@index');
$router->post('sleep/log', 'SleepController@logSleep');
$router->get('sleep/stats', 'SleepController@getStats');
$router->post('sleep/delete', 'SleepController@deleteSleep');
$router->get('sleep/recommendation', 'SleepController@getRecommendation');

// Legacy routes for backward compatibility
$router->get('sleepTracker/add', 'SleepController@index');
$router->get('sleepTracker/edit', 'SleepController@index');
$router->get('sleepTracker/view', 'SleepController@index');

// --- NOTES ---
$router->get('notes', 'NotesController@notes');
$router->post('notes/create', 'NotesController@create');
$router->post('notes/updateStatus', 'NotesController@updateStatus');
$router->post('notes/delete', 'NotesController@delete');
$router->get('notes', 'NotesController@notes');
$router->get('notes/add', 'NotesController@addNote');
$router->get('notes/edit', 'NotesController@editNote');
$router->get('notes/view', 'NotesController@viewNote');

// --- SELF CARE ---
$router->get('self-care', 'CareController@selfcare');
$router->get('care', 'CareController@selfcare');
$router->get('self-care/add', 'CareController@addSelfCare');
$router->get('self-care/edit', 'CareController@editSelfCare');
$router->get('self-care/view', 'CareController@viewSelfCare');
$router->post('checklist/toggle', 'CareController@toggleCompletion');
$router->post('checklist/add', 'CareController@add');
$router->post('checklist/update', 'CareController@update');
$router->post('checklist/delete', 'CareController@delete');

// --- MENTAL HEALTH ASSESSMENT ---
$router->post('care/saveAssessment', 'CareController@saveAssessment');
$router->get('care/getLatestAssessment', 'CareController@getLatestAssessment');

// --- REPORTS ---
$router->get('reports', 'ReportController@report');
$router->get('report', 'ReportController@report');

// --- CHECKLIST ---
$router->get('checklist', 'ChecklistController@checklist');
$router->get('checklist/add', 'ChecklistController@addChecklist');
$router->get('checklist/edit', 'ChecklistController@editChecklist');
$router->get('checklist/view', 'ChecklistController@viewChecklist');
// --- DAILY QUOTES ---
$router->get('dailyQuotes', 'DailyQuotesController@dailyQuotes');
$router->get('dailyQuotes/add', 'DailyQuotesController@addQuote');
$router->get('dailyQuotes/edit', 'DailyQuotesController@editQuote');
$router->get('dailyQuotes/view', 'DailyQuotesController@viewQuote');

// --- PROFILE MANAGEMENT ---
$router->get('profile', 'ProfileController@index');
$router->post('profile/update', 'ProfileController@updateProfile');
$router->post('profile/update-password', 'ProfileController@updatePassword');
$router->post('profile/update-picture', 'ProfileController@updateProfilePicture');
$router->post('profile/add-bmi-log', 'ProfileController@addBmiLog');
$router->post('profile/update-bmi-log', 'ProfileController@updateBmiLog');
$router->post('profile/delete-bmi-log', 'ProfileController@deleteBmiLog');

// --- AUTHENTICATION (POST requests) ---
$router->post('handle_signup', 'AuthController@handleSignup');
$router->post('handle_login', 'AuthController@handleLogin');
$router->get('logout', 'AuthController@logout'); 

?>