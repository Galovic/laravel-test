<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PageVersioningTablesAndAlters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // #1 CREATE PAGE CONTENTS TABLE
        Schema::create('page_contents', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('page_id');
            $table->foreign('page_id')->references('id')->on('pages')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->text('content')->nullable()->default(null);

            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('author_user_id')->nullable()->default(null);
            $table->foreign('author_user_id')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('SET NULL');

            $table->softDeletes();
            $table->timestamps();
        });

        // #2 RENAME TABLE modules TO module_entities
        Schema::rename('modules', 'module_entities');

        // #3 ADD REFERENCE TO PAGE CONTENT AND TO ITS PREVIOUS VERSION
        Schema::table('module_entities', function (Blueprint $table) {
            $table->unsignedInteger('page_content_id')
                ->nullable()->default(null);
            $table->foreign('page_content_id')
                ->references('id')->on('page_contents')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('previous_entity_id')
                ->nullable()->default(null);
            $table->foreign('previous_entity_id')
                ->references('id')->on('module_entities')
                ->onUpdate('cascade')->onDelete('SET NULL');
        });

        // #4 MOVE PAGE CONTENTS TO PAGE CONTENTS TABLE
        $pagesDb = DB::table('pages')->get(['id', 'content']);
        $regex = '/data-module-id=["\'](?<entity>\d+)["\']/';
        foreach ($pagesDb as $page) {
            $content = preg_replace('~\s+~', ' ', trim($page->content));
            $content = preg_replace('~>\s+<div~', '><div', $content);
            $content = preg_replace('~>\s+</div~', '></div', $content);
            $contentId = DB::table('page_contents')->insertGetId([
                'page_id' => $page->id,
                'content' => $content,
                'is_active' => 1,
                'created_at' => $now = \Carbon\Carbon::now(),
                'updated_at' => $now
            ]);

            $matches = [];
            preg_match_all($regex, $content, $matches, PREG_PATTERN_ORDER, 0);

            DB::table('module_entities')->whereIn('id', $matches['entity'])
                ->update([
                    'page_content_id' => $contentId
                ]);
        }

        // #5 DROP COLUMN "CONTENT" FROM PAGES TABLE
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['content']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // #1 RESTORE COLUMN "CONTENT" IN PAGES TABLE
        Schema::table('pages', function (Blueprint $table) {
            $table->text('content')->nullable()->default(null);
        });

        // #2 MOVE PAGE CONTENTS TO PAGE TABLE
        $pageContentsDb = DB::table('page_contents')->get(['id', 'content', 'page_id']);
        foreach ($pageContentsDb as $pageContent) {
            DB::table('pages')->where('id', $pageContent->page_id)->update([
                'content' => $pageContent->content
            ]);
        }

        // #3 REMOVE REFERENCE TO PAGE CONTENT AND PREVIOUS VERSION
        Schema::table('module_entities', function (Blueprint $table) {
            $table->dropForeign(['page_content_id']);
            $table->dropForeign(['previous_entity_id']);
            $table->dropColumn(['page_content_id', 'previous_entity_id']);
        });

        // #4 RENAME TABLE module_entities TO modules
        Schema::rename('module_entities', 'modules');

        // #5 DROP PAGE CONTENTS TABLE
        Schema::dropIfExists('page_contents');
    }
}
