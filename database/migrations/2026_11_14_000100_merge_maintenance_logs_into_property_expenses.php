<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Maintenance is now just an expense in the "maintenance" category, with an
 * optional unit, photo and "still to fix / fixed" flag. This moves every
 * maintenance log into property_expenses and drops the old table, so repair
 * costs finally count towards profit.
 *
 * Uses the query builder, not models: models change over time, migrations must not.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_expenses', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('property_id')->constrained('units')->nullOnDelete();
            $table->string('repair_status')->nullable()->after('category')->index();
            $table->string('photo')->nullable()->after('description');
        });

        DB::table('maintenance_logs')->chunkById(200, function ($logs) {
            DB::table('property_expenses')->insert($logs->map(fn ($log) => [
                'property_id' => $log->property_id,
                'unit_id' => $log->unit_id,
                'expense_date' => $log->maintenance_date,
                'category' => 'maintenance',
                'repair_status' => $log->status === 'completed' ? 'done' : 'open',
                'amount' => $log->total_cost ?? ((float) $log->product_cost + (float) $log->service_cost),
                'invoice_number' => null,
                'description' => $this->describe($log),
                'photo' => $log->photo,
                'created_at' => $log->created_at,
                'updated_at' => $log->updated_at,
            ])->all());
        });

        Schema::drop('maintenance_logs');
    }

    public function down(): void
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->cascadeOnDelete();
            $table->date('maintenance_date');
            $table->text('issue_description');
            $table->decimal('product_cost', 10, 2)->nullable();
            $table->decimal('service_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->string('status')->default('pending');
            $table->string('photo')->nullable();
            $table->timestamps();
            $table->index(['property_id', 'unit_id']);
            $table->index('status');
        });

        // Move repairs back. The parts/labour split can't be recovered; the total is kept.
        DB::table('property_expenses')->whereNotNull('repair_status')
            ->chunkById(200, function ($repairs) {
                DB::table('maintenance_logs')->insert($repairs->map(fn ($repair) => [
                    'property_id' => $repair->property_id,
                    'unit_id' => $repair->unit_id,
                    'maintenance_date' => $repair->expense_date,
                    'issue_description' => $repair->description ?? 'Repair',
                    'total_cost' => $repair->amount,
                    'status' => $repair->repair_status === 'done' ? 'completed' : 'pending',
                    'photo' => $repair->photo,
                    'created_at' => $repair->created_at,
                    'updated_at' => $repair->updated_at,
                ])->all());
            });

        DB::table('property_expenses')->whereNotNull('repair_status')->delete();

        Schema::table('property_expenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('unit_id');
            $table->dropIndex(['repair_status']);
            $table->dropColumn(['repair_status', 'photo']);
        });
    }

    private function describe(object $log): string
    {
        $parts = array_filter([
            $log->product_cost !== null ? 'parts ৳'.number_format((float) $log->product_cost, 2) : null,
            $log->service_cost !== null ? 'labour ৳'.number_format((float) $log->service_cost, 2) : null,
        ]);

        return $parts === []
            ? $log->issue_description
            : $log->issue_description.' ('.implode(', ', $parts).')';
    }
};
