<?php

namespace Modules\View\Models;

use App\Models\Interfaces\ModuleConfigurationInterface;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model implements ModuleConfigurationInterface
{
    /**
     * @var string Table name of the model
     */
    protected $table = 'module_view_configurations';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'variables' => 'array',
    ];


    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = [ 'view', 'variables' ];


    /**
     * Get default configuration
     *
     * @return Configuration
     */
    static function getDefault(){
        return new self([
            'view' => ''
        ]);
    }


    /**
     * Render module
     *
     * @return string
     */
    public function render() {
        return view($this->view, $this->variables)->render();
    }


    /**
     * Fill model with input values with mutators inside.
     *
     * @param array $inputs
     * @return $this
     */
    public function inputFill(array $inputs)
    {
        if (isset($inputs['variable']) && $inputs['variable']) {
            $this->variables = (array)$inputs['variable'];
        } else {
            $this->variables = null;
        }

        return $this->fill($inputs);
    }
}