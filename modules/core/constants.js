/*
	A config module
	
	This just defines a bunch of constants that our whole app should use.
*/
m.define('core.constants', function() {
	const constants = Object.create(null, {
		'DAY_FORMAT': {
			enumerable: true,
			value: 'MMM D, YYYY'
		},
		'TIME_FORMAT': {
			enumerable: true,
			value: 'h:mm:ssa'
		}
	})
	
	return constants
})
