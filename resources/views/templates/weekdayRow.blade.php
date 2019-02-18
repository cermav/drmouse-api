<script id="weekdayRowTemplate" type="text/x-handlebars-template">
    <div class="formRow asTableRow">
        <span class="col col-2-of-12"></span>
        <select id="weekday-@{{weekday}}-1" name="weekdays[@{{weekday}}][1][state]" class="weekdaySelect col col-3-of-12">
            @foreach ($openingHoursStates as $state)
            <option value="{{$state->id}}">{{$state->name}}</option>
            @endforeach
        </select>
        <input type="time" name="weekdays[@{{weekday}}][1][open_at]" class="col col-3-of-12" />
        <input type="time" name="weekdays[@{{weekday}}][1][close_at]" class="col col-3-of-12" />
        <button type="button" class="actionButton col col-1-of-12 removeWeekdayRow">-</button>
    </div>
</script>