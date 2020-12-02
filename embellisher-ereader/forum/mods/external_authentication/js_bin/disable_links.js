anchors = document.getElementsByTagName("a");
for (i in anchors) {
	if ((anchors[i].href) && (anchors[i].href.match(phorum_register_url) || anchors[i].href.match("register,")) && disable_phorum_registration) {
		if (anchors[i].parentNode.className == "information") {
			anchors[i].parentNode.style.display = "none";
			anchors[i].parentNode.style.visibility = "hidden";
		}
		anchors[i].style.display = "none";
		anchors[i].style.visibility = "hidden";
		
	} else if ((anchors[i].href) && (anchors[i].href.match(phorum_logout_url) || anchors[i].href.match("login,")) && disable_phorum_logout && anchors[i].href.match("logout")) {
		anchors[i].style.display = "none";
		anchors[i].style.visibility = "hidden";
		
	} else if ((anchors[i].href) && (anchors[i].href.match(phorum_login_url) || anchors[i].href.match("login,")) && disable_phorum_login && !anchors[i].href.match("logout")) {
		anchors[i].style.display = "none";
		anchors[i].style.visibility = "hidden";
		
	}
	
}
