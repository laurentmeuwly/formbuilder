<?php

namespace LaurentMeuwly\FormBuilder\Traits;

trait UsesConfiguredTable
{
    protected string $tableConfigKey;

    public function getTable()
    {
        // If classe defines TABLE_CONFIG_KEY, use it
        if (defined(static::class.'::TABLE_CONFIG_KEY')) {
            $key = constant(static::class.'::TABLE_CONFIG_KEY');
            return config("formbuilder.table_names.{$key}", parent::getTable());
        }

        return parent::getTable();
    }
}
