<?php

namespace Modules\FormsPlugin\Models;

use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Response extends Model
{
    use AdvancedEloquentTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'module_formsplugin_responses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'form_id', 'values'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'values' => 'array'
    ];


    /**
     * Form reference
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function form(){
        return $this->hasOne(Form::class, 'id', 'form_id');
    }


    /**
     * Get values with field names
     *
     * @param Form|null $form Optional, already loaded form
     * @return array Array of stdClass objects with properties [name] and [value]
     */
    public function getNamedValues(Form $form = null){
        $fieldsScope = is_null($form) ? $this->form->fields() : $form->fields();
        $fields = $fieldsScope->withTrashed()->get();

        $namedValues = [];
        foreach($this->values as $fieldId => $value){
            $field = $fields->find($fieldId);
            if($field && $field->type !== 'file'){
                $namedValues[] = (object)[
                    'name' => $field->name,
                    'value' => $value
                ];
            }
        }

        return $namedValues;
    }


    /**
     * Get files from response
     *
     * @param Form|null $form Optional, already loaded form
     * @return array Array of stdClass objects with properties [name] and [file]
     */
    public function getFiles(Form $form = null){
        $fieldsScope = is_null($form) ? $this->form->fields() : $form->fields();
        $fields = $fieldsScope->withTrashed()->get();
        $files = [];

        /** @var Field $fileField */
        foreach($fields->where('type', 'file') as $fileField){
            if(!isset($this->values[$fileField->id])) continue;

            $fileInfo = $this->values[$fileField->id];
            $fileField->setFileName($fileInfo['file']);

            if (\File::exists($fileField->file_path)) {
                $files[] = (object)[
                    'id' => $fileField->id,
                    'name' => $fileInfo['name'],
                    'file' => $fileField->file_path,
                ];
            }
        }

        return $files;
    }
}
