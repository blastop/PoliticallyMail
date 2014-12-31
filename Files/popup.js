(function() {
	var that = this;
    this.ThankYouTimeout = null;
    this.justOpened = true;

	this.HandleKarma = function(Karma) {
		chrome.browserAction.setIcon({path: "img/icon19"+(Karma ? '' : '_off')+".png"}, function() {});
		document.getElementById('active').src = 'img/'+(Karma ? 'On' : 'Off')+'.png';
		if (!(that.justOpened)) {
			chrome.tabs.getSelected(null, function(tab) {
				chrome.tabs.reload(tab.id);
			});
		}
	};

    /*this.suggest = function() {
        try {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "https://your_DNS_or_IP_here/PoliticallyMail/index.php?a=3&w="+document.getElementById('suggest').value, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('suggest').value = '';
                    document.getElementById('suggest').setAttribute('placeholder','Thank You');
					clearTimeout(that.ThankYouTimeout);
					that.ThankYouTimeout = setTimeout(function() {document.getElementById('suggest').setAttribute('placeholder','Suggest');}, 1500);
                }
            }
            xhr.send();
        } catch (e) {
            console.log(e.message);
        }
    };*/

    this.activate = function() {
		that.justOpened = false;

        this.src = this.src.indexOf('Off') > -1
            ? this.src.replace(/Off/, 'On')
            : this.src.replace(/On/, 'Off')
        ;

		chrome.extension.sendRequest(
			{
				'SetPoliticallyMail': true,
				'PoliticallyMail': this.src.indexOf('On') > -1
			},
			function(response) {
				that.HandleKarma(response['PoliticallyMail']);
			}
		);
    };

	/*this.suggest_press = function(e) {
		if (e.which == "13") {
			that.suggest();
		}
	};*/

    this.run = function() {
		chrome.extension.sendRequest(
			{'GetPoliticallyMail': true},
			function(response) {
				that.HandleKarma(response['PoliticallyMail']);

				/*document.getElementById('suggest').addEventListener('keypress', that.suggest_press, false);
				document.getElementById('suggestButton').addEventListener('click', that.suggest, false);*/
				document.getElementById('active').addEventListener('click', that.activate, false);
			}
		);
    };

    document.addEventListener('DOMContentLoaded', that.run, false);
})();