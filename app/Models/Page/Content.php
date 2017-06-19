<?php

namespace App\Models\Page;

use App\Models\Module\Entity;
use App\Models\User;
use App\Traits\AdvancedEloquentTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use AdvancedEloquentTrait, SoftDeletes;

    protected $table = 'page_contents';

    protected $fillable = [ 'content', 'is_active' ];

    /**
     * Entities in content.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entities() {
        return $this->hasMany(Entity::class, 'page_content_id', 'id');
    }


    /**
     * Author of content version.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author() {
        return $this->hasOne(User::class, 'id', 'author_user_id');
    }


    /**
     * Set content attribute - replace whitespaces.
     *
     * @param $value
     */
    public function setContentAttribute($value) {
        $content = preg_replace('~\s+~', ' ', trim($value));
        $content = preg_replace('~>\s+<div~', '><div', $content);
        $content = preg_replace('~>\s+</div~', '></div', $content);

        $this->attributes['content'] = $content;
    }


    /**
     * Replace id of existing entity in content for different entity id.
     *
     * @param int $originalEntityId
     * @param int $replaceEntityId
     */
    public function replaceEntityId($originalEntityId, $replaceEntityId) {
        $this->content = preg_replace(
            "/data-module-id=[\"']{$originalEntityId}[\"']/",
            'data-module-id="' . $replaceEntityId . '"',
            $this->content
        );
    }


    /**
     * Replace id of existing entity in content for different entity id.
     *
     * @param string $tempId
     * @param int $entityId
     */
    public function replaceTempId($tempId, $entityId) {
        $this->content = preg_replace(
            "/data-temp-id=[\"']{$tempId}[\"']/",
            'data-module-id="' . $entityId . '"',
            $this->content
        );
    }


    /**
     * Set current content active.
     */
    public function setActive() {
        self::where('page_id', $this->page_id)
            ->where('id', '<>', $this->id)
            ->update([
            'is_active' => false
        ]);

        $this->update([
            'is_active' => true
        ]);
    }


    /**
     * Replicate content with all entities.
     *
     * @param array $attributes
     * @return Content
     */
    public function replicateFull(array $attributes = null) {
        /** @var Content $newContent */
        $newContent = $this->replicate();
        if ($attributes) {
            $newContent->forceFill($attributes);
        }
        $newContent->push();

        foreach ($this->entities as $entity) {
            $newEntity = $entity->duplicateForNextVersion($newContent->id);
            $newEntity->previous_entity_id = null;
            $newEntity->save();

            $newContent->replaceEntityId($entity->id, $newEntity->id);
        }

        $newContent->save();

        return $newContent;
    }
}
