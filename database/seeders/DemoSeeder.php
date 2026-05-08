<?php

namespace Database\Seeders;

use App\Enums\LeaseStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PropertyStatus;
use App\Enums\UnitStatus;
use App\Models\Lease;
use App\Models\MaintenanceLog;
use App\Models\Payment;
use App\Models\Property;
use App\Models\PropertyExpense;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Creates a complete, realistic demo dataset so anyone cloning
     * the repository can immediately explore all features.
     *
     * Demo credentials:
     *   Email:    demo@estatra.com
     *   Password: password
     */
    public function run(): void
    {
        // ── 1. Demo user ──────────────────────────────────────────────────
        $user = User::firstOrCreate(
            ['email' => 'demo@estatra.com'],
            [
                'first_name' => 'Demo',
                'last_name'  => 'Owner',
                'password'   => Hash::make('password'),
            ]
        );

        // ── 2. Properties ─────────────────────────────────────────────────
        $properties = [
            [
                'property_name' => 'Sunrise Apartments',
                'description'   => 'A modern 6-storey apartment block in the heart of Dhaka.',
                'property_type' => 'apartment',
                'address'       => '12 Gulshan Avenue',
                'city'          => 'Dhaka',
                'state'         => 'Dhaka Division',
                'zip_code'      => '1212',
                'country'       => 'Bangladesh',
                'status'        => PropertyStatus::Active,
            ],
            [
                'property_name' => 'Green Valley Duplex',
                'description'   => 'A quiet duplex property with garden, ideal for families.',
                'property_type' => 'duplex',
                'address'       => '5 Banani Road',
                'city'          => 'Dhaka',
                'state'         => 'Dhaka Division',
                'zip_code'      => '1213',
                'country'       => 'Bangladesh',
                'status'        => PropertyStatus::Active,
            ],
            [
                'property_name' => 'Port City Flats',
                'description'   => 'Sea-facing residential units in Chittagong.',
                'property_type' => 'apartment',
                'address'       => '88 Patenga Road',
                'city'          => 'Chittagong',
                'state'         => 'Chattogram Division',
                'zip_code'      => '4221',
                'country'       => 'Bangladesh',
                'status'        => PropertyStatus::Active,
            ],
        ];

        $createdProperties = [];
        foreach ($properties as $data) {
            $createdProperties[] = Property::create(array_merge($data, ['user_id' => $user->id]));
        }

        // ── 3. Units (4 per property, mixed statuses) ─────────────────────
        $unitNames  = ['A-101', 'A-102', 'B-201', 'B-202'];
        $unitSizes  = [750, 900, 1100, 650];
        $unitFloors = [1, 1, 2, 2];

        $allUnits = [];
        foreach ($createdProperties as $property) {
            foreach ($unitNames as $i => $name) {
                $allUnits[] = Unit::create([
                    'property_id'  => $property->id,
                    'unit_name'    => $name,
                    'floor_number' => $unitFloors[$i],
                    'size'         => $unitSizes[$i],
                    'status'       => UnitStatus::Available, // will be updated below
                    'notes'        => null,
                ]);
            }
        }

        // ── 4. Tenants ────────────────────────────────────────────────────
        $tenantData = [
            ['first_name' => 'Rahim', 'last_name' => 'Uddin',  'email' => 'rahim@example.com',  'phone' => '01711-111111', 'occupation' => 'Software Engineer'],
            ['first_name' => 'Karim', 'last_name' => 'Hossain','email' => 'karim@example.com',  'phone' => '01722-222222', 'occupation' => 'Doctor'],
            ['first_name' => 'Sara',  'last_name' => 'Begum',  'email' => 'sara@example.com',   'phone' => '01733-333333', 'occupation' => 'Teacher'],
            ['first_name' => 'Nadia', 'last_name' => 'Islam',  'email' => 'nadia@example.com',  'phone' => '01744-444444', 'occupation' => 'Banker'],
            ['first_name' => 'Tarek', 'last_name' => 'Ahmed',  'email' => 'tarek@example.com',  'phone' => '01755-555555', 'occupation' => 'Business Owner'],
            ['first_name' => 'Riya',  'last_name' => 'Chowdh.','email' => 'riya@example.com',   'phone' => '01766-666666', 'occupation' => 'NGO Worker'],
            ['first_name' => 'Fazlu', 'last_name' => 'Rahman', 'email' => 'fazlu@example.com',  'phone' => '01777-777777', 'occupation' => 'Government Official'],
            ['first_name' => 'Mim',   'last_name' => 'Akter',  'email' => 'mim@example.com',    'phone' => '01788-888888', 'occupation' => 'Pharmacist'],
        ];
        $tenants = [];
        foreach ($tenantData as $data) {
            $tenants[] = Tenant::create(array_merge($data, [
                'user_id'           => $user->id,
                'national_id'       => fake()->numerify('##########'),
                'address'           => fake()->address(),
                'emergency_contact' => fake()->name() . ' (' . fake()->phoneNumber() . ')',
                'family_members'    => fake()->numberBetween(1, 4),
            ]));
        }

        // ── 5. Active Leases (8 units occupied, first 8 units) ───────────
        $rentAmounts  = [10000, 12000, 15000, 8000, 18000, 14000, 11000, 9000];
        $svcCharges   = [500, 1000, null, 500, 1500, null, 800, null];
        $deposits     = [20000, 24000, 30000, 16000, 36000, 28000, 22000, 18000];
        $leaseUnits   = array_slice($allUnits, 0, 8); // first 8 units across properties

        $leases = [];
        foreach ($leaseUnits as $i => $unit) {
            $lease = Lease::create([
                'unit_id'          => $unit->id,
                'tenant_id'        => $tenants[$i]->id,
                'start_date'       => Carbon::now()->subMonths(6)->startOfMonth(),
                'end_date'         => null,
                'rent_amount'      => $rentAmounts[$i],
                'service_charge'   => $svcCharges[$i],
                'security_deposit' => $deposits[$i],
                'status'           => LeaseStatus::Active,
            ]);

            // Mark the unit as occupied
            $unit->update(['status' => UnitStatus::Occupied]);

            $leases[] = $lease;
        }

        // ── 6. Payments — 3 months of history per lease ───────────────────
        $receiptCounter = 1;
        $paymentMethods = [
            PaymentMethod::Cash,
            PaymentMethod::BankTransfer,
            PaymentMethod::MobilePayment,
        ];

        foreach ($leases as $lease) {
            $totalRent = $lease->rent_amount + ($lease->service_charge ?? 0);

            for ($monthsAgo = 3; $monthsAgo >= 1; $monthsAgo--) {
                $rentPeriod  = Carbon::now()->subMonths($monthsAgo)->startOfMonth();
                $paymentDate = $rentPeriod->copy()->addDays(fake()->numberBetween(1, 10));

                Payment::create([
                    'lease_id'       => $lease->id,
                    'receipt_number' => str_pad($receiptCounter++, 4, '0', STR_PAD_LEFT),
                    'payment_date'   => $paymentDate,
                    'rent_period'    => $rentPeriod->format('Y-m-d'),
                    'amount_paid'    => $totalRent,
                    'payment_method' => fake()->randomElement($paymentMethods),
                    'status'         => PaymentStatus::Paid,
                    'notes'          => null,
                ]);
            }

            // Current month — partial/unpaid for some, to make the dashboard interesting
            $currentPeriod = Carbon::now()->startOfMonth();
            $alreadyPaid   = Payment::where('lease_id', $lease->id)
                ->where('rent_period', $currentPeriod->format('Y-m-d'))
                ->exists();

            if (!$alreadyPaid) {
                $isPartial = fake()->boolean(30); // 30% chance partial
                Payment::create([
                    'lease_id'       => $lease->id,
                    'receipt_number' => str_pad($receiptCounter++, 4, '0', STR_PAD_LEFT),
                    'payment_date'   => Carbon::now()->subDays(fake()->numberBetween(1, 5)),
                    'rent_period'    => $currentPeriod->format('Y-m-d'),
                    'amount_paid'    => $isPartial ? round($totalRent * 0.5) : $totalRent,
                    'payment_method' => fake()->randomElement($paymentMethods),
                    'status'         => $isPartial ? PaymentStatus::Partial : PaymentStatus::Paid,
                    'notes'          => $isPartial ? 'Partial payment — remainder due end of month.' : null,
                ]);
            }
        }

        // ── 7. Property Expenses — 3 months per property ──────────────────
        $expenseCategories = array_keys(PropertyExpense::getCategories());
        $invoiceCounter    = 1;

        foreach ($createdProperties as $property) {
            for ($monthsAgo = 3; $monthsAgo >= 0; $monthsAgo--) {
                $expenseCount = fake()->numberBetween(1, 3);
                for ($e = 0; $e < $expenseCount; $e++) {
                    $expDate = Carbon::now()->subMonths($monthsAgo)->addDays(fake()->numberBetween(1, 20));
                    PropertyExpense::create([
                        'property_id'    => $property->id,
                        'expense_date'   => $expDate->format('Y-m-d'),
                        'category'       => fake()->randomElement($expenseCategories),
                        'amount'         => fake()->randomElement([500, 800, 1200, 2000, 3500]),
                        'invoice_number' => str_pad($invoiceCounter++, 4, '0', STR_PAD_LEFT),
                        'description'    => fake()->optional(0.6)->sentence(5),
                    ]);
                }
            }
        }

        // ── 8. Maintenance Logs ────────────────────────────────────────────
        $maintenanceIssues = [
            'Leaking pipe in bathroom',
            'Electrical socket not working in bedroom',
            'Air conditioner service required',
            'Roof seepage during rain',
            'Paint peeling in living room',
            'Broken door lock',
        ];

        foreach ($createdProperties as $property) {
            $logCount = fake()->numberBetween(2, 3);
            for ($l = 0; $l < $logCount; $l++) {
                $productCost = fake()->optional(0.7)->randomElement([200, 500, 800, 1500]);
                $serviceCost = fake()->optional(0.8)->randomElement([300, 500, 1000, 2000]);

                MaintenanceLog::create([
                    'property_id'       => $property->id,
                    'unit_id'           => null,
                    'maintenance_date'  => Carbon::now()->subDays(fake()->numberBetween(5, 60))->format('Y-m-d'),
                    'issue_description' => fake()->randomElement($maintenanceIssues),
                    'product_cost'      => $productCost,
                    'service_cost'      => $serviceCost,
                    'total_cost'        => ($productCost ?? 0) + ($serviceCost ?? 0) ?: null,
                    'status'            => fake()->randomElement(['pending', 'in_progress', 'completed']),
                    'photo'             => null,
                ]);
            }
        }

        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->info('   Login: demo@estatra.com / password');
        $this->command->info('   Created: 3 properties, 12 units, 8 tenants, 8 leases, 32+ payments');
    }
}
