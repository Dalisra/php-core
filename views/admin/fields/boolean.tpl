{if $edit}
    <div class="form-group">
        <label>{$field->title}:</label>

        <div class="radio">
            <label>
                <input type="radio" name="{$field->name}" value="1" {if isset($item) && $item[$field->name] == 1}checked{/if}>Ja
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="{$field->name}" value="0" {if isset($item) && $item[$field->name] == 0}checked{/if}>Nei
            </label>
        </div>
    </div>
{else}
    {if $item[$field->name] == 1}
        <span style="color:green">Ja</span>
    {else}
        <span style="color:red">Nei</span>
    {/if}
{/if}








