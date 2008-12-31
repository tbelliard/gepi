Event.observe(window, 'load', initPage);

function initPage () {
	getWinDernieresNotices().setAjaxContent('ajax_affichage_dernieres_notices.php');
	getWinCalendar();
}


function include(filename)
{
	var head = document.getElementsByTagName('head')[0];
	//alert('test : ' + head);
	
	script = document.createElement('script');
	script.src = filename;
	script.type = 'text/javascript';
	
	head.appendChild(script);
}

include('./webtoolkit.aim.js');
include('./ajax_functions.js');
include('./nicEdit/nicEdit.js');
include('./calendar/calendar.js');
include('./calendar/lang/calendar-fr.js');
include('./calendar/calendar-setup.js');
include('../lib/clock_fr.js');
include('../edt_effets/javascripts/window.js');

function getWinListeNotices() {
	if (typeof winListeNotices=="undefined") {
		winListeNotices = new Window(
				{id: 'win_liste_notices',
				title: 'Liste des Notices',
				showEffect: Element.show,
				top:80, 
				left:0,
				width:330,
				height:GetHeight() - 140}
			);
		$('win_liste_notices_content').setStyle({	
			backgroundColor: '#d0d0d0',
			fontSize: '12px',
			color: '#000000'
		});
	}
	winListeNotices.show();
	winListeNotices.toFront();
	return winListeNotices;
}

function getWinEditionNotice() {
	if (typeof winEditionNotice=="undefined") {
		winEditionNotice = new Window(
				{id: 'win_edition_notice',
				title: 'Edition de Notice',
				showEffect: Element.show,
				top:180, 
				left:340,
				width:GetWidth()-400,
				height:GetHeight() - 240}
			);
		$('win_edition_notice_content').setStyle({	
			backgroundColor: '#d0d0d0',
			fontSize: '14px',
			color: '#000000'
		});

	}
	winEditionNotice.show();
	winEditionNotice.toFront();
	return winEditionNotice;
}

function getWinDernieresNotices() {
	if (typeof winDernieresNotices=="undefined") {
		winDernieresNotices = new Window(
				{id: 'win_dernieres_notices',
				title: 'Dernières Notices',
				showEffect: Element.show,
				hideEffect: Element.hide,
				top:130, 
				left:200,
				width:GetWidth()-320,
				height:GetHeight() - 240}
			);
		$('win_dernieres_notices_content').setStyle({	
			backgroundColor: '#d0d0d0',
			fontSize: '14px',
			color: '#000000'
		});
	}
	winDernieresNotices.show();
	winDernieresNotices.toFront();
	return winDernieresNotices;
}

function getWinCalendar() {
	if (typeof winCalendar=="undefined") {
		winCalendar = new Window(
				{id: 'win_calendar',
				title: 'Calendrier',
				showEffect: Element.show,
				top:20, 
				right:40,
				width:270,
				height:210}
			);
		$('win_calendar_content').setStyle({	
			backgroundColor: '#d0d0d0',
			color: '#000000',
		});
		$('win_calendar_content').innerHTML = '<table><tr><td><div id="calendar-container-2"></div></td></tr></table>';
		calendarInstanciation = Calendar.setup(
				{
					flat         : "calendar-container-2", // ID of the parent element
					flatCallback : dateChanged,          // our callback function
					daFormat     : "%s"    			   //date format
				}
			);
	}
	winCalendar.show();
	winCalendar.toFront();
	return winCalendar;
}

function GetWidth()
{
        var x = 0;
        if (self.innerHeight)
        {
                x = self.innerWidth;
        }
        else if (document.documentElement && document.documentElement.clientHeight)
        {
                x = document.documentElement.clientWidth;
        }
        else if (document.body)
        {
                x = document.body.clientWidth;
        }
        return x;
}

function GetHeight()
{
        var y = 0;
        if (self.innerHeight)
        {
                y = self.innerHeight;
        }
        else if (document.documentElement && document.documentElement.clientHeight)
        {
                y = document.documentElement.clientHeight;
        }
        else if (document.body)
        {
                y = document.body.clientHeight;
        }
        return y;
}