<script id="propertyInputTemplate" type="text/x-handlebars-template">
    <div class="formRow checkboxRow">
        <input type="checkbox" name="category_@{{categoryId}}_properties[]" id="property-@{{id}}" value="@{{id}}" checked />
        <label for="property-@{{id}}">@{{name}}</label>
    </div>
</script>