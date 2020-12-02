define(['jquery', 'module', 'PackageParser', 'workers/WorkerProxy', 'storage/StorageManager', 'i18n/Strings', 'backend'], function ($, module, PackageParser, WorkerProxy, StorageManager, Strings, Backend) {

	var config = module.config();

	// Changes XML to JSON
    function xmlToJson(xml) {
        // Create the return object
        var obj = {};

        if (xml.nodeType == 1) { // element
            // do attributes
            if (xml.attributes.length > 0) {
            obj["@attributes"] = {};
                for (var j = 0; j < xml.attributes.length; j++) {
                    var attribute = xml.attributes.item(j);
                    obj["@attributes"][attribute.nodeName] = attribute.nodeValue;
                }
            }
        } else if (xml.nodeType == 3) { // text
            obj = xml.nodeValue;
        }

        // do children
        if (xml.hasChildNodes()) {
            for(var i = 0; i < xml.childNodes.length; i++) {
                var item = xml.childNodes.item(i);
                var nodeName = item.nodeName;
                if (typeof(obj[nodeName]) == "undefined") {
                    obj[nodeName] = xmlToJson(item);
                } else {
                    if (typeof(obj[nodeName].push) == "undefined") {
                        var old = obj[nodeName];
                        obj[nodeName] = [];
                        obj[nodeName].push(old);
                    }
                    obj[nodeName].push(xmlToJson(item));
                }
            }
        }

        return obj;
    };

    function loadXMLDoc(filename)
    {
        if (window.XMLHttpRequest)
            xhttp=new XMLHttpRequest();
        else // code for IE5 and IE6
            xhttp=new ActiveXObject("Microsoft.XMLHTTP");
        console.log(filename)
        xhttp.open("GET",filename,false);
        xhttp.send();
        
        return xhttp.responseXML;
    }

    function loadXMLString(text)
    {
        var XMLDoc;
        if (window.DOMParser)
        {
            var parser=new DOMParser();
            xmlDoc=parser.parseFromString(text,"text/xml");
        }
        else // Internet Explorer
        {
            xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
            xmlDoc.async=false;
            xmlDoc.loadXML(text); 
        }

        return xmlDoc;
    }
	
	

	var LibraryManager = function(){ 
	};

	LibraryManager.prototype = {
	   _getFullUrl : function(packageUrl, relativeUrl){
            if (!relativeUrl){
                return null;
            }

            var parts = packageUrl.split('/');
            parts.pop();
            
            var root = parts.join('/');

            return root + (relativeUrl.charAt(0) == '/' ? '' : '/') + relativeUrl
        },

        cleanXMLLibraryJSON : function(messyJSON) {
            cleanJSON = [];
            count = 0;
            
            for (var epub in messyJSON.epublibrary.epubitem) {
                cleanJSON.push(messyJSON.epublibrary.epubitem[epub]);
                delete cleanJSON[count]["#text"];

                for(epubinfo in cleanJSON[count]) {
                    actualValue = cleanJSON[count][epubinfo]["#text"];
                    cleanJSON[count][epubinfo] = actualValue;
                }
                
                count++;
            }

            return cleanJSON;
        },
		
		retrieveStoreEpubs : function(success, searchterm){
        	console.log("in get store data");
			if (!searchterm){
				searchterm = "";
			}
            /*if (this.storeData){
				
                success(this.storeData);
                return;
            }*/ //always get store to be up to date
            var self = this;
			
			var backenddata = new Backend();
			if (backenddata.userInfo.loggedin==1) {
				console.log(backenddata.mainurl + backenddata.getstore);
				$.ajax({
					url : backenddata.mainurl + backenddata.getstore,
					type : "POST",
					dataType : "json",
					cache : false,
					data : {
						email : backenddata.userInfo.email,
						sessionid : backenddata.userInfo.sessionid,
						search : searchterm
					},
					success : function (json) {
						if (json['error'] == "LOGIN"){
							//call the function it will handle the error
							success(json);
						}else{
							self.storeData = json;
							success(json,searchterm);
						}
					}
				});
			}else{
                //TODO show login screen.
				
                success([]);
                
			}
			
		},
        
        retrieveAvailableEpubs : function(success, error, force_reload){
        	var indexUrl = StorageManager.getPathUrl('/epub_content/epub_library.json');
            
			if (!force_reload){
				if (this.libraryData){
					success(this.libraryData);
					return;
				}
			}
            var self = this;
			
			var backenddata = new Backend();
			if (backenddata.userInfo.loggedin==1) {
				$.ajax({
					url : backenddata.mainurl + backenddata.getlibrary,
					type : "POST",
					dataType : "json",
					cache : false,
					data : {
						email : backenddata.userInfo.email,
						sessionid : backenddata.userInfo.sessionid
					},
					success : function (json) {
						if (json['error'] == "LOGIN"){
							//call the function it will handle the error
							success(json);
						}else{
							self.libraryData = json;
							success(json);
						}
					}
				});
			}else{


                $.getJSON('epub_content/epub_library.json', function(json) {
                    //epubLibraryJSON = self.cleanXMLLibraryJSON(json);
                    console.log("Not logged in, show samples")
                    self.libraryData = json;
                    success(json);
                });
			}
			
		},
        
        deleteEpubWithId : function(id, success, error){
            WorkerProxy.deleteEpub(id, this.libraryData, {
                success: this._refreshLibraryFromWorker.bind(this, success),
                error: error
            });
        },
		retrieveFullEpubDetails : function(packageUrl, bookid, rootUrl, rootDir, noCoverBackground, success, error){
            var self = this;
			$.get(packageUrl, function(data){
                
                if(typeof(data) === "string" ) {
                    var parser = new window.DOMParser;
                    data = parser.parseFromString(data, 'text/xml');
                }
                var jsonObj = PackageParser.parsePackageDom(data, packageUrl);
                jsonObj.coverHref = self._getFullUrl(packageUrl, jsonObj.coverHref);
                jsonObj.packageUrl = packageUrl;
                jsonObj.rootDir = rootDir;
                jsonObj.rootUrl = rootUrl;
				jsonObj.bookid = bookid;
                jsonObj.noCoverBackground = noCoverBackground;
                success(jsonObj);
				
			}).fail(error);
		},
        _refreshLibraryFromWorker : function(callback, newLibraryData){
            this.libraryData = newLibraryData;
            callback();
        },
        handleZippedEpub : function(options){
            WorkerProxy.importZip(options.file, this.libraryData, {
                progress : options.progress,
                overwrite: options.overwrite,
                success: this._refreshLibraryFromWorker.bind(this, options.success),
                error : options.error
            });
            //Dialogs.showModalProgress()
            //unzipper.extractAll();
        },
        handleDirectoryImport : function(options){

            var rawFiles = options.files, 
                files = {};
            for (var i = 0; i < rawFiles.length; i++){
                 var path = rawFiles[i].webkitRelativePath
                // don't capture paths that contain . at the beginning of a file or dir. 
                // These are hidden files. I don't think chrome will ever reference 
                // a file using double dot "/.." so this should be safe
                if (path.indexOf('/.') != -1){
                    continue;
                }
                var parts = path.split('/');

                parts.shift();
                var shiftPath = parts.join('/');

                files[shiftPath] = rawFiles[i];
            }

            WorkerProxy.importDirectory(files, this.libraryData, {
                progress : options.progress,
                overwrite: options.overwrite,
                success: this._refreshLibraryFromWorker.bind(this, options.success),
                error : options.error
            });
        },
        handleUrlImport : function(options){
            WorkerProxy.importUrl(options.url, this.libraryData, {
                progress : options.progress,
                overwrite: options.overwrite,
                success: this._refreshLibraryFromWorker.bind(this, options.success),
                error : options.error

            });
        },
        handleMigration : function(options){
            WorkerProxy.migrateOldBooks({
                progress : options.progress,
                success: this._refreshLibraryFromWorker.bind(this, options.success),
                error : options.error
            });
        },
        handleUrl : function(options){

        },
        canHandleUrl : function(){
            return config.canHandleUrl;
        },
        canHandleDirectory : function(){
            return config.canHandleDirectory;
        }
	}

    window.cleanEntireLibrary = function(){
        StorageManager.deleteFile('/', function(){
            console.log('done');
        }, console.error);
    }
	return new LibraryManager();

});