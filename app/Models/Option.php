<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Casts\Attribute;

class Option extends BaseModel
{
    protected $fillable = [
        'option_name', 'option_value', 'field_type'
    ];

    public array $searchable = ['option_name'];


    protected function optionValue(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (is_json($value)) {
                    return json_decode($value);
                }

                return $value;
            },
            set: function ($value) {
                if (is_array($value) || is_object($value)) {
                    return json_encode($value);
                }

                return $value;
            },
        );
    }

    public static function fetch($name, $default = null)
    {
        if ($row = self::where('option_name', $name)->first()) {
            return $row->option_value;
        }

        return $default;
    }

    public static function modify($name, $value = null)
    {
        return self::updateOrCreate(
            ['option_name' => $name],
            ['option_value' => $value]
        );
    }

    public static function init($name, $value = null)
    {
        return self::firstOrCreate(
            ['option_name' => $name],
            ['option_value' => $value]
        );
    }
}
