m.define('info.psi', 'info.info-nav', function(nav) {
	return {
		view: function() {
			return m('', [
				m(nav),
				m('', 'The psi page!')
			])
		}
	}
})
