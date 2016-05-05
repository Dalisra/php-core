{if $edit}
    <div class="form-group">
        {if isset($item) && isset($item[$field->name])}
            <img src='{$site_url}{$item[$field->name]}' style="max-height: 100px; max-width: 100px;"/>
        {/if}
        <p class="help-block">Last opp nytt {$field->title}:</p>
        <input type="file" name="{$field->name}" id="{$field->name}"/>
    </div>
{else}
    {if isset($item) && isset($item[$field->name])}
        <img src='{$site_url}{$item[$field->name]}' style="max-height: 500px; max-width: 500px;"/>
    {else}
        <i class="fa fa-image fa-5x"></i>
    {/if}

{/if}