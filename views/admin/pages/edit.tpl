{extends 'admin/layouts/default.tpl'}
{block name=title}{$page->title}{/block}
{block name=content}
    <div class="row">
        <div class="col-lg-12">
            {if isset($item)}
                <h1 class="page-header">Redigering av {$page->title} element #{$item.id}</h1>
            {else}
                <h1 class="page-header">Legge inn et nytt {$page->title} element</h1>
            {/if}
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->

    <div class="row">
        <div class="col-lg-12">
            <form role="form" method="post" action="{$site_url}{$controller_url}/{$page->url}/save" enctype="multipart/form-data">

                {if isset($item)}<input type="hidden" name="id" value="{$item.id}"/>{/if}

                {foreach from=$page->listFields item=field}
                    {if $field->name|in_array:APP_Field::$SYSTEM_FIELDS}
                        {* Do nothing *}
                    {elseif $field->visibleInEdit}
                        {assign var="templateName" value=$field->type}
                        {include file="admin/fields/$templateName.tpl" edit=true}
                    {else}
                        {if isset($request[$field->name])}
                            {* Hvis formen har preutfylt felt, skal man ikke vise det for bruker. *}
                            <input type='hidden' name='{$field->name}' value="{$request[$field->name]}" />
                        {else}
                            <div class="form-group">
                                <label>{$field->title}:</label>
                                <input type="text" class="form-control" placeholder="{$field->title}" name="{$field->name}" {if isset($item)}value="{$item[$field->name]}{/if}">
                            </div>
                            <hr/>
                        {/if}
                    {/if}
                {/foreach}
                {* TODO: make modal work *}
                <button type="submit" class="btn btn-default">Lagre</button>
            </form>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
{/block}