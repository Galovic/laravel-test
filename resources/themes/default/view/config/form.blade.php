<div class="form-group">
    <label class="col-xs-4 control-label text-right">Stránka s články:</label>
    <div class="col-xs-7">
        {{ Form::select($language_code . '_articles_page_id', $pages, $articlesPageId, [
            'class' => 'form-control'
        ]) }}
    </div>
</div>