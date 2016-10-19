/*
	core -- A utility module. ... THE utility module.
	
	This module should be required just about everywhere.
	It contains all functionality needed globally by the app.
	It holds every basic component/model/module-of-any-sort.
	
	The module itself works by requiring 'base' (the real heart of core) and adding a bunch of stuff to it.
	This gives the rest of our app a single 'global' utility to use.
*/
m.define('core', ['core.base', 'core.alert', 'core.constants', 'core.custom'], function(base, alert, constants, custom) {
	
	base.alert = alert // stick the alert component on there
	base = base.extend(base, constants) // stick the constants in there
	base = base.extend(base, custom) // put any platform-customized stuff on there
	
	/*
		We've only got a few things on here right now.
		Core would also contain:
			-popups -- probably including support for nested popups
			-the disablePage() and enablePage() functions
			-directives -- globally accessible chunks of HTML (in the form of mithril virtual elements)
			-partials -- globally accessible partials (beefed-up directives)
	*/
	
	return base
})
