function newWindow(URL,scroll,x,y) {
	day = new Date();
	id = day.getTime();
	eval("page" + id + " = window.open(URL, '" + id + "', 'toolbar=0,scrollbars=' + scroll + ',location=0,statusbar=0,menubar=0,resizable=1,width=' + x + ',height=' + y);");
}
