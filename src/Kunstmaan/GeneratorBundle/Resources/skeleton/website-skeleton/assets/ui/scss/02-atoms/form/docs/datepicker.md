<b>Description:</b>
<br>
<br>
<b>Acceptable use:</b>
<br>
- Default min-day is today, can be overwritten using a data attribute: <code>data-minDate="dd-mm-yyyy"</code>
- Default max-day is null, can be overwritten using a data attribute: <code>data-maxDate="dd-mm-yyyy"</code>

```html
@example
<div class="datepicker js-datepicker">
    <input type="text" class="form-control datepicker__input js-datepicker-input" placeholder="pick a date" data-input>
    <div class="datepicker__controls">
        <button class="datepicker__controls__control datepicker__controls__control--open" data-toggle>
            <svg class="datepicker__controls__control__icon"><use xlink:href="../icons/symbol-defs.svg#icon--date"></use></svg>
        </button>
        <button class="datepicker__controls__control datepicker__controls__control--close js-datepicker-control-close" data-clear>
            <svg class="datepicker__controls__control__icon"><use xlink:href="../icons/symbol-defs.svg#icon--close"></use></svg>
        </button>
    </div>
</div>
<br>
<div class="datepicker datepicker--range js-datepicker" data-range>
    <div class="datepicker__range">
        <input type="text" class="form-control datepicker__input js-datepicker-input" placeholder="From" data-input>
        <input type="text" class="form-control datepicker__input js-datepicker-input" placeholder="Until" data-input>
    </div>
    <div class="datepicker__controls">
        <button class="datepicker__controls__control datepicker__controls__control--open" data-toggle>
            <svg class="datepicker__controls__control__icon"><use xlink:href="../icons/symbol-defs.svg#icon--date"></use></svg>
        </button>
        <button class="datepicker__controls__control datepicker__controls__control--close js-datepicker-control-close" data-clear>
            <svg class="datepicker__controls__control__icon"><use xlink:href="../icons/symbol-defs.svg#icon--close"></use></svg>
        </button>
    </div>
</div>
```

```html
@code
<div class="datepicker js-datepicker">
    <input type="text" class="form-control datepicker__input js-datepicker-input" placeholder="pick a date" data-input>
    <div class="datepicker__controls">
        <button class="datepicker__controls__control datepicker__controls__control--open" data-toggle>
            <svg class="datepicker__controls__control__icon"><use xlink:href="../icons/symbol-defs.svg#icon--date"></use></svg>
        </button>
        <button class="datepicker__controls__control datepicker__controls__control--close js-datepicker-control-close" data-clear>
            <svg class="datepicker__controls__control__icon"><use xlink:href="../icons/symbol-defs.svg#icon--close"></use></svg>
        </button>
    </div>
</div>

<div class="datepicker datepicker--range js-datepicker" data-range>
    <div class="datepicker__range">
        <input type="text" class="form-control datepicker__input js-datepicker-input" placeholder="From" data-input>
        <input type="text" class="form-control datepicker__input js-datepicker-input" placeholder="Until" data-input>
    </div>
    <div class="datepicker__controls">
        <button class="datepicker__controls__control datepicker__controls__control--open" data-toggle>
            <svg class="datepicker__controls__control__icon"><use xlink:href="../icons/symbol-defs.svg#icon--date"></use></svg>
        </button>
        <button class="datepicker__controls__control datepicker__controls__control--close js-datepicker-control-close" data-clear>
            <svg class="datepicker__controls__control__icon"><use xlink:href="../icons/symbol-defs.svg#icon--close"></use></svg>
        </button>
    </div>
</div>
```