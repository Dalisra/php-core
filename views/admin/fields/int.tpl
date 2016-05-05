{if $edit}
    <div class="form-group">
        <label>{$field->title}:</label>
        <input type="number" class="form-control" placeholder="{$field->title}" name="{$field->name}" {if isset($item)} value="{$item[$field->name]}"{/if}>
    </div>
{else}
    {$item[$field->name]}
{/if}