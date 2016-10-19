m.define('info.tips', 'info.info-nav', function(nav) {
	return {
		view: function() {
			return m('', [
				m(nav),
				m('', 'The tips page!')
			])
		}
	}
})
