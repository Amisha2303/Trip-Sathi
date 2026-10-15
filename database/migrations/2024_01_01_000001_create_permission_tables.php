<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Role Based Access Control: roles, permissions and their pivots.
 * Owned by spatie/laravel-permission; the schema is published here so the
 * migration order is fully deterministic and reviewable in one place.
 */
return new class extends Migration
{
    public function up(): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('level')->default(50)->index();
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use (
            $tableNames,
            $columnNames,
            $pivotRole
        ) {
            $table->unsignedBigInteger($pivotRole);
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign(
                $pivotRole,
                $tableNames['model_has_roles'] . '_' . $pivotRole . '_foreign'
            )->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary(
                [$pivotRole, 'model_id', 'model_type'],
                'model_has_roles_role_model_type_primary'
            );
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use (
            $tableNames,
            $columnNames,
            $pivotPermission
        ) {
            $table->unsignedBigInteger($pivotPermission);
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign(
                $pivotPermission,
                $tableNames['model_has_permissions'] . '_' . $pivotPermission . '_foreign'
            )->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->primary(
                [$pivotPermission, 'model_id', 'model_type'],
                'model_has_permissions_permission_model_type_primary'
            );
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use (
            $tableNames,
            $pivotRole,
            $pivotPermission
        ) {
            $table->unsignedBigInteger($pivotPermission);
            $table->unsignedBigInteger($pivotRole);

            $table->foreign(
                $pivotPermission,
                $tableNames['role_has_permissions'] . '_' . $pivotPermission . '_foreign'
            )->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign(
                $pivotRole,
                $tableNames['role_has_permissions'] . '_' . $pivotRole . '_foreign'
            )->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        // Spatie sets a config flag after publishing; reset it so
        // `php artisan config:cache` in CI picks up our column names.
        app('config')->set('permission.cache.key', 'spatie.permission.cache');
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');

        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
};
