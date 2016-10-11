m.define('app', ['core', 'layout', 'home', 'info', 'not-found'], function(core, layout, home, info, notFound) {
	var links = [
		{text: 'Home', route: 'home'},
		{text: 'Info for Moms', route: 'info'}
	]
	
	var app = {
		view: function() {
			var route = m.route() && m.route().slice(1) || 'home'
			
			return [
				m(layout.header),
				m(layout.nav, links),
				m('main#page', {class: route.replace('/', ' '), config: core.retain}),
				m(layout.footer)
			]
		}
	}
	
	m.route.mode = 'hash'
	m.mount(document.body, app)
	m.route(document.getElementById('page'), '/', {
		'/': home,
		'/home': home,
		'/info': info,
		'/info/tips': info.tips,
		'/info/network': info.network,
		'/info/help': info.help,
		'/info/psi': info.psi,
		'/:other': notFound
	})
})
