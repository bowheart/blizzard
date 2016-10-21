m.define('home', 'core', function(core) {
	return {
		view: function() {
			return ('', [
				m('.container-6.pad-row-4', [
					m('.row.row-flex.text-center', [
						m('.col-3-8.pad-col-2', [
							m('.border.pad-2', [
								m('h5', 'Salem, UT Village'),
								m('p.text-left', '(Support Group)')
							])
						]),
						m('.col-3-7.pad-col-2', [
							m('.border.pad-2', [
								m('h5', 'Events & Guest Presenters'),
								m('p.text-left', '')
							])
						]),
						m('.col-3-8.pad-col-2', [
							m('.border.pad-2', [
								m('h5', 'Zoom Village'),
								m('p.text-left', '(Online Support Group)')
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
