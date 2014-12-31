chrome.runtime.onInstalled.addListener(function() {
	localStorage.setItem('PoliticallyMail', true);
});

chrome.extension.onRequest.addListener(function(request, sender, sendResponse) {
	if (request['GetDict']) {
		var lastDict = localStorage.getItem('lastDictDate');
		var today = new Date();
		var lastWeek = new Date(today.getTime()-3600000); // 1000*60*60
		if (!lastDict || lastDict < lastWeek) {
			localStorage.setItem('lastDictDate', today);
			try {
				var xhr = new XMLHttpRequest();
				xhr.open("GET", "https://your_DNS_or_IP_here/PoliticallyMail/Files/dict.txt?t="+Math.random(), true);
//				xhr.setRequestHeader("Content-type", "application/json; charset=utf-8");
				xhr.timeout = 25000;
				xhr.ontimeout = function () {
					localStorage.setItem('dict', '');
					sendResponse({'dict': ''});
				};
				xhr.onreadystatechange = function() {
					var dict;
					if (xhr.readyState == 4) {
						if (xhr.status == 200) {
							dict = xhr.responseText;
							if (dict.length < 5000000) {
								localStorage.setItem('dict', dict);
							}
						} else {
							dict = localStorage.getItem('dict');
						}
						if (dict) {
							sendResponse({'dict': dict});
						}
					}
				};
				xhr.send();
			} catch (e) {
				console.log(e.message);
			}
		} else {
			sendResponse({'dict': localStorage.getItem('dict')});
		}
		return false;
	}
	if (request['SetPoliticallyMail'] && request['PoliticallyMail'] !== null) {
		localStorage.setItem('PoliticallyMail', request['PoliticallyMail']);
	}
	sendResponse({'PoliticallyMail': (localStorage.getItem('PoliticallyMail') == "false" ? false : true)});
});