/*
	Mithril-x is just an add-on to mithril.js
*/

// Create a closure -- a self-calling function like so: (function() {...})() -- so that we're not sticking stuff on the global object.
(function() {
	// modules -- a private object used by our m.define() function (below)
	var modules = {
		
		// add() -- A new module was defined! If it's deps are already met, load it, otherwise add it to the list (this.toLoad)
		add: function(module) {
			if (this.check(module)) return
			this.toLoad.push(module)
		},
		
		// check(module) -- Sees if this module's deps are met. If so, loads it.
		check: function(module, index) {
			for (var j = 0; j < module.deps.length; j++) {
				var dep = module.deps[j]
				if (!this.loaded[dep]) return false // we found an unloaded dep; this module isn't ready
			}
			if (typeof index !== 'undefined') this.toLoad.splice(index, 1)
			this.load(module)
			return true
		},
		
		// digest() -- Loops through all modules toLoad and sees if their deps are met. If so, loads them.
		digest: function() {
			for (var i = 0; i < this.toLoad.length; i++) {
				var module = this.toLoad[i]
				if (this.check(module, i)) return
			}
		},
		
		// load() -- Compiles the list of deps of 'module' and passes them into the module definition function.
		// Also moves this module from 'toLoad' to 'loaded' so other modules that require this one can know that it's loaded.
		load: function(module) {
			var deps = module.deps.map(function(dep) {
				return this.loaded[dep]
			}.bind(this))
			this.loaded[module.name] = module.defFunc.apply(module.defFunc, deps) || {}
			this.digest() // now that this module's loaded, digest to see if this module resolves any other module's last dep
		},
		loaded: {}, // the list of defined modules
		toLoad: [] // the list of modules whose dependencies are not yet met
	}
	
	
	
	/*
		m.define() -- A utility for organized, encapsulated module definition and dependency injection.
		
		Parameters:
			name -- required -- string -- The name of this module.
			deps -- optional -- string | array -- The module or list of modules required by this module.
				Each of these must be the name of another module defined somewhere.
				If any dep in this list is not defined in the app, the app will 'hang' (see below).
				These will be 'injected' into the defFunc.
			defFunc -- required -- function -- The function that will be called to define this module.
				The parameters of this function will be this module's dependencies, in the order they were listed.
				Whatever is 'return'ed by this function will be injected into any modules that require this one.
	*/
	
	// Expose define() to the global 'm' object created by mithril.
	m.define = function(name, deps, defFunc) {
		// arg swapping -- deps is optional; if 'defFunc' doesn't exist, move it down
		if (!defFunc) {
			defFunc = deps
			deps = []
		}
		if (typeof deps === 'string') deps = [deps] // if 'deps' is a string, make it the only dependency
		modules.add({name: name, deps: deps, defFunc: defFunc})
	}
	
	/*
		m.hangInfo()
		This can be helpful when you suspect the app is hanging.
		It will tell you the names of all the modules the app is waiting on.
		Look through them and see if any shouldn't be there.
	*/
	
	// Expose hangInfo() to the global 'm' object created by mithril.
	m.hangInfo = function() {
		return 'Waiting for modules: ' + modules.toLoad.map(function(module) {
			return module.deps
		}).reduce(function(mem, nextDeps) {
			
			for (var i = 0; i < nextDeps.length; i++) {
				var nextDep = nextDeps[i]
				
				if (~!mem.indexOf(nextDep)) mem.push(nextDep)
			}
			
			return mem
			
		}, []).join(', ')
	}
})()
