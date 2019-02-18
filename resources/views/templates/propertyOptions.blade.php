<script id="propertyOptionsTemplate" type="text/x-handlebars-template">
    <ul>
        @{{#properties}}
        <li data-property-id=@{{id}} data-category-id=@{{property_category_id}} data-property-name=@{{name}} >
                @{{name}}
        </li>
        @{{/properties}}
    </ul>
</script>