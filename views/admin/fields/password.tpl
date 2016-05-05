{if $edit}
    <div class="form-group">
        <label>{$field->title}:</label>
        <div class="input-group">
            <span class="input-group-addon">
                <input type="checkbox" onchange="enablePasswordChange('password{$field->name}')">
            </span>
            <script>
                function enablePasswordChange(id){
                    var elem = document.getElementById(id);
                    elem.disabled = !elem.disabled;
                }
            </script>
            <input id="password{$field->name}" type="password" class="form-control" placeholder="{$field->title}" name="{$field->name}" maxlength="255" disabled>
        </div>
    </div><!-- /input-group -->
{else}
    Passord vises ikke.
{/if}