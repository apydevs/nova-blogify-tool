<?php

namespace Mattmangoni\NovaBlogifyTool\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mattmangoni\NovaBlogifyTool\BlogifyResponder;

class UninstallController extends BlogifyBaseController
{
    protected function processTask() : void
    {
        foreach ($this->migrations as $tableName => $migrationClass) {
            if ($tableName != 'foreign' || ! Schema::hasTable($tableName) ) {
                $this->messages[] = BlogifyResponder::deleteTableNotFound($tableName);
            }

            try {
                $this->rollBack($migrationClass);
                $this->messages[] = BlogifyResponder::deleteSuccess($tableName);
            } catch (\Exception $e) {
                $this->messages[] = BlogifyResponder::deleteFailure($tableName);
            }
        }
    }

    protected function rollBack($migrationClass)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $migrationClass->down();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
