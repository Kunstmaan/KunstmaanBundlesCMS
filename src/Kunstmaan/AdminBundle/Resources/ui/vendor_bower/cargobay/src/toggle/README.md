## Cargo Bay Toggle

### General
- Toggle snippet
- jQuery and vanilla js version

### Dependencies
- [jQuery](http://jquery.com/) (for jQuery version)
- [Velocity.js](http://julian.com/research/velocity/)


### Initialise
```javascript
cargobay.toggle.init();
```

### Usage

This snippet is intended to quickly toggle the state of a component. (For example the opening/closing of a menu.)

The class **js-toggle-btn** is used as a javascript hook to identify the control button. This button also uses the **data-target** attribute to identify the element that it should control. Its value can be any css selector pointing to the **toggle-item**.
Additional data attributes for the **js-toggle-btn** are the following:

 - **data-hide-others**: Boolean to define if a click on this button should close other open items on the same level. Using this attribute also requires you to use the **data-level** attribute. ( See below ).
 - **data-level**: Used with the **data-hide-others** attribute to identify buttons on the same level.
 - **data-duration**: Use this attribute if you want to change the default duration of the animation. Time is in ms and can be unique for each button. ( Default is 150ms )

The **toggle-item** should be a wrapper for the **toggle-item__content** component.

Inside the button you can optionally have a set of icons to illustrate the state of  the item you are hiding/showing. You can toggle these by applying the **toggle-btn__icon--show** (visible by default) and **toggle-btn__icon--hide** (shown when toggled) classes.

```html
<div class="container">
    <h1>Cargo Bay Toggle - jQuery</h1>


	<button class="js-toggle-btn toggle-button toggle-button-demo" data-target="#main-navigation--tabs">
	    Toggle Me!
	    <span class="icon--chevron-down toggle-btn__icon--show"></span>
	    <span class="icon--chevron-up toggle-btn__icon--hide"></span>
	</button>

	<div id="main-navigation--tabs" class="toggle-item toggle-item-demo">
	    <div class="toggle-item__content">
		<p>
		    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quisquam magni inventore nisi enim numquam, accusamus tempore voluptates possimus amet quod aspernatur ea, nulla, sapiente non facere quidem laudantium illo ipsam dolor aliquam dignissimos? Ad non, itaque blanditiis cum impedit, porro dolor nobis. Cupiditate debitis beatae labore, suscipit, dolorem nam omnis?
		</p>
		<p>
		    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quisquam magni inventore nisi enim numquam, accusamus tempore voluptates possimus amet quod aspernatur ea, nulla, sapiente non facere quidem laudantium illo ipsam dolor aliquam dignissimos? Ad non, itaque blanditiis cum impedit, porro dolor nobis. Cupiditate debitis beatae labore, suscipit, dolorem nam omnis?
		</p>
	    </div>
	</div>
</div>
```

### Support
- Latest Chrome
- Latest FireFox
- Latest Safari
- IE9 (IE10 for vanilla version) and up
