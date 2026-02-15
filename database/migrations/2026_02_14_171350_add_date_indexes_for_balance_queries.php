<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateIndexesForBalanceQueries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            if (!$this->indexExists('bills', 'bills_created_at_index')) {
                $table->index('created_at', 'bills_created_at_index');
            }
            if (!$this->indexExists('bills', 'bills_updated_at_index')) {
                $table->index('updated_at', 'bills_updated_at_index');
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (!$this->indexExists('expenses', 'expenses_expense_date_index')) {
                $table->index('expense_date', 'expenses_expense_date_index');
            }
        });

        Schema::table('referral_commissions', function (Blueprint $table) {
            if (!$this->indexExists('referral_commissions', 'ref_comm_status_created_index')) {
                $table->index(['status', 'created_at'], 'ref_comm_status_created_index');
            }
            if (!$this->indexExists('referral_commissions', 'ref_comm_status_updated_index')) {
                $table->index(['status', 'updated_at'], 'ref_comm_status_updated_index');
            }
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists($table, $indexName)
    {
        $indexes = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableIndexes($table);
        return array_key_exists($indexName, $indexes);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            if ($this->indexExists('bills', 'bills_created_at_index')) {
                $table->dropIndex('bills_created_at_index');
            }
            if ($this->indexExists('bills', 'bills_updated_at_index')) {
                $table->dropIndex('bills_updated_at_index');
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if ($this->indexExists('expenses', 'expenses_expense_date_index')) {
                $table->dropIndex('expenses_expense_date_index');
            }
        });

        Schema::table('referral_commissions', function (Blueprint $table) {
            if ($this->indexExists('referral_commissions', 'ref_comm_status_created_index')) {
                $table->dropIndex('ref_comm_status_created_index');
            }
            if ($this->indexExists('referral_commissions', 'ref_comm_status_updated_index')) {
                $table->dropIndex('ref_comm_status_updated_index');
            }
        });
    }
}
