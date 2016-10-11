m.define('layout.header', 'core', function(core) {
	return {
		view: function() {
			return m('header.v-align-content', [
				m('.wrapper', [
					m('h1', 'Village WITH'),
					m('p.tagline', '"Women Investing Together in Healing"')
				])
			])
		}
	}
})
