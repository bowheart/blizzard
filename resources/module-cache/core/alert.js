/*
	alert -- A component living in the 'core' namespace.
	A component is a mithril thing -- an object with a view() and optional controller() function
	
	This component also provides an interface for working with the underlying alerts.
	This is the only module you need to include when working with alerts.
	(Core itself actually provides access to this module, so outside of core's submodules, all you need is to require 'core', and use core.alert)
*/
m.define('core.alert', ['core.base', 'core.alert-factory'], function(core, factory) {
	return {
		new: factory.add.bind(factory), // factory.add() is the only alert-factory function we'll expose to dependants.
		
		/*
			Here's our first view! Some things to note:
			We put as little logic as possible in the view itself.
			All the view does is piece together markup. It shouldn't declare any functions, except anonymous functions to pass to an Array.map() function.
			All event handlers are off-loaded to a view-model or really anything but the view. (In this example, the onclick event handler lives in the factory)
			
			Why do views need to be light-weight?
			
			Mithril calls every view() function on every mounted component on every redraw to check for any differences.
			
			
			Where should I put stuff?
			
			Off-load everything that doesn't need to run when the view markup is generated to view-models (more on those later), models, or factories.
			"Everything that doesn't need to run" means event handlers and mithril config functions.
		*/
		view: function() {
			
			// Use the exposed factory.map() function to iterate over all the alerts contained in the factory and return a modified version of each.
			return m('.alerts', factory.map(function(alert) {
				
				// Put the look of the alert together.
				var alertContent = [
					m('p', alert.text()),
					m('a.close[data-dismiss="alert"]', {onclick: factory.remove.bind(factory, alert.id())}, m.trust('&times;'))
				]
				// Put the title on there, if it exists.
				if (alert.title()) alertContent.unshift(m('h4', alert.title()))
				
				return m('.alert.alert-' + alert.type(), {key: alert.id()}, alertContent)
			}))
		}
	}
})



/*
	alert-factory -- A factory living in the 'core' namespace.
	A factory is a beefed-up data-store.
	This factory stores the 'alerts' array and provides public add/remove functions plus those added by core.factory()
	Note that this factory lives in the 'core' namespace, parallel with its component ('core.alert'), not in a sub-namespace (like 'core.alert.alert-factory')
	
	This factory shouldn't ever need to be included directly. Instead, use the alert interface (above)
*/
m.define('core.alert-factory', ['core.base', 'core.Alert'], function(core, Alert) {
	var alerts = [], // this factory's private data store
		idCounter = 0 // we're going to use this to tag and keep track of all the alerts housed in this factory
	
	/*
		This is core.factory() in action! Check its documentation in core/base.
	*/
	return core.factory(alerts, {
		add: function(type, title, text) {
			// Create the new alert
			var newAlert = new Alert(type, title, text)
			
			// Keep track of this alert -- give it a unique id
			newAlert.id = m.prop(idCounter++)
			
			// Add it to our data store
			alerts.push(newAlert)
			
			// Set a timeout to remove this alert if the user doesn't close it.
			setTimeout(function() {
				this.remove(newAlert.id())
				core.redraw()
			}.bind(this), 5000)
			
			return newAlert
		},
		remove: function(id) {
			// find the alert in the alerts array
			var alertIndex = alerts.findIndex(function(alert) {
				return alert.id() === id
			})
			
			// if the alert doesn't exist (either the user has the id wrong, or it has already been removed), do nothing
			if (alertIndex === -1) return
			
			// remove the alert
			alerts.splice(alertIndex, 1)
		}
	})
})



/*
	Alert -- A model living in the 'core' namespace.
	Note the PascalCase.
	Note that this model lives in the 'core' namespace, parallel with its factory ('core.alert-factory'), not in a sub-namespace (like 'core.alert.Alert')
	
	This model shouldn't ever need to be included directly, instead, use the alert interface (above)
*/
m.define('core.Alert', 'core.base', function(core) {
	return core.model({
		
		// init() -- the constructor of core models.
		init: function(type, title, text) {
			// arg swapping -- 'title' is optional; if 'text' doesn't exist, move it down
			if (!text) {
				text = title
				title = undefined
			}
			this.type = m.prop(type) // define a getter/setter function for this property
			this.title = m.prop(title) // define a getter/setter function for this property
			this.text = m.prop(text) // define a getter/setter function for this property
		}
	})
})
