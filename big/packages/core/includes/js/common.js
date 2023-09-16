function fnExcelReport(table) {
	var tab_text = "<table border='2px'><tr bgcolor='#87AFC6'>";
	var textRange;
	var j = 0;
	tab = document.getElementById(table); // id of table

	for (j = 0; j < tab.rows.length; j++) {
		tab_text = tab_text + tab.rows[j].innerHTML + '</tr>';
		//tab_text=tab_text+"</tr>";
	}

	tab_text = tab_text + '</table>';
	tab_text = tab_text.replace(/<A[^>]*>|<\/A>/g, ''); //remove if u want links in your table
	tab_text = tab_text.replace(/<img[^>]*>/gi, ''); // remove if u want images in your table
	tab_text = tab_text.replace(/<input[^>]*>|<\/input>/gi, ''); // reomves input params

	var ua = window.navigator.userAgent;
	var msie = ua.indexOf('MSIE ');

	if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
		// If Internet Explorer
		txtArea1.document.open('txt/html', 'replace');
		txtArea1.document.write(tab_text);
		txtArea1.document.close();
		txtArea1.focus();
		sa = txtArea1.document.execCommand('SaveAs', true, 'Say Thanks to Sumit.xlsx');
	} //other browser not tested on IE 11
	else sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));

	return sa;
}
function ClickHereToPrint(iframe, target) {
	try {
		var oIframe = document.getElementById(iframe);
		var oContent = document.getElementById(target).innerHTML;
		var oDoc = oIframe.contentWindow || oIframe.contentDocument;
		if (oDoc.document) oDoc = oDoc.document;
		oDoc.write('<head><title>title</title>');
		oDoc.write('</head><body onload="this.focus(); this.print();">');
		oDoc.write(oContent + '</body>');
		oDoc.close();
	} catch (e) {
		self.print();
	}
}
function load_ajax(pram, block) {
	//	jQuery('#module_'+block).append()
	jQuery.ajax({
		method: 'POST',
		url: 'form.php?block_id=' + block,
		data: pram + '&load_ajax=1',
		beforeSend: function () {
			//jQuery('#load').fadeIn(10).animate({opacity: 1.0}, 10);
		},
		success: function (content) {
			//jQuery('#load').fadeOut(1000);
			//jQuery('#loading').hide()
			getId('module_' + block).innerHTML = content;
		},
	});
}
function echo(st) {
	document.write(st);
}
function getId(id) {
	//alternative for $ function
	if (typeof id == 'object') {
		return id;
	}
	return document.getElementById(id);
}

function toggle(id, status) {
	if (getId(id)) {
		if (typeof status != 'undefined') {
			getId(id).style.display = status;
		} else if (getId(id).style.display == 'none') {
			getId(id).style.display = 'block';
		} else {
			getId(id).style.display = 'none';
		}
	}
}
function findPos(obj) {
	var curleft = (curtop = 0);
	if (obj.offsetParent) {
		do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		} while ((obj = obj.offsetParent));
	}
	return [curleft, curtop];
}

function select_all_checkbox(form, name, status, select_color, unselect_color) {
	for (var i = 0; i < form.elements.length; i++) {
		if (form.elements[i].name == 'selected_ids[]') {
			if (status == -1) {
				form.elements[i].checked = !form.elements[i].checked;
			} else {
				form.elements[i].checked = status;
			}
			if (select_color) {
				if (getId(name + '_tr_' + form.elements[i].value)) {
					jQuery('#' + name + '_tr_' + form.elements[i].value).attr(
						'background-color',
						form.elements[i].checked ? select_color : unselect_color
					);
				}
			}
		}
	}
}
function select_checkbox(form, name, checkbox, select_color, unselect_color) {
	tr_color = checkbox.checked ? select_color : unselect_color;
	if (typeof event == 'undefined' || !event.shiftKey) {
		getId(name + '_all_checkbox').lastSelected = checkbox;
		if (select_color && getId(name + '_tr_' + checkbox.value)) {
			jQuery('#' + name + '_tr_' + checkbox.value).attr(
				'background-color',
				checkbox.checked ? select_color : unselect_color
			);
		}
		update_all_checkbox_status(form, name);
		return;
	}
	//select_all_checkbox(form, name, false, select_color, unselect_color);

	var active = typeof getId(name + '_all_checkbox').lastSelected == 'undefined' ? true : false;

	for (var i = 0; i < form.elements.length; i++) {
		if (!active && form.elements[i] == getId(name + '_all_checkbox').lastSelected) {
			active = 1;
		}
		if (!active && form.elements[i] == checkbox) {
			active = 2;
		}
		if (active && form.elements[i].id == name + '_checkbox') {
			form.elements[i].checked = checkbox.checked;
			getId(name + '_tr_' + form.elements[i].value).style.backgroundColor = checkbox.checked
				? select_color
				: unselect_color;
		}
		if (
			(active && form.elements[i] == checkbox && active == 1) ||
			(form.elements[i] == getId(name + '_all_checkbox').lastSelected && active == 2)
		) {
			break;
		}
	}
	update_all_checkbox_status(form, name);
}
function update_all_checkbox_status(form, name) {
	var status = true;
	for (var i = 0; i < form.elements.length; i++) {
		if (form.elements[i].name == 'selected_ids[]' && !form.elements[i].checked) {
			status = false;
			break;
		}
	}
	getId(name + '_all_checkbox').checked = status;
}
function make_date_input(input_name, input_value) {
	echo('<div id="' + input_name + '_div"></div>');
	new Ext.form.DateField({
		name: input_name,
		id: input_name,
		value: input_value,
		renderTo: input_name + '_div',
		format: 'd/m/Y',
	});
}
var ns = navigator.appName.indexOf('Netscape') != -1;
var d = document;
var px = document.layers ? '' : 'px';
function JSFX_FloatDiv(id, sx, sy) {
	var el = d.getElementById ? d.getElementById(id) : d.all ? d.all[id] : d.layers[id];
	window[id + '_obj'] = el;
	if (d.layers) el.style = el;
	el.cx = el.sx = sx;
	el.cy = el.sy = sy;
	el.sP = function (x, y) {
		this.style.left = x + px;
		this.style.top = y + px;
	};
	el.flt = function () {
		var pX, pY;
		pX =
			this.sx >= 0
				? 0
				: ns
				? innerWidth
				: document.documentElement && document.documentElement.clientWidth
				? document.documentElement.clientWidth
				: document.body.clientWidth;
		pY = ns
			? pageYOffset
			: document.documentElement && document.documentElement.scrollTop
			? document.documentElement.scrollTop
			: document.body.scrollTop;
		if (this.sy < 0)
			pY += ns
				? innerHeight
				: document.documentElement && document.documentElement.clientHeight
				? document.documentElement.clientHeight
				: document.body.clientHeight;
		this.cx += (pX + this.sx - this.cx) / 8;
		this.cy += (pY + this.sy - this.cy) / 8;
		this.sP(this.cx, this.cy);
		setTimeout(this.id + '_obj.flt()', 40);
	};
	return el;
}
function numberFormat(nStr) {
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}
function to_numeric(st) {
	st = st + '';
	if (st) {
		return typeof st == 'number' || (typeof st.match != 'undefined' && !st.match(/[^0-9.,-]/))
			? parseFloat(st.replace(/\,/g, ''))
			: st;
	} else {
		return st;
	}
}
/*-------------------------------------------------------------------------*/
function is_numeric(sText) {
	var ValidChars = '0123456789.';
	var isNumeric = true;
	var Char;

	for (i = 0; i < sText.length && isNumeric == true; i++) {
		Char = sText.charAt(i);
		if (ValidChars.indexOf(Char) == -1) {
			isNumeric = false;
		}
	}
	return isNumeric;
}
function stringToNumber(st) {
	st = st + '';
	if (st) {
		return typeof st == 'number' || (typeof st.match != 'undefined' && !st.match(/[^0-9.,-]/))
			? parseFloat(st.replace(/\,/g, ''))
			: st;
	} else {
		return st;
	}
}
function roundNumber(num, dec) {
	var result = Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
	return result;
}
function strPad(str, pad, width) {
	var str = str.toString();
	desStr = str;
	if (str.length < width) {
		for (i = 0; i < width - str.length; i++) {
			desStr = pad + desStr;
		}
	}
	return desStr;
}
function LastDayOfMonth(year, month) {
	month = stringToNumber(month);
	var day;
	switch (month) {
		case 1:
		case 3:
		case 5:
		case 7:
		case 8:
		case 10:
		case 12:
			day = 31;
			break;
		case 4:
		case 6:
		case 9:
		case 11:
			day = 30;
			break;
		case 2:
			if ((year % 4 == 0 && year % 100 != 0) || year % 400 == 0) day = 29;
			else day = 28;
			break;
	}
	return day;
}
function compareDate(fromDate, toDate) {
	var larger = 1;
	var smaller = -1;
	var equal = 0;

	var fromYear = stringToNumber(fromDate.substring(0, 4));
	var fromMonth = stringToNumber(fromDate.substring(5, 7));
	var fromDay = stringToNumber(fromDate.substring(8, 10));

	var toYear = stringToNumber(toDate.substring(0, 4));
	var toMonth = stringToNumber(toDate.substring(5, 7));
	var toDay = stringToNumber(toDate.substring(8, 10));

	if (fromYear < toYear) {
		return smaller;
	} else if (toYear == fromYear) {
		if (fromMonth < toMonth) {
			return smaller;
		} else if (fromMonth == toMonth) {
			if (fromDay < toDay) {
				return smaller;
			} else if (fromDay == toDay) {
				return equal;
			} else {
				return larger;
			}
		} else {
			return larger;
		}
	} else {
		return larger;
	}
}
function start_clock() {
	var thetime = new Date();
	var nhours = thetime.getHours();
	var nmins = thetime.getMinutes();
	var nsecn = thetime.getSeconds();
	var nday = thetime.getDay();
	var nmonth = thetime.getMonth();
	var ntoday = thetime.getDate();
	var nyear = thetime.getYear();
	var AorP = ' ';
	if (nhours >= 12) AorP = 'P.M.';
	else AorP = 'A.M.';
	if (nhours >= 13) nhours -= 12;
	if (nhours == 0) nhours = 12;
	if (nsecn < 10) nsecn = '0' + nsecn;
	if (nmins < 10) nmins = '0' + nmins;
	getId('clockspot').innerHTML = nhours + ': ' + nmins + ': ' + nsecn + ' ' + AorP;
	setTimeout('start_clock()', 1000);
}
function max_height(obj) {
	var arr = Array();
	var max_height = 0;
	var i = 0;
	obj.each(function () {
		arr[i] = parseInt(obj.eq(i).height());
		if (arr[i] > max_height) max_height = arr[i];
		i++;
	});
	return max_height;
}
function printWebPart(printDiv) {
	var divToPrint = document.getElementById(printDiv);
	var newWin = window.open('', 'Print-Window');
	newWin.document.open();
	newWin.document.write(
		'<html><head><style>table { page-break-inside:auto } tr{ page-break-inside:avoid; page-break-after:auto } @media all {.page-break{ display: none; font-size:20px;} @media print {.page-break { display: block; page-break-before: always; }}</style></head><body onload="window.print()">' +
			divToPrint.innerHTML +
			'</body></html>'
	);
	newWin.document.close();
	setTimeout(function () {
		newWin.close();
	}, 10);
}
function printWebPart1(tagid) {
	if (tagid && document.getElementById(tagid)) {
		var newWindow = window.open();
		newWindow.document.write(document.getElementById(tagid).innerHTML);
		newWindow.print();
	}
}
function newPrintWebPart(tagid) {
	if (tagid && document.getElementById(tagid)) {
		window.frames['print_frame'].document.body.innerHTML = document.getElementById(tagid).innerHTML;
		window.frames['print_frame'].window.focus();
		window.frames['print_frame'].window.print();
	}
}
function TextCounter(obj, maxCounter, remainCounterObj) {
	text = obj.value;
	textLen = text.length;
	remain = maxCounter - textLen;
	remainCounterObj.innerHTML = remain;
	if (remain < 0) {
		obj.value = text.substr(0, maxCounter);
		getId('remainCounterId').value = 0;
		alert('You reach the max');
	}
}
function popupCenterDual(url, title, w, h) {
	console.log('test');
	let left = screen.width / 2 - w / 2;
	let top = screen.height / 2 - h / 2;
	return window.open(
		url,
		title,
		'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' +
			w +
			', height=' +
			h +
			', top=' +
			top +
			', left=' +
			left
	);
}
function updateItemLike(obj, itemId, phpSession, blockId) {
	jQuery.ajax({
		method: 'POST',
		url: 'form.php?block_id=' + blockId,
		data: {
			cmd: 'update_like',
			item_id: itemId,
			php_session: phpSession,
		},
		beforeSend: function () {
			//obj.disabled = true;
		},
		success: function (content) {
			if (content) {
				getId('counter_' + itemId).innerHTML = '{' + content + '}';
				obj.style.color = '#AAAAAA';
			}
		},
	});
}
function PreviewImage(fileId, imageId) {
	var oFReader = new FileReader();
	oFReader.readAsDataURL(document.getElementById(fileId).files[0]);
	oFReader.onload = function (oFREvent) {
		document.getElementById(imageId).src = oFREvent.target.result;
	};
}
/**
 * { function_description }
 *
 * @param      {string}  num     The number
 * @return     {string}  { description_of_the_return_value }
 */
Number.prototype.toVNText = function () {
	let maps = { 0: 'không', 1: 'một', 2: 'hai', 3: 'ba', 4: 'bốn', 5: 'năm', 6: 'sáu', 7: 'bảy', 8: 'tám', 9: 'chín' };
	function chuc(e) {
		let n = parseInt(e);

		if (n >= 20) {
			let dv = maps[e[1]];
			if (e[1] == 4) dv = 'tư';
			else if (e[1] == 1) dv = 'mốt';
			else if (e[1] == 5) dv = 'lăm';

			return maps[e[0]] + ' mươi ' + (e[1] > 0 ? dv : '');
		} else if (n >= 10) {
			return 'mười ' + (n == 10 ? '' : maps[e[1]] == 0 ? '' : maps[e[1]]);
		}

		return '';
	}

	function tram(e) {
		return parseInt(e)
			? maps[e[0]] + ' trăm ' + (e[1] == 0 && e[2] != 0 ? 'linh ' + maps[e[2]] : chuc(e.slice(1)))
			: '';
	}
	function dv(e, i) {
		return maps[e];
	}

	return this.toString()
		.split(/(?=(?:\d{3})+(?:\.|$))/g)
		.reverse()
		.map(function (e, i) {
			switch (e.length) {
				case 1:
					return dv(e, i);

				case 2:
					return chuc(e);

				case 3:
					return tram(e);
			}
		})
		.map(function (e, i) {
			return e ? e + ' ' + ['', 'nghìn', 'triệu', 'tỉ'][(i >= 4 ? i + 1 : i) % 4] : '';
		})
		.reverse()
		.join(' ')
		.trim()
		.replace(/\s*không*$/gi, '')
		.trim();
};
