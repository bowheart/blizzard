m.define('layout.header', 'core', function(core) {
	return {
		view: function() {
			return m('header', [
				m('h1', 'Village WITH'),
				m('p.tagline', '"Women Investing Together in Healing"')
			])
		}
	}
})
