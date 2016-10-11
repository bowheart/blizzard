m.define('info.network', 'info.info-nav', function(nav) {
	return {
		view: function() {
			return m('', [
				m(nav),
				m('', 'The network page!')
			])
		}
	}
})
