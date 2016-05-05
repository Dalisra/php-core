{extends file="admin/layouts/default.tpl"}
{block name=title}{$page->title}{/block}
{block name=content}
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">{$page->title} {$controller_url}</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="{$site_url}{$controller_url}/{$page->url}/edit"><i class="fa-2x fa fa-plus-circle"></i> Legg til nytt element</a>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-list">
                            <thead>
                            <tr>
                                {foreach from=$page->listFields item=field}
                                    {if $field->visibleInList}
                                        <th>{$field->title}</th>
                                    {/if}
                                {/foreach}
                                <th>Styring</th>
                            </tr>
                            </thead>
                                <tbody>
                                    {foreach from=$items item=item}
                                        <tr id='{$page->name}-{$item.id}'>
                                            {foreach from=$page->listFields item=field}
                                                {if $field->visibleInList}
                                                    <td>
                                                        {assign var="templateName" value=$field->type}
                                                        {include file="admin/fields/$templateName.tpl" edit=false}
                                                    </td>
                                                {/if}
                                            {/foreach}
                                            <td>
                                                <a title="Rediger" href="{$site_url}{$controller_url}/{$page->url}/edit?id={$item.id}"><i class='fa fa-edit'></i></a>
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <a title="SLETT" href='#/' onclick="deleteRow('{$page->name}', '{$page->url}', '{$item.id}');" style="color:red;"><i class='fa fa-trash'></i></a>
                                            </td>
                                        </tr>
                                    {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
{/block}


{block name="scripts"}
    <!-- DataTables JavaScript -->
    <script src="{$site_url}admin/bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
    <script src="{$site_url}admin/bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>

    <!-- Page-Level Demo Scripts - Tables - Use for reference -->
    <script>
        $(document).ready(function() {
            $('#dataTables-list').DataTable({
                responsive: true,
                "language": {
                    "url": "{$site_url}json/dataTable-" + "no" + ".json"
                }
            });
        });
    </script>

    <script>
        function deleteRow(name, url, id) {
            event.preventDefault();
            var sure = window.confirm("Er du sikker på at du vil slette dette element?");
            if(sure == true) {
                $.ajax({
                    url: "{$site_url}{$controller_url}/" + url + '/delete?id=' + id,
                    success: function (data) {
                        $('#' + name + "-" + id).remove();
                    }
                });
            }
        }
    </script>
{/block}