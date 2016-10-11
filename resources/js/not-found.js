m.define('not-found', function() {
	return {
		view: function() {
			return m('.container-3', [
				m('h3', 'Not Found'),
				m('p', 'Sorry, it looks like that page doesn\'t exist.')
			])
		}
	}
})
