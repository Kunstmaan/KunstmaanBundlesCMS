## Cargo Bay Has-space

### General
- Calculates if items in a given container have enough space to stand next to eachother.
- jQuery and vanilla-ja version


### Dependencies
- jQuery (for jQuery version)


### Initialise
```javascript
cargobay.hasSpace.init();
```


### Usage
The class **'.js-has-space'** is used as the javascript-hook for the container.
The class **'.js-has-space__item'** is used as the javascript-hook for the items that are in the container.
The class **'.js-has-space__item--hidden'** is used as the javascript-hook to exclude the item from the total needed width calculation.

With the attribute **data-space-hook-target="your_class_or_id_here"** you can hook on to an element that will be used to calculcate the available space.

The classes **'.has-space--space'** and **'.has-space--no-space'** are used to define the state. This class is set on the container you defined with the class **'.js-has-space'**.


#### Demo 1 - Class as space-hook-target
```html
<nav role="navigation" data-space-hook-target=".js-has-space__space-hook--nav-1" class="js-has-space js-has-space__space-hook--nav-1 demo-nav">
    <a href="#" class="js-has-space__item demo-nav__item">Item</a>
    <a href="#" class="js-has-space__item demo-nav__item">Item</a>
    <a href="#" class="js-has-space__item demo-nav__item">Item</a>
</nav>
```

#### Demo 2 - Id as space-hook-target, has hidden items
```html
<nav role="navigation" id="has-space__space-hook--nav2" data-space-hook-target="#has-space__space-hook--nav2" class="js-has-space demo-nav">
    <a href="#" class="js-has-space__item demo-nav__item">Item</a>
    <a href="#" class="js-has-space__item demo-nav__item">Item</a>
    <a href="#" class="js-has-space__item js-has-space__item--hidden demo-nav__item demo-nav__item--hidden">Item</a>
    <a href="#" class="js-has-space__item js-has-space__item--hidden demo-nav__item demo-nav__item--hidden">Item</a>
</nav>
```

#### Demo 3 - External class as space-hook-target
```html
<nav role="navigation" data-space-hook-target=".has-space__space-hook--nav3" class="js-has-space demo-nav">
    <a href="#" class="js-has-space__item demo-nav__item">Item</a>
    <a href="#" class="js-has-space__item demo-nav__item">Item</a>
    <a href="#" class="js-has-space__item demo-nav__item">Item</a>
</nav>
<p class="has-space__space-hook--nav3 demo-nav-external-spacehook">
    External Spacehook for demo 3
</p>
```


### Support
- Latest Chrome
- Latest FireFox
- Latest Safari
- IE9 (IE10 for vanilla version) and up
