/* Load this script using conditional IE comments if you need to support IE 7 and IE 6. */

window.onload = function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'demosite\'">' + entity + '</span>' + html;
	}
	var icons = {
			'icon-home' : '&#xf015;',
			'icon-chevron-up' : '&#xf077;',
			'icon-chevron-down' : '&#xf078;',
			'icon-chevron-left' : '&#xf053;',
			'icon-chevron-right' : '&#xf054;',
			'icon-remove' : '&#xf00d;',
			'icon-reorder' : '&#xf0c9;',
			'icon-search' : '&#xf002;',
			'icon-twitter' : '&#xf099;',
			'icon-github' : '&#xf09b;',
			'icon-Untitled-1' : '&#xe000;',
			'icon-linkedin' : '&#xf0e1;',
			'icon-facebook' : '&#xf09a;'
		},
		els = document.getElementsByTagName('*'),
		i, attr, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		attr = el.getAttribute('data-icon');
		if (attr) {
			addIcon(el, attr);
		}
		c = el.className;
		c = c.match(/icon-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
};