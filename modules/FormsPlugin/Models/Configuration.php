<?php

namespace Modules\FormsPlugin\Models;

use App\Models\Interfaces\ModuleConfigurationInterface;
use App\Models\Web\Language;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model implements ModuleConfigurationInterface
{
    /**
     * @var string Table name of the model
     */
    protected $table = 'module_formsplugin_configurations';

    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = [ 'view', 'form_id' ];


    /**
     * Form
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function form(){
        return $this->hasOne(Form::class, 'id', 'form_id');
    }


    /**
     * Get action url
     *
     * @return string
     */
    public function getFormActionAttribute(){
        return route('module.formsplugin.submit', $this->form_id);
    }


    /**
     * Get view name
     *
     * @return string
     */
    public function getViewName(){
        return $this->view ?: 'module-formsplugin:email.default';
    }


    /**
     * Render module
     *
     * @return string
     */
    public function render($options){
        $language = Language::find($options['language_id']);

        /** @var Form $form */
        $form = $this->form;
        $form->setLanguage($language);

        return view($this->view, [
            'configuration' => $this,
            'form' => $form
        ])->render();
    }

    /**
     * Fill model with input values with mutators inside.
     *
     * @param array $inputs
     * @return $this
     */
    public function inputFill(array $inputs)
    {
        return $this->fill($inputs);
    }
}
