<?php

namespace Modules\FormsPlugin\Models;

use App\Models\Web\Language;
use App\Models\Web\Url;
use App\Models\Web\ViewData;
use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    use SoftDeletes, AdvancedEloquentTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'module_forms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'is_ajax', 'send_to_email'
    ];

    /**
     * The attributes that are dates
     *
     * @var array
     */
    public $dates = [ 'deleted_at' ];

    /**
     * Attributes set to null when empty
     *
     * @var array
     */
    protected $nullIfEmpty = [ 'send_to_email' ];

    /**
     * @var Language
     */
    protected $language;


    /**
     * Relation with user
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }


    /**
     * Relation with fields
     */
    public function fields()
    {
        $relation =  $this->hasMany(Field::class, 'form_id');

        if ($this->language) {
            $relation->whereLanguage($this->language);
        }

        return $relation;
    }

    /**
     * Relation with fields
     */
    public function localisation()
    {
        $relation =  $this->hasMany(Localisation::class, 'form_id');

        if ($this->language) {
            $relation->whereLanguage($this->language);
        }

        return $relation;
    }


    /**
     * Relation with fields
     */
    public function getLocalisedAttribute()
    {
        $relation = $this->localisation->first();
        return $relation ?: new Localisation();
    }


    /**
     * Relation with responses
     */
    public function responses()
    {
        return $this->hasMany(Response::class, 'form_id', 'id');
    }


    /**
     * Relation with fields
     */
    public function languageFields($language)
    {
        return $this->hasMany(Field::class, 'form_id')
            ->whereLanguage($language);
    }


    /**
     * Form submit action
     *
     * @return string
     */
    public function getActionAttribute(){
        return route('module.formsplugin.submit', $this->id);
    }


    /**
     * Form parameters
     *
     * @return array
     */
    public function getParameters($language){
        return [
            'method' => 'POST',
            'url' => $this->action,
            'files' => $this->hasFiles($language)
        ];
    }


    /**
     * Get validation rules
     *
     * @param $language
     * @return array
     */
    public function getValidationRules($language){
        $rules = [];

        foreach($this->languageFields($language)->get() as $field){
            $fieldRules = $field->rules;

            if(!$fieldRules) continue;

            $rules[$field->error_key] = join('|', $fieldRules);
        }

        return $rules;
    }


    /**
     * Save fields
     *
     * @param $fields
     */
    public function saveFields(array $fields, Language $language){
        $order = 1;
        $fieldIdsPreserve = [];

        foreach($fields as $fieldObject){

            $fieldData = [
                'name' => $fieldObject->name,
                'type' => $fieldObject->type,
                'language_id' => $language->id,
                'order' => isset($fieldObject->order) ? $fieldObject->order : 1,
                'options' => isset($fieldObject->options) && $fieldObject->options ? json_encode($fieldObject->options) : null,
            ];

            // Editing existing field
            if($fieldObject->id){
                $field = $this->fields->find($fieldObject->id);
                if(!$field) continue;
                $field->update($fieldData);
            }
            // Creating new field
            else{
                $field = $this->fields()->create($fieldData);
            }

            $fieldIdsPreserve[] = $field->id;

            $order++;
        }

        $this->fields()->whereLanguage($language)
            ->whereNotIn('id', $fieldIdsPreserve)->delete();
    }


    /**
     * Has files?
     *
     * @return boolean
     */
    private function hasFiles($language){
        return !!$this->languageFields($language)->where('type', 'file')->count();
    }


    /**
     * Set language to work with
     *
     * @param Language $language
     * @return $this
     */
    public function setLanguage(Language $language) {
        $this->language = $language;

        return $this;
    }

    /**
     * Get id of success flash message
     *
     * @return string
     */
    public function getSuccessMessageIdAttribute() {
        return 'form-success-' . $this->id;
    }
}
