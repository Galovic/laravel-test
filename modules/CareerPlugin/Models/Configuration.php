<?php

namespace Modules\CareerPlugin\Models;

use App\Models\Interfaces\ModuleConfigurationInterface;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model implements ModuleConfigurationInterface
{
    /**
     * @var string Table name of the model
     */
    protected $table = 'module_career_configurations';

    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = [ 'show_number', 'view' ];


    /**
     * Get default configuration
     *
     * @return Configuration
     */
    static function getDefault(){
        return new self([
            'show_number' => 10
        ]);
    }


    /**
     * Scope to careers
     *
     * @return mixed
     */
    public function careers(){
        return with(new Career);
    }


    /**
     * Render module
     *
     * @return string
     */
    public function render($options){
        return view($this->view, [
            'configuration' => $this,
            'careers' => $this->careers()->whereLanguage($options['language_id'])->get()
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
