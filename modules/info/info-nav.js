m.define('info.info-nav', function() {
	var links = [
		{text: 'Tips for Wellness', route: 'tips'},
		{text: 'Your Network', route: 'network'},
		{text: 'How to Help', route: 'help'},
		{text: 'PSI Info', route: 'psi'}
	]
	
	return {
		view: function() {
			return m('nav.v-align-content.info-nav', [
				m('ul.list-inline',
					links.map(function(link) {
						return m('li', [
							m('a', {config: m.route, href: '/info/' + link.route}, link.text)
						])
					})
				)
			])
		}
	}
})
