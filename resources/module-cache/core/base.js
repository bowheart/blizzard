/*
	base -- a utility module living in the 'core' namespace.
	
	This is the heart of core.
	This utility module should never need to be included directly except by other core submodules.
	This is separate from 'core' itself to allow other core submodules to use this utility (avoiding circular dependencies).
*/
m.define('core.base', 'core.custom', function(custom) {
	
	/*
		core.clone() -- Deep clone an object or array
	*/
	var clone = function(thing) {
		return Array.isArray(thing) ? cloneArr(thing) : typeof thing === 'object' ? cloneObj(thing) : thing
	}
	// recursively clone objects
	var cloneObj = function(obj) {
		var targetObj = {},
			keys = Object.keys(obj)
		
		for (var i = 0; i < keys.length; i++) {
			var key = keys[i],
				val = obj[key]
			
			targetObj[key] = clone(val)
		}
		return targetObj
	}
	// recursively clone arrays
	var cloneArr = function(arr) {
		var targetArr = []
		
		for (var i = 0; i < arr.length; i++) {
			var val = arr[i]
			targetArr.push(val)
		}
		return targetArr
	}
	
	
	/*
		core.extend() -- For single-layer object extension.
		Puts all of the properties of obj2 into obj1, overriding any duplicates.
		If we need nested (recursive) extension, we can add it.
	*/
	var extend = function(obj1, obj2) {
		var keys = Object.keys(obj2)
		
		for (var i = 0; i < keys.length; i++) {
			var key = keys[i]
			obj1[key] = obj2[key]
		}
		return obj1
	}
	
	
	/*
		core.factory() -- Adds basic functionality to basic array-storage factories.
		Use this if your factory is storing a single array.
		
		Params:
			storedArray [array] -- required -- The private array (data store) of this factory.
			factory [object] -- required -- The object this factory exposes to dependants.
	*/
	var factory = function(storedArray, factory) {
		if (!Array.isArray(storedArray)) throw new TypeError('core.factory() error: first parameter must be an array')
		if (typeof factory !== 'object') throw new TypeError('core.factory() error: second parameter must be the factory object')
		
		// Provide direct access to some of the array's native functions (these are non-mutator [accessor] functions, so the data is still safe).
		factory.each = storedArray.forEach.bind(storedArray)
		factory.filter = storedArray.filter.bind(storedArray)
		factory.map = storedArray.map.bind(storedArray)
		factory.reduce = storedArray.reduce.bind(storedArray)
		
		// provide a getter for the length of the array.
		factory.total = function() {
			return storedArray.length
		}
		return factory
	}
	
	
	/*
		core.model() -- An easy interface for declaring JavaScript 'classes' with inheritance
		
		Params:
			parent [object|function] -- optional -- The prototype, or function whose prototype this model will inherit.
			model [object] -- The class definition. If the object has an 'init()' property (optional), it will become the class constructor.
	*/
	var model = function(parent, model) {
		// Arg swapping -- 'parent' is optional; if 'model' doesn't exist, move it down.
		if (!model) {
			model = parent
			parent = custom.ModelBase // it's fine if custom.ModelBase is undefined
		}
		
		// Make sure the model param is valid.
		if (typeof model !== 'object') throw new TypeError('core.model() error: model must be an object')
		
		// Make sure the parent param is valid, if it was given.
		if (parent && !~['function', 'object'].indexOf(typeof parent)) {
			throw new TypeError('core.model() error: parent must be a constructor function or an object')
		}
		
		// Use the init() property of the model as the constructor (if it exists)
		var constructor = model.init || new Function()
		
		// Make the rest of the model the prototype
		constructor.prototype = model
		
		// Implement the inheritance (if user gave a parent model)
		if (parent) Object.setPrototypeOf(model, typeof parent === 'function' ? parent.prototype : parent)
		
		return constructor
	}
	
	
	/*
		core.redraw() -- Use this every time you change a value synchronously behind the scenes and want your views to reflect your changes.
		For asynchronous requests (not including with m.request()) where you don't want your views to redraw until the request completes,
		you should call m.startComputation() and m.endComputation() manually -- 'start' before the request and 'end' once it completes.
	*/
	var redraw = function() {
		m.startComputation()
		m.endComputation()
	}
	
	
	/*
		core.retain() -- A mithril config function. Use this to keep an element from (potentially) being redrawn every cycle.
		
		Example:
			m('.my-div', {config: core.retain}, 'I shall not be redrawn! (unless my parent view is removed from the page, or my tag name changes)')
	*/
	var retain = function(el, isInit, context) {
		context.retain = true
	}
	
	
	// Expose this object to dependants.
	return {
		capitalize: function(str) {
			return str[0].toUpperCase() + str.slice(1)
		},
		clone: clone,
		extend: extend,
		factory: factory,
		model: model,
		redraw: redraw,
		retain: retain
	}
})
