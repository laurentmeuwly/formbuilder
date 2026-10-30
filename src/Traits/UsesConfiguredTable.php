<?php

namespace LaurentMeuwly\FormBuilder\Traits;

trait UsesConfiguredTable
{
    /**
     * Use the table name configured in `formbuilder.table_names` for the
     * key given by the model's TABLE_CONFIG_KEY constant.
     */
    public function getTable()
    {
        if (defined(static::class.'::TABLE_CONFIG_KEY')) {
            $key = constant(static::class.'::TABLE_CONFIG_KEY');

            return config("formbuilder.table_names.{$key}", parent::getTable());
        }

        return parent::getTable();
    }
}
