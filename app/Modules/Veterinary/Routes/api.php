<?php

use App\Modules\Veterinary\Controllers\VeterinaryController;
use App\Modules\Veterinary\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::post('/veterinary-profile', [VeterinaryController::class, 'store']);
    Route::get('/veterinarians', [VeterinaryController::class, 'index']);
    Route::get('/veterinarians/cities', [VeterinaryController::class, 'getCities']);
    Route::get('/veterinarians/{id}', [VeterinaryController::class, 'show']);
    Route::get('/veterinarians/{id}/posts', [VeterinaryController::class, 'getPosts']);
    Route::get('/veterinarians/{id}/reviews', [VeterinaryController::class, 'getReviews']);
    Route::post('/veterinarians/{id}/reviews', [VeterinaryController::class, 'addReview']);

    // Appointment routes
    Route::get('/veterinarians/{id}/slots', [AppointmentController::class, 'getAvailableSlots']);
    Route::post('/appointments', [AppointmentController::class, 'book']);
    Route::get('/appointments/my-appointments', [AppointmentController::class, 'myAppointments']);
    Route::get('/veterinarians/my-clinic/appointments', [AppointmentController::class, 'clinicAppointments']);
    Route::post('/appointments/{id}/status', [AppointmentController::class, 'updateStatus']);
    Route::post('/appointments/{id}/reschedule', [AppointmentController::class, 'reschedule']);
    Route::post('/veterinarians/my-clinic/weekly-availability', [AppointmentController::class, 'updateWeeklyAvailability']);
    Route::post('/veterinarians/my-clinic/exceptions', [AppointmentController::class, 'saveException']);
    Route::delete('/veterinarians/my-clinic/exceptions/{id}', [AppointmentController::class, 'deleteException']);
});
