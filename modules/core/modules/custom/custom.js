/*
	custom -- A utility module living in the 'core' namespace.
*/
m.define('core.custom', function() {
	/*
		core.ModelBase -- Define some functionality that all models in our app should have natively.
	*/
	var ModelBase = {
		_isModel: true,
		
		/*
			ModelBase.serialize() -- Prep the data of a model for saving to a database.
		*/
		serialize: function() {
			var serialized = {},
				keys = Object.keys(this)
			
			for (var i = 0; i < keys.length; i++) {
				var key = keys[i],
					val = this[key]
				
				// Ignore the 'vm' property, as it only represents view state and shouldn't be saved to a database.
				if (key === 'vm') continue
				
				if (typeof val === 'function' && val.name === 'prop' && typeof val.toJSON === 'function') {
					val = val() // It's an m.prop() getter/setter function. Dereference the value.
				}
				
				// Skip other functions and undefined values.
				if (~['function', 'undefined'].indexOf(typeof val)) continue
				
				// Recurse if 'val' is an object.
				if (val && typeof val === 'object') {
					serialized[key] = val._isAMomentObject ? +val : this.serializeObj(val)
					continue
				}
				
				serialized[key] = val
			}
			return serialized
		}
	}
	
	
	// Expose this object to dependants.
	return {
		ModelBase: ModelBase
	}
})
