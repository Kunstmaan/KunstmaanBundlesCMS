## Cargo Bay Sidebar Toggle

### General
- Show/hide a hidden sidebar
- jQuery and vanilla js version

### Dependencies
- [jQuery](http://jquery.com/) (for jQuery version)
- [Velocity.js](http://julian.com/research/velocity/)

### Initialise
```javascript
cargobay.sidebarToggle.init();
```

### Usage

This add-on allows you to easely show and hide a hidden sidebar. You can place the sidebar at any side (top/right/bottom/left).

#### Js-classes
The class **js-sidebar-toggle__toggle-btn** is used as a javascript hook to identify the toggle-button of the sidebar.

#### Css-classes
The class **sidebar-toggle-container** is used to identify the container (used for disabling overflow), the class **sidebar-toggle__side-bar** is used to identify the sidebar and **sidebar-toggle__content** is used to identify your content wrapper. **sidebar-toggle__sidebar--[top|right|bottom|left]** is used to place your sidebar on a specific position. **sidebar-toggle-container--prevent-overflow** prevents overflow of your container when sidebar is shown.

#### Config
All the config is done on the button with data-attributes:
- **data-content**: Put a id or class here to identify your content
- **data-sidebar**: Put a id or class here to identify your sidebar
- **data-container**: Put a id or class here to identify your content
- **data-position**: [top|right|bottom|left], make sure to place the complementary class on the sidebar (**sidebar-toggle__sidebar--[position]**)
- **data-prevent-overflow**: [true|false], prevent overflow on container when sidebar is shown?
- **data-duration**: Duration of animation

#### Available SCSS-variables
**$sidebar--left__width:** 60%!default;<br>
**$sidebar--left__max-width:** 30rem!default;

**$sidebar--right__width:** 60%!default;<br>
**$sidebar--right__max-width:** 30rem!default;

**$sidebar--top__height:** auto!default;<br>
**$sidebar--top__max-height:** 80%!default;

**$sidebar--bottom__height:** auto!default;<br>
**$sidebar--bottom__max-height:** 80%!default;

#### Html-example

```html
<!-- Container -->
<body id="sidebar-toggle-container" class="sidebar-toggle-container">
    <!-- Sidebar -->
    <aside id="sidebar-toggle__sidebar" class="sidebar-toggle__sidebar sidebar-toggle__sidebar--left">
        ...
    </aside>

    <!-- Main content -->
    <main role="main" id="sidebar-toggle__content" class="sidebar-toggle__content">
        <!-- Toggle Button -->
        <button type="button" class="js-sidebar-toggle__toggle-btn sidebar-toggle__toggle-btn" data-content="#sidebar-toggle__content" data-sidebar="#sidebar-toggle__sidebar" data-container="#sidebar-toggle-container" data-position="left" data-prevent-overflow="false" data-duration="300">
            Toggle Button
        </button>

        ...
    </main>
</body>
```

### Support
- Latest Chrome
- Latest FireFox
- Latest Safari
- IE9 (IE10 for vanilla version) and up
