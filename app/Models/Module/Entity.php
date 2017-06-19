<?php

namespace App\Models\Module;

use App\Models\Interfaces\ModuleConfigurationInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    /**
     * Model table
     *
     * @var string
     */
    protected $table = 'module_entities';

    /**
     * Mass assignable attributes
     *
     * @var array
     */
    protected $fillable = [ 'module', 'enabled' ];

    /**
     * Configuration cached.
     * @var mixed
     */
    private $cachedConfiguration;


    /**
     * Get entity configuration.
     * @cached
     * @return ModuleConfigurationInterface
     */
    public function getConfiguration(){
        if (!$this->cachedConfiguration) {
            $this->cachedConfiguration = $this->getModule()
                ->getConfiguration($this->configuration_id);
        }

        return $this->cachedConfiguration;
    }


    /**
     * Create new entity configuration.
     * @param array $attributes
     * @return mixed|null
     */
    public function createConfiguration(array $attributes) {
        $module = $this->getModule();

        if ($class = $module->getConfigurationClass()) {
            $newConfiguration = new $class();
            $newConfiguration->inputFill($attributes);
            $newConfiguration->save();
            $this->configuration_id = $newConfiguration->id;
            return $this->cachedConfiguration = $newConfiguration;
        }

        return null;
    }


    /**
     * Render entity content
     *
     * @return mixed
     */
    public function render($options = []){
        return $this->getConfiguration()->render($options);
    }


    /**
     * Get module
     *
     * @return \Module
     */
    public function getModule () {
        return \Module::findOrFail($this->module);
    }


    /**
     * Create new version of configuration.
     *
     * @param int $pageContentId
     * @param array $configuration - Configuration options.
     * @return Entity - Returns new entity.
     */
    public function duplicateForNextVersion($pageContentId, array $configuration = null) {

        $newEntity = new Entity([
            'module' => $this->module,
            'enabled' => true
        ]);

        $newConfiguration = $this->duplicateConfigurationForNextVersion($configuration);

        $newEntity->previous_entity_id = $this->id;
        $newEntity->page_content_id = $pageContentId;
        $newEntity->configuration_id = $newConfiguration->id;

        $newEntity->save();
        return $newEntity;
    }


    /**
     * Duplicate entity configuration.
     *
     * @return mixed - Created configuration.
     */
    private function duplicateConfigurationForNextVersion (array $configuration = null) {
        /** @var ModuleConfigurationInterface $newConfiguration */
        $newConfiguration = $this->getConfiguration()->replicate();

        if ($configuration) {
            $newConfiguration->inputFill($configuration);
        }

        $newConfiguration->created_at = Carbon::now();
        $newConfiguration->updated_at = $newConfiguration->created_at;
        $newConfiguration->save();

        return $newConfiguration;
    }


    /**
     * Render entity preview
     *
     * @param mixed $customConfiguration
     * @return mixed
     */
    public function renderPreview($customConfiguration = null){
        $configuration = $customConfiguration ?: $this->getConfiguration();
        return $this->getModule()->view('module_preview', [
            'configuration' => $configuration
        ])->render();
    }
}
