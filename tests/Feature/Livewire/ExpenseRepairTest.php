<?php

namespace Tests\Feature\Livewire;

use App\Enums\RepairStatus;
use App\Livewire\V1\Dashboard\Dashboard;
use App\Livewire\V1\Expense\ExpenseForm;
use App\Livewire\V1\Expense\ExpenseList;
use App\Models\Property;
use App\Models\PropertyExpense;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Maintenance is now an expense in the "maintenance" category.
 */
class ExpenseRepairTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private Property $property;

    private Unit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo('2026-11-15 10:00:00');
        $this->owner = User::factory()->create();
        $this->property = Property::factory()->create(['user_id' => $this->owner->id]);
        $this->unit = Unit::factory()->for($this->property)->create();
    }

    public function test_an_owner_can_log_a_repair_with_unit_photo_and_status(): void
    {
        Storage::fake('public');

        Livewire::actingAs($this->owner)
            ->withQueryParams(['category' => PropertyExpense::REPAIR])
            ->test(ExpenseForm::class)
            ->assertSet('category', PropertyExpense::REPAIR)
            ->set('property_id', (string) $this->property->id)
            ->set('unit_id', (string) $this->unit->id)
            ->set('amount', 1500)
            ->set('description', 'Leaking pipe in bathroom')
            ->set('repair_status', RepairStatus::Open->value)
            ->set('photo', UploadedFile::fake()->image('leak.jpg'))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('expense.index'));

        $repair = PropertyExpense::sole();
        $this->assertSame($this->unit->id, $repair->unit_id);
        $this->assertSame(RepairStatus::Open, $repair->repair_status);
        Storage::disk('public')->assertExists($repair->photo);
    }

    public function test_a_repair_needs_a_description(): void
    {
        Livewire::actingAs($this->owner)
            ->test(ExpenseForm::class)
            ->set('property_id', (string) $this->property->id)
            ->set('category', PropertyExpense::REPAIR)
            ->set('amount', 0)
            ->call('save')
            ->assertHasErrors(['description' => 'required']);
    }

    public function test_repair_fields_are_cleared_for_other_categories(): void
    {
        Livewire::actingAs($this->owner)
            ->test(ExpenseForm::class)
            ->set('property_id', (string) $this->property->id)
            ->set('category', 'water')
            ->set('unit_id', (string) $this->unit->id)
            ->set('amount', 900)
            ->call('save')
            ->assertHasNoErrors();

        $expense = PropertyExpense::sole();
        $this->assertNull($expense->unit_id);
        $this->assertNull($expense->repair_status);
    }

    public function test_the_unit_must_be_in_the_chosen_property(): void
    {
        $otherProperty = Property::factory()->create(['user_id' => $this->owner->id]);
        $unitElsewhere = Unit::factory()->for($otherProperty)->create();

        Livewire::actingAs($this->owner)
            ->test(ExpenseForm::class)
            ->set('property_id', (string) $this->property->id)
            ->set('category', PropertyExpense::REPAIR)
            ->set('unit_id', (string) $unitElsewhere->id)
            ->set('amount', 100)
            ->set('description', 'Broken lock')
            ->call('save')
            ->assertHasErrors(['unit_id']);
    }

    public function test_an_owner_cannot_add_expenses_to_someone_elses_property(): void
    {
        $someoneElses = Property::factory()->create();

        Livewire::actingAs($this->owner)
            ->test(ExpenseForm::class)
            ->set('property_id', (string) $someoneElses->id)
            ->set('category', 'water')
            ->set('amount', 100)
            ->call('save')
            ->assertHasErrors(['property_id']);
    }

    public function test_the_list_can_show_only_repairs_still_to_fix(): void
    {
        $open = PropertyExpense::factory()->for($this->property)->create([
            'category' => PropertyExpense::REPAIR, 'repair_status' => RepairStatus::Open, 'description' => 'Roof leak',
        ]);
        PropertyExpense::factory()->for($this->property)->create([
            'category' => PropertyExpense::REPAIR, 'repair_status' => RepairStatus::Done, 'description' => 'Door fixed',
        ]);
        PropertyExpense::factory()->for($this->property)->create(['category' => 'water', 'description' => 'Water bill']);

        Livewire::actingAs($this->owner)
            ->test(ExpenseList::class)
            ->assertSee('Water bill')
            ->set('openRepairs', true)
            ->assertSee('Roof leak')
            ->assertDontSee('Door fixed')
            ->assertDontSee('Water bill');

        $this->assertSame([$open->id], PropertyExpense::openRepairs()->pluck('id')->all());
    }

    public function test_repair_costs_count_towards_monthly_expenses(): void
    {
        PropertyExpense::factory()->for($this->property)->create([
            'category' => PropertyExpense::REPAIR, 'repair_status' => RepairStatus::Done,
            'amount' => 2500, 'expense_date' => '2026-11-03',
        ]);

        Livewire::actingAs($this->owner)
            ->test(Dashboard::class)
            ->assertSet('monthlyExpenses', 2500.0);
    }

    public function test_invoice_numbers_do_not_clash_between_owners(): void
    {
        foreach ([$this->owner, User::factory()->create()] as $owner) {
            $property = Property::factory()->create(['user_id' => $owner->id]);

            Livewire::actingAs($owner)
                ->test(ExpenseForm::class)
                ->set('property_id', (string) $property->id)
                ->set('category', 'water')
                ->set('amount', 100)
                ->call('save')
                ->assertHasNoErrors();
        }

        $this->assertSame(['0001', '0002'], PropertyExpense::withoutGlobalScopes()->orderBy('id')->pluck('invoice_number')->all());
    }
}
