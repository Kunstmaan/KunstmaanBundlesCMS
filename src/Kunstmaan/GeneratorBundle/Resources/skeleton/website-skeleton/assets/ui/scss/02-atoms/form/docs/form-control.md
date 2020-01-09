<b>Description:</b>
<br>
<br>
<b>Acceptable use:</b>
```html
@example
<input class="form-control form-control--input" type="text" placeholder="Input Field">
<br>
<div class="form-control-icon">
    <input class="form-control form-control--input" type="text" placeholder="Input Field with icon">
    <svg class="form-control-icon__icon"><use xlink:href="../icons/symbol-defs.svg#icon--star"></use></svg>
</div>
<br>
<textarea class="form-control form-control--textarea"></textarea>
<br>
<div class="form-control-select form-control-icon">
    <select name="choose" id="choose" class="form-control form-control--select">
        <option value="list1">List item 1</option>
        <option value="list2">List item 2</option>
        <option value="list3">List item 3</option>
        <option value="list4">List item 4</option>
        <option value="list5">List item 5</option>
    </select>
    <svg class="form-control-icon__icon"><use xlink:href="../icons/symbol-defs.svg#icon--dropdown"></use></svg>
</div>
<br>
<div class="form-control-upload">
    <label class="form-control-upload__label form-control-label">
        Drag files here
    </label>
    <svg class="form-control-upload__icon">
        <use xlink:href="../icons/symbol-defs.svg#icon--add"></use>
    </svg>
    <input type="file" class="form-control form-control--upload">
</div>
```

```html
@code
<input class="form-control form-control--input" type="text" placeholder="Input Field">

<div class="form-control-icon">
    <input class="form-control form-control--input" type="text" placeholder="Input Field with icon">
    <svg class="form-control-icon__icon"><use xlink:href="../icons/symbol-defs.svg#icon--star"></use></svg>
</div>

<textarea class="form-control form-control--textarea"></textarea>

<div class="form-control-select form-control-icon">
    <select name="choose" id="choose" class="form-control form-control--select">
        <option value="list1">List item 1</option>
        <option value="list2">List item 2</option>
        <option value="list3">List item 3</option>
        <option value="list4">List item 4</option>
        <option value="list5">List item 5</option>
    </select>
    <svg class="form-control-icon__icon"><use xlink:href="../icons/symbol-defs.svg#icon--dropdown"></use></svg>
</div>

<div class="form-control-upload">
    <label class="form-control-upload__label form-control-label">
        Drag files here
    </label>
    <svg class="form-control-upload__icon">
        <use xlink:href="../icons/symbol-defs.svg#icon--add"></use>
    </svg>
    <input type="file" class="form-control form-control--upload">
</div>
```