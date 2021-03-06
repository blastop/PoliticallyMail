var PoliticallyMailExt = function() {
	var that = this;

	this.go = function(is_active) {
		if (is_active === true) {
			this.body = document.getElementsByTagName('body')[0];
			this.regs = [];
			this.dictionary = [];
			this.dynamicTags = ['SCRIPT', 'STYLE', 'NOSCRIPT', 'IFRAME', 'OBJECT', 'EMBED', 'PARAM', 'A'];
			this.dynamicTagsLength = that.dynamicTags.length;
			this.words_count = 0;
//			this.whitespace = "((\\s\\s)|\\s|[^a-zא-תа-я]|ה|^|$)+";
//			this.whitespace = "((\\s|ה)+|^|$)";
			this.whitespace = "([^a-zא-תа-я\\~]+|^|$)";

			this.isAllowedTag = function(node) {
				for (var i=0; i<that.dynamicTagsLength; i++) {
					if (that.dynamicTags[i] == node) {
						return false;
					}
				}
				return true;
			};

			this.cleanInmostNodes = function(e) {
				var items = that.getTextNodes(e);
				var item = null;
				var OriginalString = null;
				var PotentialPolitician = null;
				var PotentiallyCleanElements = null;
				var TemporaryPotentialCleanElement = null;
				var CollectingPos = 0;
				var CollectingPart = 0;
				var nodeIndex = 0;

				for (var i = 0, kProgressor = 0, iEnd = items.length; i < iEnd; i++, kProgressor = 0) {
					if (items[i].parentNode && that.isAllowedTag(items[i].parentNode.nodeName)) {
						OriginalString = items[i].nodeValue;
						item = items[i].parentNode;
						nodeIndex = that.getIndex(items[i]);

						for (var k = kProgressor; k < that.words_count; k++) {
							if (that.regs[k].test(OriginalString)) {
								OriginalString = OriginalString.replace(that.whitespace, ' ');
								PotentialPolitician = that.get_indices(k, OriginalString);
								TemporaryPotentialCleanElement = null;
								CollectingPos = 0;
								CollectingPart = 0;
								PotentiallyCleanElements = document.createDocumentFragment();
								for (var p = 0, pEnd = PotentialPolitician.length, CollectingPartLength = that.dictionary[k].name.length; p < pEnd; p++) {
									CollectingPart = PotentialPolitician[p];
									PotentiallyCleanElements.appendChild(document.createTextNode(OriginalString.substr(0, CollectingPart)));
									PotentiallyCleanElements.appendChild(that.CleanPolitician(OriginalString.substr(CollectingPart, CollectingPartLength)));
									CollectingPos += CollectingPart + CollectingPartLength;
								}
								/* append the last part, if such exists */
								PotentiallyCleanElements.appendChild(document.createTextNode(OriginalString.substr(CollectingPos)));
								/* replace that child */
								item.replaceChild(PotentiallyCleanElements, item.childNodes[nodeIndex]);
								items.splice.apply(items, [i, 1].concat(item.childNodes));

								kProgressor = k + 1;
								iEnd += PotentiallyCleanElements.childNodes.length -1;
								i--;
								break;
							}
						}
					}
				}
			};

			this.CleanPolitician = function(GoodIntention) {
				GoodIntention = GoodIntention.toLowerCase();
				var idx = that.getDictionaryIndex(GoodIntention), CleanA;
				//GoodIntention = '~' + GoodIntention + '~';
				if (idx >= 0) {
					CleanA = document.createElement('A');
					CleanA.style.color = '#0000FF';
					CleanA.style.display = 'inline';
					CleanA.style.fontSize = 'inherit';
					CleanA.style.textDecoration = 'underline';
					CleanA.style.cursor = 'pointer';
					CleanA.data = GoodIntention;
					CleanA.setAttribute('href', 'mailto:' + that.dictionary[idx].email);

					var CleanText = document.createTextNode(GoodIntention);

					CleanA.appendChild(CleanText);
				} else {
					CleanA = document.createTextNode(GoodIntention);
				}
				return CleanA;
			};

			this.getDictionaryIndex = function(needleKey) {
				for (var i=0; i<that.words_count; i++) {
					if (that.dictionary[i].name == needleKey) {
						return i;
					}
				}
				return -1;
			};

			this.get_indices = function(needleKey, haystack) {
				var indices = [];
				var needle = that.dictionary[needleKey].name;
				var i=-1;
				haystack = haystack.toLowerCase();
				while(( i = haystack.indexOf(needle, i+1)) >= 0) {
					indices.push(i);
				}
				return indices;
			};

			this.getTextNodes = function(el){
				var cur;
				var ret = [];
				var walker = document.createTreeWalker(el, NodeFilter.SHOW_TEXT, null, false);
				while(cur = walker.nextNode()) {
					ret.push(cur);
				}
				return ret;
			};

			/* get the childNode's index */
			this.getIndex = function(el) {
				var nodeIndex = 0;
				while( (el = el.previousSibling) != null ) {
					nodeIndex++;
				}
				return nodeIndex;
			};

			chrome.extension.sendMessage({'GetDict': true}, function(response) {
				if (response['dict'].length > 0){
					that.dictionary = JSON.parse(response['dict']);
					that.words_count = that.dictionary.length;
					for (var i = 0; i < that.words_count; i++) {
						that.regs.push(new RegExp('('+that.dictionary[i].name.replace(/( |_)/g, that.whitespace)+')', 'ig'));
					}
					//that.regs = new RegExp('('+that.regs.join('|')+')', 'ig');

					that.cleanInmostNodes(that.body);
					that.body.addEventListener(
						'DOMSubtreeModified',
						function(e) {
							that.cleanInmostNodes(e.target);
						},
						false
					);
				}
			});
		}
	};
};

chrome.extension.sendMessage({'GetPoliticallyMail': true}, function(response) {
	var pme = new PoliticallyMailExt();
	pme.go(response['PoliticallyMail']);
});