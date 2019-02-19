<script id="optionsTemplate" type="text/x-handlebars-template">
    <ul>
        @{{#options}}
        <li data-option-id=@{{id}} @{{#if property_category_id}}data-category-id=@{{property_category_id}}@{{/if}} data-option-name='@{{name}}' >
                @{{name}}
        </li>
        @{{/options}}
    </ul>
</script>