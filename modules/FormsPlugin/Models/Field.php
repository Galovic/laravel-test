<?php

namespace Modules\FormsPlugin\Models;

use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Field extends Model
{
    use SoftDeletes, AdvancedEloquentTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'module_form_fields';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type', 'options', 'language_id'
    ];

    /**
     * The attributes that are dates
     *
     * @var array
     */
    public $dates = [ 'deleted_at' ];


    /**
     * All fields named like $formNameKey[$id]
     *
     * @var string
     */
    protected $formNameKey = "input";


    /**
     * @var string
     */
    private $fileName;


    /**
     * Select only language mutations
     *
     * @param $query
     * @param mixed $language
     */
    public function scopeWhereLanguage($query, $language) {
        if (!$this->uniqueScope('whereLanguage')) {
            return;
        }

        $query->where("{$this->table}.language_id", is_scalar($language) ? $language : $language->id);
    }


    /**
     * Language
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function language(){
        return $this->hasOne('App\Models\Web\Language', 'id', 'language_id');
    }


    /**
     * Get form name
     *
     * @return string
     */
    public function getFormNameAttribute(){
        return "{$this->formNameKey}[{$this->id}]";
    }


    /**
     * Get field error key
     *
     * @return string
     */
    public function getErrorKeyAttribute(){
        return "{$this->formNameKey}.{$this->id}";
    }


    /**
     * Get file name
     *
     * @return string
     */
    public function getFileNameAttribute(){
        if (!$this->fileName) {
            $this->fileName = time() . ".file";
        }
        return $this->fileName;
    }


    /**
     * Create path to file
     *
     * @return string
     */
    public function getFilePathAttribute(){
        return $this->storage_path . "/" . $this->file_name;
    }


    /**
     * Get file storage path
     *
     * @return string
     */
    public function getStoragePathAttribute(){
        return storage_path("app/modules/forms/{$this->form_id}/{$this->id}");
    }


    /**
     * Set file name
     *
     * @param string $fileName
     */
    public function setFileName($fileName){
        $this->fileName = $fileName;
    }


    /**
     * Get options
     *
     * @param $value
     * @return mixed
     */
    public function getOptionsAttribute($value){
        return json_decode($value);
    }


    /**
     * Get rules for input of this field
     *
     * @return array
     */
    public function getRulesAttribute(){
        $fieldRules = [];

        $options = $this->options;

        // Required
        if(isset($options->required) && $options->required){
            $fieldRules[] = 'required';
        }

        // Maxlength
        if(isset($options->maxlength) && $options->maxlength){
            $fieldRules[] = 'max:' . intval($options->maxlength);
        }

        // Email
        if($this->type === 'email'){
            $fieldRules[] = 'email';
        }

        if($this->type === 'file'){
            $fieldRules[] = 'file';
            if(isset($options->accept) && $options->accept){
                $accept = array_map(
                    function($value){
                        return preg_replace('/[^a-z0-9]/', '', $value);
                    },
                    explode(',', $options->accept)
                );
                $fieldRules[] = 'mimes:' . join(',', $accept);
            }
        }

        return $fieldRules;
    }


    /**
     * Merged options
     *
     * @param array $options
     * @return array
     */
    public function getMergedOptions(array $options){
        if(!$this->options) return $options;
        $merge = array_merge($fieldOptions = (array)$this->options, $options);

        if(isset($fieldOptions['class']) && isset($options['class'])){
            $merge['class'] .= ' ' . $options['class'];
        }

        return $merge;
    }
}
