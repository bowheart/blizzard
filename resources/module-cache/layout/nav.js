m.define('layout.nav', 'core', function(core) {
	return {
		controller: function(links) {
			
		},
		view: function(ctrl, links) {
			return m('nav.bg-1.text-center', [
				m('ul.list-inline',
					links.map(function(link) {
						return m('li', [
							m('a', {config: m.route, href: '/' + link.route}, link.text)
						])
					})
				)
			])
		}
	}
})
