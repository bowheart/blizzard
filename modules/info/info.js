m.define('info', ['info.info-nav', 'info.tips', 'info.network', 'info.help', 'info.psi'], function(nav, tips, network, help, psi) {
	return {
		tips: tips,
		network: network,
		help: help,
		psi: psi,
		view: function() {
			return m('', [
				m(tips)
			])
		}
	}
})
