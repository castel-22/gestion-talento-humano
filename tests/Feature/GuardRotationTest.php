<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\GuardDuty;
use App\Models\GuardRotation;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class GuardRotationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Bypass all Laravel Policies/authorization checks (avoids Spatie role/permission database lookups)
        Gate::before(fn () => true);

        // Create the tables manually to run tests in SQLite in-memory without running broken/deleted migration files.
        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        // Spatie Permissions tables
        Schema::create('permissions', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create('roles', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create('role_has_permissions', function ($table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('model_has_roles', function ($table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['role_id', 'model_id', 'model_type']);
        });

        Schema::create('model_has_permissions', function ($table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['permission_id', 'model_id', 'model_type']);
        });

        Schema::create('employees', function ($table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('status')->default('activo');
            $table->timestamps();
        });

        Schema::create('guard_rotations', function ($table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('employee_a_id')->nullable();
            $table->unsignedBigInteger('employee_b_id')->nullable();
            $table->unsignedBigInteger('employee_c_id')->nullable();
            $table->unsignedBigInteger('employee_d_id')->nullable();
            $table->timestamps();
        });

        Schema::create('guard_duties', function ($table) {
            $table->id();
            $table->unsignedBigInteger('guard_rotation_id');
            $table->date('date');
            $table->string('letter');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Create a user and sign in.
     */
    protected function signIn()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
        ]);

        return $this->actingAs($user);
    }

    /**
     * Test saving a rotation with employee codifications.
     */
    public function test_can_store_guard_rotation_with_codification()
    {
        $this->signIn();

        $employeeA = Employee::create(['first_name' => 'Juan', 'last_name' => 'Perez']);
        $employeeB = Employee::create(['first_name' => 'Maria', 'last_name' => 'Gomez']);

        $response = $this->post(route('guard-rotations.store'), [
            'name' => 'Guardias de Emergencia',
            'description' => 'Rotación de prueba',
            'is_active' => 1,
            'employee_a_id' => $employeeA->id,
            'employee_b_id' => $employeeB->id,
        ]);

        $response->assertRedirect(route('guard-rotations.index'));
        $this->assertDatabaseHas('guard_rotations', [
            'name' => 'Guardias de Emergencia',
            'employee_a_id' => $employeeA->id,
            'employee_b_id' => $employeeB->id,
            'employee_c_id' => null,
        ]);
    }

    /**
     * Test validation checks before sequence generation.
     */
    public function test_cannot_generate_monthly_guard_sequence_without_full_codification()
    {
        $this->signIn();

        $employeeA = Employee::create(['first_name' => 'Juan', 'last_name' => 'Perez']);

        // Create rotation with incomplete codification (only A is set)
        $rotation = GuardRotation::create([
            'name' => 'Guardia Incompleta',
            'employee_a_id' => $employeeA->id,
        ]);

        $response = $this->post(route('guard-rotations.generate', $rotation), [
            'month' => 5,
            'year' => 2026,
        ]);

        $response->assertRedirect(route('guard-rotations.edit', $rotation));
        $response->assertSessionHas('error');
    }

    /**
     * Test successful sequence generation when full codification exists.
     */
    public function test_can_generate_monthly_guard_sequence_with_full_codification()
    {
        $this->signIn();

        $empA = Employee::create(['first_name' => 'Tech', 'last_name' => 'A']);
        $empB = Employee::create(['first_name' => 'Tech', 'last_name' => 'B']);
        $empC = Employee::create(['first_name' => 'Tech', 'last_name' => 'C']);
        $empD = Employee::create(['first_name' => 'Tech', 'last_name' => 'D']);

        $rotation = GuardRotation::create([
            'name' => 'Guardia Completa',
            'employee_a_id' => $empA->id,
            'employee_b_id' => $empB->id,
            'employee_c_id' => $empC->id,
            'employee_d_id' => $empD->id,
        ]);

        $response = $this->post(route('guard-rotations.generate', $rotation), [
            'month' => 5, // May has 31 days
            'year' => 2026,
        ]);

        $response->assertRedirect(route('guard-rotations.calendar', [
            'guard_rotation' => $rotation->id,
            'month' => 5,
            'year' => 2026
        ]));

        $duties = GuardDuty::where('guard_rotation_id', $rotation->id)->orderBy('date')->get();
        $this->assertCount(31, $duties);

        // Check sequence A-B-C-D-A-B-C-D...
        $this->assertEquals('A', $duties[0]->letter);
        $this->assertEquals($empA->id, $duties[0]->employee_id);

        $this->assertEquals('B', $duties[1]->letter);
        $this->assertEquals($empB->id, $duties[1]->employee_id);

        $this->assertEquals('C', $duties[2]->letter);
        $this->assertEquals($empC->id, $duties[2]->employee_id);

        $this->assertEquals('D', $duties[3]->letter);
        $this->assertEquals($empD->id, $duties[3]->employee_id);

        $this->assertEquals('A', $duties[4]->letter);
        $this->assertEquals($empA->id, $duties[4]->employee_id);
    }

    /**
     * Test updating a specific day.
     */
    public function test_can_update_specific_day()
    {
        $this->signIn();

        $rotation = GuardRotation::create(['name' => 'Guardia Test']);
        $emp = Employee::create(['first_name' => 'Tech', 'last_name' => 'A']);

        $response = $this->post(route('guard-rotations.update-day', $rotation), [
            'date' => '2026-05-01',
            'letter' => 'A',
            'employee_id' => $emp->id,
            'notes' => 'Nota especial',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('guard_duties', [
            'guard_rotation_id' => $rotation->id,
            'date' => '2026-05-01 00:00:00',
            'letter' => 'A',
            'employee_id' => $emp->id,
            'notes' => 'Nota especial',
        ]);
    }
}
