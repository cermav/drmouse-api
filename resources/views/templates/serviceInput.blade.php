<script id="serviceInputTemplate" type="text/x-handlebars-template">
    <div class="formRow col col-6-of-12">
        <label for="service-price-@{{id}}" class="formRowTitle">@{{name}}:</label>
        <input type="number" name="service_prices[@{{id}}]" id="service-price-@{{id}}" />
        <div class="preValue after">Kč</div>
    </div>
</script>