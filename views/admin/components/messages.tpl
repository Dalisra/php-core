
{if isset($messages['success'])}
    {foreach from=$messages['success'] item=message}
        <div class="alert alert-success" role="alert">{$message}</div>
    {/foreach}
{/if}

{if isset($messages['info'])}
    {foreach from=$messages['info'] item=message}
        <div class="alert alert-info" role="alert">{$message}</div>
    {/foreach}
{/if}

{if isset($messages['warning'])}
    {foreach from=$messages['warning'] item=message}
        <div class="alert alert-warning" role="alert">{$message}</div>
    {/foreach}
{/if}

{if isset($messages['error'])}
    {foreach from=$messages['error'] item=message}
        <div class="alert alert-danger" role="alert">{$message}</div>
    {/foreach}
{/if}