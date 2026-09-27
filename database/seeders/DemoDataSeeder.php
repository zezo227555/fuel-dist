<?php

namespace Database\Seeders;

use App\Actions\StoreCitizenDocument;
use App\Actions\StoreVehicleImage;
use App\Enums\AppRole;
use App\Enums\DocumentType;
use App\Enums\FillDenialReason;
use App\Enums\ReviewDecision;
use App\Enums\VehicleImageType;
use App\Enums\VehicleStatus;
use App\Enums\VehicleType;
use App\Models\CooldownChangeRequest;
use App\Models\CooldownSetting;
use App\Models\FillUp;
use App\Models\FillUpDenial;
use App\Models\Station;
use App\Models\User;
use App\Models\Vehicle;
use App\Support\PlateNumber;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->ensureMockImagesExist();

        $storeDocument = app(StoreCitizenDocument::class);
        $storeVehicleImage = app(StoreVehicleImage::class);

        $admin = $this->staffUser('admin@fdms.test', 'مسؤول النظام', AppRole::Supervisor);
        $supervisor2 = $this->staffUser('supervisor2@fdms.test', 'مشرف ثانٍ', AppRole::Supervisor);
        $reviewer = $this->staffUser('reviewer@fdms.test', 'مراجع التسجيلات', AppRole::Reviewer);
        $auditor = $this->staffUser('auditor@fdms.test', 'مدقق النظام', AppRole::Auditor);
        $attendant = $this->staffUser('attendant@fdms.test', 'موظف المحطة', AppRole::Attendant);
        $attendant2 = $this->staffUser('attendant2@fdms.test', 'موظف محطة الجنوب', AppRole::Attendant);

        $north = Station::query()->updateOrCreate(
            ['name' => 'محطة الشمال'],
            [
                'address' => 'حي المنشية، سبها',
                'latitude' => 27.0377000,
                'longitude' => 14.4283000,
                'map_url' => null,
                'is_active' => true,
            ],
        );

        $south = Station::query()->updateOrCreate(
            ['name' => 'محطة الجنوب'],
            [
                'address' => 'حي المهدية، سبها',
                'latitude' => 26.9950000,
                'longitude' => 14.4410000,
                'map_url' => null,
                'is_active' => true,
            ],
        );

        $east = Station::query()->updateOrCreate(
            ['name' => 'محطة الشرق'],
            [
                'address' => 'طريق المطار، سبها',
                'latitude' => 27.0100000,
                'longitude' => 14.4700000,
                'map_url' => null,
                'is_active' => true,
            ],
        );

        $north->attendants()->sync([$attendant->id]);
        $south->attendants()->sync([$attendant2->id]);
        $east->attendants()->sync([$attendant->id, $attendant2->id]);

        CooldownSetting::current()->update(['period_hours' => 96]);

        $eligibleCitizen = $this->citizen(
            'citizen@fdms.test',
            'أحمد علي',
            '0911111111',
            '11990010010011',
            'P1000001',
            $storeDocument,
        );

        $pendingCitizen = $this->citizen(
            'citizen2@fdms.test',
            'فاطمة حسن',
            '0922222222',
            '11990020020022',
            'P2000002',
            $storeDocument,
        );

        $rejectedCitizen = $this->citizen(
            'citizen3@fdms.test',
            'سالم عمر',
            '0933333333',
            '11990030030033',
            'P3000003',
            $storeDocument,
        );

        $cooldownCitizen = $this->citizen(
            'citizen4@fdms.test',
            'مريم صالح',
            '0944444444',
            '11990040040044',
            'P4000004',
            $storeDocument,
        );

        $eligibleVehicle = $this->vehicle(
            $eligibleCitizen,
            'SEB-1001',
            VehicleType::Private,
            VehicleStatus::Approved,
            $reviewer,
            $storeVehicleImage,
        );

        $pendingVehicle = $this->vehicle(
            $pendingCitizen,
            'SEB-2002',
            VehicleType::Taxi,
            VehicleStatus::Pending,
            null,
            $storeVehicleImage,
        );

        $rejectedVehicle = $this->vehicle(
            $rejectedCitizen,
            'SEB-3003',
            VehicleType::Commercial,
            VehicleStatus::Rejected,
            $reviewer,
            $storeVehicleImage,
            'الصور غير واضحة',
        );

        $cooldownVehicle = $this->vehicle(
            $cooldownCitizen,
            'SEB-4004',
            VehicleType::Private,
            VehicleStatus::Approved,
            $reviewer,
            $storeVehicleImage,
        );

        FillUp::query()
            ->where('vehicle_id', $cooldownVehicle->id)
            ->delete();

        FillUp::query()->create([
            'vehicle_id' => $cooldownVehicle->id,
            'station_id' => $north->id,
            'attendant_id' => $attendant->id,
            'filled_at' => now()->subDay(),
        ]);

        FillUpDenial::query()
            ->where('plate_number', $cooldownVehicle->plate_number)
            ->delete();

        FillUpDenial::query()->create([
            'plate_number' => $cooldownVehicle->plate_number,
            'vehicle_id' => $cooldownVehicle->id,
            'station_id' => $north->id,
            'attendant_id' => $attendant->id,
            'reason' => FillDenialReason::CooldownActive,
            'attempted_at' => now()->subHours(2),
            'eligible_at' => now()->subDay()->addHours(96),
        ]);

        if (! CooldownChangeRequest::query()->where('status', 'pending')->exists()) {
            CooldownChangeRequest::query()->create([
                'old_period_hours' => 96,
                'new_period_hours' => 48,
                'proposed_by' => $admin->id,
                'status' => 'pending',
                'notes' => 'اقتراح تجريبي لتقصير فترة الانتظار إلى يومين',
            ]);
        }

        $this->command?->newLine();
        $this->command?->info('Demo data ready. Password for all accounts: password');
        $this->command?->table(
            ['Role / use', 'Email', 'Try this'],
            [
                ['Supervisor (propose cooldown)', $admin->email, 'Approve stations / cooldown / reviews'],
                ['Supervisor 2 (second approver)', $supervisor2->email, 'Approve pending cooldown 4 to 2 days'],
                ['Reviewer', $reviewer->email, 'Review pending vehicle SEB-2002'],
                ['Auditor', $auditor->email, 'Read-only cooldown / staff panel'],
                ['Attendant (north + east)', $attendant->email, 'Desk: SEB-1001 eligible, SEB-4004 blocked'],
                ['Attendant 2 (south + east)', $attendant2->email, 'Station desk at الجنوب'],
                ['Citizen (approved car)', $eligibleCitizen->email, 'Vehicles + stations list'],
                ['Citizen (pending car)', $pendingCitizen->email, 'Pending registration SEB-2002'],
                ['Citizen (rejected car)', $rejectedCitizen->email, 'Rejected registration SEB-3003'],
                ['Citizen (on cooldown)', $cooldownCitizen->email, 'Approved SEB-4004, filled yesterday'],
            ],
        );

        $this->command?->info("Eligible plate for fill-up: {$eligibleVehicle->plate_display}");
        $this->command?->info("Pending plate for review: {$pendingVehicle->plate_display}");
        $this->command?->info("Rejected plate: {$rejectedVehicle->plate_display}");
        $this->command?->info("Cooldown plate: {$cooldownVehicle->plate_display}");
        $this->command?->info("Stations: {$north->name}, {$south->name}, {$east->name}");
    }

    protected function staffUser(string $email, string $name, AppRole $role): User
    {
        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'phone' => null,
                'national_id' => null,
                'passport_number' => null,
            ],
        );

        $user->syncRoles([$role]);

        return $user;
    }

    protected function citizen(
        string $email,
        string $name,
        string $phone,
        string $nationalId,
        string $passport,
        StoreCitizenDocument $storeDocument,
    ): User {
        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'phone' => $phone,
                'national_id' => $nationalId,
                'passport_number' => $passport,
            ],
        );

        $user->syncRoles([AppRole::Citizen]);

        $storeDocument->handle($user, DocumentType::NationalId, $this->upload('doc/id-card.jpg'));
        $storeDocument->handle($user, DocumentType::Passport, $this->upload('doc/passport.jpg'));
        $storeDocument->handle($user, DocumentType::Selfie, $this->upload('doc/selfi.png'));

        return $user->fresh(['documents']) ?? $user;
    }

    protected function vehicle(
        User $owner,
        string $plateDisplay,
        VehicleType $type,
        VehicleStatus $status,
        ?User $reviewer,
        StoreVehicleImage $storeVehicleImage,
        ?string $rejectionReason = null,
    ): Vehicle {
        $normalized = PlateNumber::normalize($plateDisplay);

        $vehicle = Vehicle::query()->updateOrCreate(
            ['plate_number' => $normalized],
            [
                'user_id' => $owner->id,
                'plate_display' => $plateDisplay,
                'type' => $type,
                'status' => $status,
                'reviewed_by' => $status === VehicleStatus::Pending ? null : $reviewer?->id,
                'reviewed_at' => $status === VehicleStatus::Pending ? null : now()->subHours(6),
                'rejection_reason' => $status === VehicleStatus::Rejected ? $rejectionReason : null,
            ],
        );

        $storeVehicleImage->handle($vehicle, VehicleImageType::Front, $this->upload('cars/front.jpg'));
        $storeVehicleImage->handle($vehicle, VehicleImageType::Rear, $this->upload('cars/reare.jpg'));
        $storeVehicleImage->handle($vehicle, VehicleImageType::Side, $this->upload('cars/front.jpg'));

        if ($status !== VehicleStatus::Pending && $reviewer) {
            $vehicle->reviewLogs()->delete();
            $vehicle->reviewLogs()->create([
                'reviewer_id' => $reviewer->id,
                'decision' => $status === VehicleStatus::Approved
                    ? ReviewDecision::Approved
                    : ReviewDecision::Rejected,
                'notes' => $rejectionReason,
                'created_at' => now()->subHours(6),
            ]);
        }

        return $vehicle->fresh(['images', 'owner']) ?? $vehicle;
    }

    protected function upload(string $relativePublicPath): UploadedFile
    {
        $path = public_path($relativePublicPath);

        if (! is_file($path)) {
            throw new RuntimeException("Missing mock image: {$path}");
        }

        $filename = basename($path);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mime = match ($extension) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };

        return new UploadedFile($path, $filename, $mime, null, true);
    }

    protected function ensureMockImagesExist(): void
    {
        foreach ([
            'doc/id-card.jpg',
            'doc/passport.jpg',
            'doc/selfi.png',
            'cars/front.jpg',
            'cars/reare.jpg',
        ] as $relative) {
            $path = public_path($relative);

            if (! is_file($path)) {
                throw new RuntimeException("Demo seeder needs mock image at public/{$relative}");
            }
        }
    }
}
