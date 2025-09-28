<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use App\Filament\Resources\EnrollmentResource;
use App\Models\Enrollment;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\QueryException;

class CreateEnrollment extends CreateRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            $enrollment = Enrollment::create($data);
            
            // If a discount code was used, increment its usage count
            if (!empty($data['discount_code'])) {
                $discountCode = \App\Models\DiscountCode::where('code', $data['discount_code'])->first();
                if ($discountCode) {
                    $discountCode->incrementUsage();
                }
            }
            
            return $enrollment;
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation
                $this->halt();
                $this->notify('error', 'This student is already enrolled in the selected course. Please choose a different course or student.');
                // Return a new empty model to satisfy the return type
                return new Enrollment();
            }
            throw $e;
        }
    }
}
