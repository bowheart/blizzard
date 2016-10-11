m.define('info.help', 'info.info-nav', function(nav) {
	return {
		view: function() {
			return m('', [
				m(nav),
				m('', 'The help page!')
			])
		}
	}
})
