<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueAcrossTables implements Rule
{
    protected $table1;
    protected $table2;
    protected $column;

    public function __construct($table1, $table2, $column)
    {
        $this->table1 = $table1;
        $this->table2 = $table2;
        $this->column = $column;
    }

    public function passes($attribute, $value)
    {
        $existsInTable1 = DB::table($this->table1)->where($this->column, $value)->exists();
        $existsInTable2 = DB::table($this->table2)->where($this->column, $value)->exists();

        return !$existsInTable1 && !$existsInTable2;
    }

    public function message()
    {
        return 'The :attribute has already been taken.';
    }
}
