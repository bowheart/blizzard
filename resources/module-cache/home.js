m.define('home', 'core', function(core) {
	return {
		view: function() {
			return ('', [
				m('.container-7', [
					m('.row', [
						m('.col-3-9.pad-row-4.pad-col-2', [
							m('.box', [
								m('h5', 'Salem, UT Village'),
								m('p', '(Support Group)')
							])
						]),
						m('.col-3-6.pad-row-4.pad-col-2', [
							m('.box', [
								m('h5', 'Events & Guest Presenters')
							])
						]),
						m('.col-3-9.pad-row-4.pad-col-2', [
							m('.box', [
								m('h5', 'Zoom Village'),
								m('p', '(Online Support Group)')
							])
						])
					]),
					m('.pad-row-4.text-center', [
						m('button.btn-1', 'Calendar of Events & Gatherings')
					])
				]),
				m('.pad-row-4.home-bottom', [
					m('.container-4', [
						m('h4', 'Mission & Values'),
						m('p', 'We offer peer support and evidence-based information so that moms don\'t have to feel alone.'),
						m('ul.pad-row-6.pad-left-4', [
							m('li', 'E = Expect confidentiality'),
							m('li', 'M = Motherhood & family are enhanced by support'),
							m('li', 'I = Information is evidence-based'),
							m('li', 'L = Listening & unserstanding are the foundation of healing'),
							m('li', 'Y = You are not alone! There is hope!')
						])
					])
				])
			])
		}
	}
})
